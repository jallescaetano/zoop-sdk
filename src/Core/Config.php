<?php

namespace Church\Core;

use GuzzleHttp\Client;

class Config
{
    /**
     * Cria a configuração do sistema de comunicação da igreja.
     *
     * @param string $whatsappApiUrl  URL base da Evolution API  (ex: https://api.suaigreja.com)
     * @param string $whatsappApiKey  Chave de autenticação da Evolution API
     * @param string $instanceName    Nome da instância WhatsApp cadastrada na Evolution API
     * @param array  $options         Configurações opcionais (veja abaixo)
     *
     * Opções disponíveis:
     *   storage_path            (string)  Pasta para armazenar dados em JSON. Padrão: /tmp/church_data
     *   initial_follow_up_hours (int)     Horas após o cadastro para enviar 1ª mensagem ao visitante. Padrão: 2
     *   follow_up_flow          (array)   Sequência de mensagens de acompanhamento.
     *                                     Cada item: ['delay_hours' => int, 'message' => string]
     *                                     Variáveis disponíveis na mensagem: {nome}, {telefone}
     */
    public static function configure(
        string $whatsappApiUrl,
        string $whatsappApiKey,
        string $instanceName,
        array $options = []
    ): array {
        $defaults = [
            'storage_path'            => sys_get_temp_dir() . '/church_data',
            'initial_follow_up_hours' => 2,
            'follow_up_flow'          => [
                [
                    'delay_hours' => 2,
                    'message'     => 'Olá {nome}! 🙏 Foi uma alegria ter você conosco hoje. Obrigado pela sua visita! Esperamos te ver em breve.',
                ],
                [
                    'delay_hours' => 48,
                    'message'     => 'Oi {nome}, tudo bem? Passando para dizer que sua presença foi muito especial para nós. Estaremos te esperando no próximo culto!',
                ],
                [
                    'delay_hours' => 96,
                    'message'     => 'Olá {nome}! Temos uma programação especial essa semana. Ficamos felizes em recebê-lo(a) novamente. Deus abençoe!',
                ],
                [
                    'delay_hours' => 168,
                    'message'     => '{nome}, já faz uma semana desde a sua primeira visita! ❤️ Nossa família te espera com todo amor e carinho no próximo domingo.',
                ],
            ],
        ];

        $config = array_merge($defaults, $options);

        $config['whatsapp_api_url'] = rtrim($whatsappApiUrl, '/');
        $config['whatsapp_api_key'] = $whatsappApiKey;
        $config['instance_name']    = $instanceName;

        $config['guzzle'] = new Client([
            'base_uri'    => $config['whatsapp_api_url'] . '/',
            'headers'     => [
                'apikey'       => $config['whatsapp_api_key'],
                'Content-Type' => 'application/json',
            ],
            'http_errors' => false,
        ]);

        return $config;
    }
}
