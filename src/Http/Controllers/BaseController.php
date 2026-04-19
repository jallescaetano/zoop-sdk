<?php

namespace Church\Http\Controllers;

use Church\ChurchClient;

abstract class BaseController
{
    protected ChurchClient $client;
    protected array $config;

    public function __construct(ChurchClient $client, array $config)
    {
        $this->client = $client;
        $this->config = $config;
    }

    protected function render(string $view, array $data = []): void
    {
        $data['view']        = $view;
        $data['config']      = $this->config;
        $data['currentPath'] = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        extract($data);
        require __DIR__ . '/../../Views/layout.php';
    }

    protected function renderPartial(string $view, array $data = []): void
    {
        extract($data);
        require __DIR__ . "/../../Views/{$view}.php";
    }

    protected function redirect(string $path, string $flash = '', string $flashType = 'success'): void
    {
        if ($flash) {
            session_start();
            $_SESSION['flash']      = $flash;
            $_SESSION['flash_type'] = $flashType;
        }
        header("Location: {$path}");
        exit;
    }

    protected function flash(): ?array
    {
        session_start();
        if (!empty($_SESSION['flash'])) {
            $msg  = $_SESSION['flash'];
            $type = $_SESSION['flash_type'] ?? 'success';
            unset($_SESSION['flash'], $_SESSION['flash_type']);
            return ['message' => $msg, 'type' => $type];
        }
        return null;
    }

    protected function post(string $key, mixed $default = null): mixed
    {
        return $_POST[$key] ?? $default;
    }

    protected function get(string $key, mixed $default = null): mixed
    {
        return $_GET[$key] ?? $default;
    }
}
