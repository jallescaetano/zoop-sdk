<?php

namespace Church\Resources;

use Church\Contracts\StorageInterface;
use Church\Contracts\WhatsAppSenderInterface;
use Church\Exceptions\ChurchException;
use Church\Exceptions\VisitorNotFoundException;

/**
 * Módulo 2 — Gestão de Visitantes
 *
 * Cadastra visitantes e gerencia um fluxo automático de mensagens de acompanhamento
 * ao longo da semana seguinte à visita.
 *
 * Como funciona o fluxo:
 *   1. Ao cadastrar um visitante, o sistema agenda as mensagens conforme o `follow_up_flow`.
 *   2. Cada item do fluxo define quantas horas após o cadastro a mensagem será enviada.
 *   3. Basta executar `processScheduled()` periodicamente (ex.: cron a cada 5 min) para
 *      disparar as mensagens cujo horário já chegou.
 */
class VisitorManagement
{
    private StorageInterface $storage;
    private WhatsAppSenderInterface $sender;
    private array $followUpFlow;

    public function __construct(
        StorageInterface $storage,
        WhatsAppSenderInterface $sender,
        array $followUpFlow
    ) {
        $this->storage      = $storage;
        $this->sender       = $sender;
        $this->followUpFlow = $followUpFlow;
    }

    // -------------------------------------------------------------------------
    // Cadastro
    // -------------------------------------------------------------------------

    /**
     * Cadastra um visitante e agenda automaticamente o fluxo de mensagens.
     *
     * @param array $data Campos obrigatórios: nome, telefone
     *                    Campos opcionais: email, como_conheceu, observacoes, visitado_em (Y-m-d H:i:s)
     * @return array Visitante com fluxo de mensagens agendadas
     */
    public function register(array $data): array
    {
        $this->validateVisitorData($data);

        $id          = uniqid('vis_', true);
        $registeredAt = $data['visitado_em'] ?? date('Y-m-d H:i:s');

        $visitor = [
            'id'           => $id,
            'nome'         => trim($data['nome']),
            'telefone'     => Members::formatPhone($data['telefone']),
            'email'        => $data['email'] ?? null,
            'como_conheceu'=> $data['como_conheceu'] ?? null,
            'observacoes'  => $data['observacoes'] ?? null,
            'visitado_em'  => $registeredAt,
            'criado_em'    => date('Y-m-d H:i:s'),
        ];

        $this->storage->save('visitors', $id, $visitor);
        $this->scheduleFollowUpFlow($id, $visitor['nome'], $visitor['telefone'], $registeredAt);

        return array_merge($visitor, [
            'mensagens_agendadas' => count($this->followUpFlow),
        ]);
    }

    // -------------------------------------------------------------------------
    // Consulta
    // -------------------------------------------------------------------------

    public function find(string $id): array
    {
        $visitor = $this->storage->find('visitors', $id);
        if (!$visitor) {
            throw new VisitorNotFoundException($id);
        }
        return $visitor;
    }

    public function all(): array
    {
        return array_values($this->storage->all('visitors'));
    }

    /** Retorna todas as mensagens agendadas de um visitante. */
    public function getSchedule(string $visitorId): array
    {
        $this->find($visitorId); // garante que o visitante existe
        return $this->storage->filter(
            'follow_up_schedule',
            fn($s) => $s['visitor_id'] === $visitorId
        );
    }

    // -------------------------------------------------------------------------
    // Processamento do fluxo (deve ser chamado por cron/scheduler)
    // -------------------------------------------------------------------------

