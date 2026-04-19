<?php

namespace Church\Http\Controllers;

use Church\Resources\Communication;

class CommunicationController extends BaseController
{
    public function index(): void
    {
        $groups = $this->getGroups();

        $this->render('communication', [
            'page'   => 'comunicacao',
            'title'  => 'Comunicação',
            'groups' => $groups,
            'flash'  => $this->flash(),
        ]);
    }

    public function send(): void
    {
        $target  = $this->post('target', Communication::TARGET_ALL);
        $message = trim($this->post('message', ''));
        $extra   = $this->post('extra', []);

        if (empty($message)) {
            $this->redirect('/comunicacao', 'A mensagem não pode estar vazia.', 'error');
        }

        try {
            $result = $this->client->communication()->send($target, $message, $extra);
            $this->redirect(
                '/comunicacao',
                "Mensagens enviadas: {$result['enviados']} | Falhas: {$result['falhas']}"
            );
        } catch (\Throwable $e) {
            $this->redirect('/comunicacao', 'Erro ao enviar: ' . $e->getMessage(), 'error');
        }
    }

    private function getGroups(): array
    {
        $members = $this->client->members()->all();
        $groups  = array_unique(array_filter(array_column($members, 'grupo')));
        sort($groups);
        return $groups;
    }
}
