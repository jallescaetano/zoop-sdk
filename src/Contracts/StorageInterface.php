<?php

namespace Church\Contracts;

interface StorageInterface
{
    public function all(string $collection): array;
    public function find(string $collection, string $id): ?array;
    public function save(string $collection, string $id, array $data): array;
    public function delete(string $collection, string $id): bool;
    public function filter(string $collection, callable $callback): array;
}
