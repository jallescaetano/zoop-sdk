<?php

namespace Church\WhatsApp;

use Church\Contracts\WhatsAppSenderInterface;
use Church\Exceptions\WhatsAppException;
use GuzzleHttp\ClientInterface;

/**
 * Integração com a Evolution API (https://evolution-api.com).
 * API open-source mais utilizada para automação WhatsApp no Brasil.
 */
class EvolutionApiSender implements WhatsAppSenderInterface
{
    private ClientInterface $client;
    private string $instance;

    public function __construct(ClientInterface $client, string $instance)
    {
        $this->client   = $client;
        $this->instance = $instance;
    }

    public function send(string $phone, string $message): bool
    {
        try {
            $response = $this->client->request('POST', "message/sendText/{$this->instance}", [
                'json' => [
                    'number'      => $phone,
                    'textMessage' => ['text' => $message],
                    'options'     => [
                        'delay'    => 1200,
                        'presence' => 'composing',
                    ],
                ],
            ]);

            $statusCode = $response->getStatusCode();

            if ($statusCode >= 500) {
                throw new WhatsAppException(
                    "Erro interno da API WhatsApp ao enviar para {$phone}. Status: {$statusCode}"
                );
            }

            return $statusCode >= 200 && $statusCode < 300;
        } catch (WhatsAppException $e) {
            throw $e;
        } catch (\Throwable $e) {
            throw new WhatsAppException(
                "Falha ao enviar mensagem WhatsApp para {$phone}: {$e->getMessage()}",
                0,
                $e
            );
        }
    }
}
