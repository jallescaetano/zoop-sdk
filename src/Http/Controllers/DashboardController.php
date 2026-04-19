<?php

namespace Church\Http\Controllers;

class DashboardController extends BaseController
{
    public function index(): void
    {
        $members  = $this->client->members()->all();
        $visitors = $this->client->visitors()->all();

        $men   = count(array_filter($members, fn($m) => $m['genero'] === 'M' && $m['ativo']));
        $women = count(array_filter($members, fn($m) => $m['genero'] === 'F' && $m['ativo']));

        $recentVisitors = array_slice(
            array_reverse($visitors),
            0, 5
        );

        $this->render('dashboard', [
            'page'           => 'dashboard',
            'title'          => 'Dashboard',
            'totalMembers'   => count(array_filter($members, fn($m) => $m['ativo'])),
            'totalMen'       => $men,
            'totalWomen'     => $women,
            'totalVisitors'  => count($visitors),
            'recentVisitors' => $recentVisitors,
            'flash'          => $this->flash(),
        ]);
    }
}