    /**
     * Verifica e envia todas as mensagens cujo horário agendado já passou.
     * Execute este método a cada 5 minutos via cron ou scheduler da sua aplicação.
     *
     * @return array Resumo com mensagens enviadas e falhas
     */
    public function processScheduled(): array
    {
        $now      = new \DateTimeImmutable();
        $pending  = $this->storage->filter('follow_up_schedule', function (array $item) use ($now): bool {
            return $item['status'] === 'pending'
                && new \DateTimeImmutable($item['scheduled_at']) <= $now;
        });

        $report = ['enviadas' => 0, 'falhas' => 0, 'detalhes' => []];

        foreach ($pending as $item) {
            $visitor = $this->storage->find('visitors', $item['visitor_id']);
            if (!$visitor) {
                $this->markScheduleItem($item['id'], 'skipped', 'Visitante não encontrado');
                continue;
            }

            $message = $this->personalize($item['message'], $visitor);
            $success = false;
            $error   = null;

            try {
                $success = $this->sender->send($visitor['telefone'], $message);
            } catch (\Throwable $e) {
                $error = $e->getMessage();
            }

            $status = $success ? 'sent' : 'failed';
            $this->markScheduleItem($item['id'], $status, $error);

            $report['detalhes'][] = [
                'visitor_id'   => $item['visitor_id'],
                'nome'         => $visitor['nome'],
                'step'         => $item['step'],
                'scheduled_at' => $item['scheduled_at'],
                'status'       => $status,
                'erro'         => $error,
            ];

            $success ? $report['enviadas']++ : $report['falhas']++;
        }

        return $report;
    }

    /**
     * Reagenda manualmente o fluxo de um visitante a partir do zero.
     * Cancela mensagens pendentes anteriores.
     */
    public function reschedule(string $visitorId): void
    {
        $visitor = $this->find($visitorId);

        // Cancela pendências anteriores
        $pending = $this->storage->filter(
            'follow_up_schedule',
            fn($s) => $s['visitor_id'] === $visitorId && $s['status'] === 'pending'
        );
        foreach ($pending as $item) {
            $this->markScheduleItem($item['id'], 'cancelled', 'Reagendado manualmente');
        }

        $this->scheduleFollowUpFlow(
            $visitorId,
            $visitor['nome'],
            $visitor['telefone'],
            date('Y-m-d H:i:s')
        );
    }

    // -------------------------------------------------------------------------
    // Internals
    // -------------------------------------------------------------------------

    private function scheduleFollowUpFlow(
        string $visitorId,
        string $name,
        string $phone,
        string $baseTime
    ): void {
        $base = new \DateTimeImmutable($baseTime);

        foreach ($this->followUpFlow as $index => $step) {
            $scheduledAt = $base->modify("+{$step['delay_hours']} hours");
            $scheduleId  = uniqid('sch_', true);

            $this->storage->save('follow_up_schedule', $scheduleId, [
                'id'           => $scheduleId,
                'visitor_id'   => $visitorId,
                'step'         => $index + 1,
                'delay_hours'  => $step['delay_hours'],
                'message'      => $step['message'],
                'scheduled_at' => $scheduledAt->format('Y-m-d H:i:s'),
                'status'       => 'pending',  // pending | sent | failed | skipped | cancelled
                'sent_at'      => null,
                'error'        => null,
            ]);
        }
    }

    private function markScheduleItem(string $id, string $status, ?string $error = null): void
    {
        $item = $this->storage->find('follow_up_schedule', $id);
        if (!$item) {
            return;
        }
        $item['status'] = $status;
        $item['error']  = $error;
        if ($status === 'sent') {
            $item['sent_at'] = date('Y-m-d H:i:s');
        }
        $this->storage->save('follow_up_schedule', $id, $item);
    }

    private function personalize(string $message, array $visitor): string
    {
        return str_replace(
            ['{nome}', '{telefone}'],
            [$visitor['nome'] ?? '', $visitor['telefone'] ?? ''],
            $message
        );
    }

    private function validateVisitorData(array $data): void
    {
        foreach (['nome', 'telefone'] as $field) {
            if (empty($data[$field])) {
                throw new ChurchException("Campo obrigatório ausente: {$field}");
            }
        }
    }
}
