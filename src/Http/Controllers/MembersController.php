<?php

namespace Church\Http\Controllers;

class MembersController extends BaseController
{
    public function index(): void
    {
        $filter  = $this->get('filtro', 'todos');
        $members = match ($filter) {
            'homens'  => $this->client->members()->men(),
            'mulheres'=> $this->client->members()->women(),
            default   => $this->client->members()->allActive(),
        };

        $this->render('members/index', [
            'page'    => 'membros',
            'title'   => 'Membros',
            'members' => $members,
            'filter'  => $filter,
            'flash'   => $this->flash(),
        ]);
    }

    public function create(): void
    {
        $this->render('members/form', [
            'page'   => 'membros',
            'title'  => 'Novo Membro',
            'member' => null,
            'flash'  => $this->flash(),
        ]);
    }

    public function store(): void
    {
        try {
            $this->client->members()->create([
                'nome'     => $this->post('nome'),
                'telefone' => $this->post('telefone'),
                'genero'   => $this->post('genero'),
                'grupo'    => $this->post('grupo') ?: null,
            ]);
            $this->redirect('/membros', 'Membro cadastrado com sucesso!');
        } catch (\Throwable $e) {
            $this->redirect('/membros/novo', $e->getMessage(), 'error');
        }
    }

    public function edit(): void
    {
        $id     = $this->get('id');
        $member = $this->client->members()->find($id);

        $this->render('members/form', [
            'page'   => 'membros',
            'title'  => 'Editar Membro',
            'member' => $member,
            'flash'  => $this->flash(),
        ]);
    }

    public function update(): void
    {
        $id = $this->post('id');
        try {
            $this->client->members()->update($id, [
                'nome'     => $this->post('nome'),
                'telefone' => $this->post('telefone'),
                'genero'   => $this->post('genero'),
                'grupo'    => $this->post('grupo') ?: null,
            ]);
            $this->redirect('/membros', 'Membro atualizado com sucesso!');
        } catch (\Throwable $e) {
            $this->redirect('/membros/editar?id=' . $id, $e->getMessage(), 'error');
        }
    }

    public function destroy(): void
    {
        $id = $this->post('id');
        $this->client->members()->delete($id);
        $this->redirect('/membros', 'Membro removido.');
    }
}
