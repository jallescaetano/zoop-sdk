<?php

namespace Church\Core;

use Church\Contracts\StorageInterface;

class JsonStorage implements StorageInterface
{
    private string $basePath;

    public function __construct(string $basePath)
    {
        $this->basePath = $basePath;
        if (!is_dir($this->basePath)) {
            mkdir($this->basePath, 0755, true);
        }
    }

    public function all(string $collection): array
    {
        $file = $this->filePath($collection);
        if (!file_exists($file)) {
            return [];
        }
        $data = json_decode(file_get_contents($file), true);
        return is_array($data) ? $data : [];
    }

    public function find(string $collection, string $id): ?array
    {
        return $this->all($collection)[$id] ?? null;
    }

    public function save(string $collection, string $id, array $data): array
    {
        $records      = $this->all($collection);
        $records[$id] = $data;
        file_put_contents(
            $this->filePath($collection),
            json_encode($records, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
        );
        return $data;
    }

    public function delete(string $collection, string $id): bool
    {
        $records = $this->all($collection);
        if (!isset($records[$id])) {
            return false;
        }
        unset($records[$id]);
        file_put_contents(
            $this->filePath($collection),
            json_encode($records, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
        );
        return true;
    }

    public function filter(string $collection, callable $callback): array
    {
        return array_values(array_filter($this->all($collection), $callback));
    }

    private function filePath(string $collection): string
    {
        return $this->basePath . '/' . $collection . '.json';
    }
}
