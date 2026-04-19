<?php

namespace Church;

use Church\Contracts\StorageInterface;
use Church\Contracts\WhatsAppSenderInterface;
use Church\Core\Config;
use Church\Core\JsonStorage;
use Church\Resources\Communication;
use Church\Resources\Members;
use Church\Resources\VisitorManagement;
use Church\WhatsApp\EvolutionApiSender;

/**
 * Ponto de entrada do sistema de comunicação para igrejas.
 *
 * Exemplo de uso:
 *
 *   $client = ChurchClient::create(
 *       whatsappApiUrl: 'https://api.suaigreja.com',
 *       whatsappApiKey: 'SUA_CHAVE',
 *       instanceName:   'minha-igreja'
 *   );
 *
 *   // Módulo 1 — Comunicação segmentada
 *   $client->communication()->sendToAll('Culto especial neste domingo às 19h! 🙌');
 *   $client->communication()->sendToWomen('Reunião do ministério feminino amanhã às 20h.');
 *   $client->communication()->sendToMen('Encontro de homens sábado às 8h. Confirme presença!');
 *   $client->communication()->sendToGroup('jovens', 'Ensaio da equipe de louvor hoje à tarde!');
 *
 *   // Módulo 2 — Cadastro de visitantes + fluxo automático
 *   $client->visitors()->register([
 *       'nome'          => 'Maria Silva',
 *       'telefone'      => '11987654321',
 *       'como_conheceu' => 'Indicação de amigo',
 *   ]);
 *
 *   // Executar a cada 5 min via cron para disparar mensagens agendadas:
 *   $client->visitors()->processScheduled();
 */
class ChurchClient
{
    private Members $members;
    private Communication $communication;
    private VisitorManagement $visitors;

    public function __construct(
        StorageInterface $storage,
        WhatsAppSenderInterface $sender,
        array $followUpFlow
    ) {
        $this->members       = new Members($storage);
        $this->communication = new Communication($sender, $this->members);
        $this->visitors      = new VisitorManagement($storage, $sender, $followUpFlow);
    }

    /**
     * Cria o cliente usando a configuração padrão (Evolution API + JSON storage).
     */
    public static function create(
        string $whatsappApiUrl,
        string $whatsappApiKey,
        string $instanceName,
        array $options = []
    ): self {
        $config  = Config::configure($whatsappApiUrl, $whatsappApiKey, $instanceName, $options);
        $storage = new JsonStorage($config['storage_path']);
        $sender  = new EvolutionApiSender($config['guzzle'], $instanceName);

        return new self($storage, $sender, $config['follow_up_flow']);
    }

    /**
     * Cria o cliente injetando dependências customizadas.
     * Útil para usar outro banco de dados ou outro provedor WhatsApp.
     */
    public static function createCustom(
        StorageInterface $storage,
        WhatsAppSenderInterface $sender,
        array $followUpFlow
    ): self {
        return new self($storage, $sender, $followUpFlow);
    }

    // -------------------------------------------------------------------------
    // Módulos
    // -------------------------------------------------------------------------

    /** Módulo de comunicação segmentada via WhatsApp. */
    public function communication(): Communication
    {
        return $this->communication;
    }

    /** Módulo de gestão de visitantes e fluxo de acompanhamento. */
    public function visitors(): VisitorManagement
    {
        return $this->visitors;
    }

    /** Gestão direta de membros cadastrados. */
    public function members(): Members
    {
        return $this->members;
    }
}
