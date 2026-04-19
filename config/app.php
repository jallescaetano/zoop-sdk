<?php

return [
    'church_name'          => env('CHURCH_NAME', 'Minha Igreja'),
    'whatsapp_api_url'     => env('WHATSAPP_API_URL', ''),
    'whatsapp_api_key'     => env('WHATSAPP_API_KEY', ''),
    'whatsapp_instance'    => env('WHATSAPP_INSTANCE', ''),
    'storage_path'         => env('STORAGE_PATH', __DIR__ . '/../storage/data'),
    'follow_up_flow'       => [
        ['delay_hours' => 2,   'message' => 'Olá {nome}! 🙏 Foi uma alegria ter você conosco hoje. Obrigado pela sua visita!'],
        ['delay_hours' => 48,  'message' => 'Oi {nome}! Passando para dizer que sua presença foi muito especial. Estaremos te esperando no próximo culto!'],
        ['delay_hours' => 96,  'message' => 'Olá {nome}! Temos uma programação especial essa semana. Ficamos felizes em recebê-lo(a) novamente. Deus abençoe!'],
        ['delay_hours' => 168, 'message' => '{nome}, já faz uma semana desde a sua primeira visita! ❤️ Nossa família te espera com todo amor e carinho no próximo domingo.'],
    ],
];

function env(string $key, mixed $default = null): mixed
{
    return $_ENV[$key] ?? getenv($key) ?: $default;
}
