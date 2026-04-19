<?php

namespace Church\Resources;

use Church\Contracts\WhatsAppSenderInterface;
use Church\Exceptions\ChurchException;

/**
 * Módulo 1 — Comunicação
 *
 * Envia mensagens WhatsApp segmentadas por gênero, grupo ou lista específica de membros.
 */
class Communication
{
    /** Constantes de segmentação */
    const TARGET_ALL      = 'all';
    const TARGET_MEN      = 'men';
    const TARGET_WOMEN    = 'women';
    const TARGET_GROUP    = 'group';
    const TARGET_SPECIFIC = 'specific';

    private WhatsAppSenderInterface $sender;
    private Members $members;

    public function __construct(WhatsAppSenderInterface $sender, Members $members)
    {
        $this->sender  = $sender;
        $this->members = $members;
    }

    // -------------------------------------------------------------------------
    // Métodos públicos de envio
    // -------------------------------------------------------------------------

    /** Envia para todos os membros ativos. */
    public function sendToAll(string $message): array
    {
        return $this->dispatch($this->members->allActive(), $message);
    }

    /** Envia apenas para os homens. */
    public function sendToMen(string $message): array
    {
        return $this->dispatch($this->members->men(), $message);
    }

    /** Envia apenas para as mulheres. */
    public function sendToWomen(string $message): array
    {
        return $this->dispatch($this->members->women(), $message);
    }

    /** Envia para todos os membros de um grupo. */
    public function sendToGroup(string $group, string $message): array
    {
        $recipients = $this->members->byGroup($group);
        if (empty($recipients)) {
            throw new ChurchException("Nenhum membro ativo encontrado no grupo: {$group}");
        }
        return $this->dispatch($recipients, $message);
    }

    /** Envia para uma lista específica de IDs de membros. */
    public function sendToMembers(array $memberIds, string $message): array
    {
        if (empty($memberIds)) {
            throw new ChurchException('A lista de membros não pode ser vazia.');
        }
        return $this->dispatch($this->members->byIds($memberIds), $message);
    }

    /**
     * Envia para um número avulso (sem precisar ser membro cadastrado).
     * Útil para testes ou comunicações pontuais.
     */
    public function sendToPhone(string $phone, string $message): array
    {
        $formatted = Members::formatPhone($phone);
        $success   = false;
        $error     = null;

        try {
            $success = $this->sender->send($formatted, $message);
        } catch (\Throwable $e) {
            $error = $e->getMessage();
        }

        return $this->buildReport(
            [['telefone' => $formatted, 'nome' => $phone, 'sucesso' => $success, 'erro' => $error]]
        );
    }

    /**
     * Método genérico de envio usando as constantes TARGET_*.
     *
     * @param string $target   Uma das constantes TARGET_*
     * @param string $message  Texto da mensagem (aceita {nome}, {grupo})
     * @param array  $extra    Para TARGET_SPECIFIC: array de IDs de membros
     *                         Para TARGET_GROUP: string com nome do grupo
     */
    public function send(string $target, string $message, array|string $extra = []): array
    {
        return match ($target) {
            self::TARGET_ALL      => $this->sendToAll($message),
            self::TARGET_MEN      => $this->sendToMen($message),
            self::TARGET_WOMEN    => $this->sendToWomen($message),
            self::TARGET_GROUP    => $this->sendToGroup((string) $extra, $message),
            self::TARGET_SPECIFIC => $this->sendToMembers((array) $extra, $message),
            default               => throw new ChurchException("Alvo de envio inválido: {$target}"),
        };
    }

    // -------------------------------------------------------------------------
    // Internals
    // -------------------------------------------------------------------------

    private function dispatch(array $recipients, string $message): array
    {
        $rows = [];

        foreach ($recipients as $member) {
            $text    = $this->personalize($message, $member);
            $success = false;
            $error   = null;

            try {
                $success = $this->sender->send($member['telefone'], $text);
            } catch (\Throwable $e) {
                $error = $e->getMessage();
            }

            $rows[] = [
                'id'       => $member['id'],
                'nome'     => $member['nome'],
                'telefone' => $member['telefone'],
                'sucesso'  => $success,
                'erro'     => $error,
            ];
        }

        return $this->buildReport($rows);
    }

    private function buildReport(array $rows): array
    {
        $sent   = count(array_filter($rows, fn($r) => $r['sucesso']));
        $failed = count($rows) - $sent;

        return [
            'total'      => count($rows),
            'enviados'   => $sent,
            'falhas'     => $failed,
            'resultados' => $rows,
        ];
    }

    /** Substitui variáveis de template ({nome}, {grupo}) pela informação do membro. */
    private function personalize(string $message, array $member): string
    {
        return str_replace(
            ['{nome}', '{grupo}', '{telefone}'],
            [$member['nome'] ?? '', $member['grupo'] ?? '', $member['telefone'] ?? ''],
            $message
        );
    }
}
