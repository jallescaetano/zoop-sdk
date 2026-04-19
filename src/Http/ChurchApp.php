<?php

namespace Church\Http;

use Church\ChurchClient;
use Church\Core\Config;
use Church\Core\JsonStorage;
use Church\WhatsApp\EvolutionApiSender;

class ChurchApp
{
    private array $config;
    private ChurchClient $client;

    public function __construct(array $config)
    {
        $this->config = $config;

        $storage = new JsonStorage($config['storage_path']);
        $sender  = new EvolutionApiSender(
            Config::configure(
                $config['whatsapp_api_url'],
                $config['whatsapp_api_key'],
                $config['whatsapp_instance']
            )['guzzle'],
            $config['whatsapp_instance']
        );

        $this->client = ChurchClient::createCustom($storage, $sender, $config['follow_up_flow']);
    }

    public function run(): void
    {
        $path   = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $method = $_SERVER['REQUEST_METHOD'];

        // Remove base path if running in a subdirectory
        $path = '/' . trim($path, '/') ?: '/';

        $routes = [
            'GET'  => [
                '/'                 => [Controllers\DashboardController::class, 'index'],
                '/membros'          => [Controllers\MembersController::class, 'index'],
                '/membros/novo'     => [Controllers\MembersController::class, 'create'],
                '/membros/editar'   => [Controllers\MembersController::class, 'edit'],
                '/visitantes'       => [Controllers\VisitorsController::class, 'index'],
                '/visitantes/novo'  => [Controllers\VisitorsController::class, 'create'],
                '/visitantes/fluxo' => [Controllers\VisitorsController::class, 'schedule'],
                '/comunicacao'      => [Controllers\CommunicationController::class, 'index'],
            ],
            'POST' => [
                '/membros'          => [Controllers\MembersController::class, 'store'],
                '/membros/editar'   => [Controllers\MembersController::class, 'update'],
                '/membros/deletar'  => [Controllers\MembersController::class, 'destroy'],
                '/visitantes'       => [Controllers\VisitorsController::class, 'store'],
                '/comunicacao'      => [Controllers\CommunicationController::class, 'send'],
            ],
        ];

        $handler = $routes[$method][$path] ?? null;

        if (!$handler) {
            http_response_code(404);
            $this->render('404', ['path' => $path]);
            return;
        }

        [$controllerClass, $action] = $handler;
        $controller = new $controllerClass($this->client, $this->config);
        $controller->$action();
    }

    private function render(string $view, array $data = []): void
    {
        extract($data);
        require __DIR__ . "/../Views/{$view}.php";
    }

    public function getClient(): ChurchClient
    {
        return $this->client;
    }

    public function getConfig(): array
    {
        return $this->config;
    }
}
