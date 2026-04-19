<?php

namespace Church\Resources;

use Church\Contracts\StorageInterface;
use Church\Exceptions\MemberNotFoundException;

class Members
{
    const GENDER_MALE   = 'M';
    const GENDER_FEMALE = 'F';

    private StorageInterface $storage;

    public function __construct(StorageInterface $storage)
    {
        $this->storage = $storage;
    }

    /**
     * Cadastra um novo membro.
     *
     * @param array $data Campos obrigatórios: nome, telefone, genero (M/F)
     *                    Campos opcionais: grupo (string), ativo (bool, padrão true)
     */
    public function create(array $data): array
    {
        $this->validate($data);

        $id     = uniqid('mbr_', true);
        $member = [
            'id'        => $id,
            'nome'      => trim($data['nome']),
            'telefone'  => self::formatPhone($data['telefone']),
            'genero'    => strtoupper($data['genero']),
            'grupo'     => $data['grupo'] ?? null,
            'ativo'     => $data['ativo'] ?? true,
            'criado_em' => date('Y-m-d H:i:s'),
        ];

        return $this->storage->save('members', $id, $member);
    }

    public function update(string $id, array $data): array
    {
        $member = $this->find($id);

        if (isset($data['telefone'])) {
            $data['telefone'] = self::formatPhone($data['telefone']);
        }
        if (isset($data['genero'])) {
            $data['genero'] = strtoupper($data['genero']);
        }

        return $this->storage->save('members', $id, array_merge($member, $data, ['id' => $id]));
    }

    public function find(string $id): array
    {
        $member = $this->storage->find('members', $id);
        if (!$member) {
            throw new MemberNotFoundException($id);
        }
        return $member;
    }

    public function all(): array
    {
        return array_values($this->storage->all('members'));
    }

    public function allActive(): array
    {
        return $this->storage->filter('members', fn($m) => $m['ativo'] === true);
    }

    public function men(): array
    {
        return $this->storage->filter('members', fn($m) => $m['ativo'] && $m['genero'] === self::GENDER_MALE);
    }

    public function women(): array
    {
        return $this->storage->filter('members', fn($m) => $m['ativo'] && $m['genero'] === self::GENDER_FEMALE);
    }

    public function byGroup(string $group): array
    {
        return $this->storage->filter('members', fn($m) => $m['ativo'] && $m['grupo'] === $group);
    }

    public function byIds(array $ids): array
    {
        return $this->storage->filter('members', fn($m) => $m['ativo'] && in_array($m['id'], $ids));
    }

    public function delete(string $id): bool
    {
        return $this->storage->delete('members', $id);
    }

    public static function formatPhone(string $phone): string
    {
        $digits = preg_replace('/\D/', '', $phone);
        // Adiciona código do Brasil (+55) se o número ainda não o tiver
        if (strlen($digits) === 11 || strlen($digits) === 10) {
            $digits = '55' . $digits;
        }
        return $digits;
    }

    private function validate(array $data): void
    {
        foreach (['nome', 'telefone', 'genero'] as $field) {
            if (empty($data[$field])) {
                throw new \Church\Exceptions\ChurchException("Campo obrigatório ausente: {$field}");
            }
        }
        if (!in_array(strtoupper($data['genero']), [self::GENDER_MALE, self::GENDER_FEMALE])) {
            throw new \Church\Exceptions\ChurchException("Gênero inválido. Use 'M' para masculino ou 'F' para feminino.");
        }
    }
}
