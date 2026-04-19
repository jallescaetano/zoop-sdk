<?php

namespace Church\Http\Controllers;

class VisitorsController extends BaseController
{
    public function index(): void
    {
        $visitors = array_reverse($this->client->visitors()->all());

        $this->render('visitors/index', [
            'page'     => 'visitantes',
            'title'    => 'Visitantes',
            'visitors' => $visitors,
            'flash'    => $this->flash(),
        ]);
    }

    public function create(): void
    {
        $this->render('visitors/form', [
            'page'  => 'visitantes',
            'title' => 'Cadastrar Visitante',
            'flash' => $this->flash(),
        ]);
    }

    public function store(): void
    {
        try {
            $this->client->visitors()->register([
                'nome'          => $this->post('nome'),
                'telefone'      => $this->post('telefone'),
                'email'         => $this->post('email') ?: null,
                'como_conheceu' => $this->post('como_conheceu') ?: null,
                'observacoes'   => $this->post('observacoes') ?: null,
            ]);
            $this->redirect('/visitantes', 'Visitante cadastrado! Fluxo de acompanhamento agendado.');
        } catch (\Throwable $e) {
            $this->redirect('/visitantes/novo', $e->getMessage(), 'error');
        }
    }

    public function schedule(): void
    {
        $id       = $this->get('id');
        $visitor  = $this->client->visitors()->find($id);
        $schedule = $this->client->visitors()->getSchedule($id);

        usort($schedule, fn($a, $b) => $a['step'] <=> $b['step']);

        $this->render('visitors/schedule', [
            'page'     => 'visitantes',
            'title'    => 'Fluxo de Acompanhamento',
            'visitor'  => $visitor,
            'schedule' => $schedule,
            'flash'    => $this->flash(),
        ]);
    }
}
