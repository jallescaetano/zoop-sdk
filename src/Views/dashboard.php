<!-- Stats cards -->
<div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-5 mb-8">
    <?php
    $cards = [
        ['label' => 'Membros Ativos',  'value' => $totalMembers,  'icon' => '👥', 'color' => 'indigo'],
        ['label' => 'Homens',          'value' => $totalMen,       'icon' => '👨', 'color' => 'blue'],
        ['label' => 'Mulheres',        'value' => $totalWomen,     'icon' => '👩', 'color' => 'pink'],
        ['label' => 'Visitantes',      'value' => $totalVisitors,  'icon' => '🙋', 'color' => 'violet'],
    ];
    $colorMap = [
        'indigo' => ['bg' => 'bg-indigo-50', 'text' => 'text-indigo-700', 'icon' => 'bg-indigo-100'],
        'blue'   => ['bg' => 'bg-blue-50',   'text' => 'text-blue-700',   'icon' => 'bg-blue-100'],
        'pink'   => ['bg' => 'bg-pink-50',   'text' => 'text-pink-700',   'icon' => 'bg-pink-100'],
        'violet' => ['bg' => 'bg-violet-50', 'text' => 'text-violet-700', 'icon' => 'bg-violet-100'],
    ];
    foreach ($cards as $card):
        $c = $colorMap[$card['color']];
    ?>
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 flex items-center gap-4">
        <div class="w-12 h-12 <?= $c['icon'] ?> rounded-xl flex items-center justify-center text-2xl flex-shrink-0">
            <?= $card['icon'] ?>
        </div>
        <div>
            <p class="text-2xl font-bold <?= $c['text'] ?>"><?= $card['value'] ?></p>
            <p class="text-sm text-gray-500"><?= $card['label'] ?></p>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<!-- Quick actions -->
<div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <h2 class="font-semibold text-gray-800 mb-4">Ações Rápidas</h2>
        <div class="space-y-3">
            <a href="/comunicacao"
               class="flex items-center gap-3 p-3 rounded-xl bg-indigo-50 hover:bg-indigo-100 text-indigo-700 transition-colors">
                <span class="text-xl">💬</span>
                <div>
                    <p class="font-medium text-sm">Enviar Comunicado</p>
                    <p class="text-xs text-indigo-500">Mensagem para membros via WhatsApp</p>
                </div>
            </a>
            <a href="/visitantes/novo"
               class="flex items-center gap-3 p-3 rounded-xl bg-violet-50 hover:bg-violet-100 text-violet-700 transition-colors">
                <span class="text-xl">🙋</span>
                <div>
                    <p class="font-medium text-sm">Cadastrar Visitante</p>
                    <p class="text-xs text-violet-500">Inicia fluxo automático de acompanhamento</p>
                </div>
            </a>
            <a href="/membros/novo"
               class="flex items-center gap-3 p-3 rounded-xl bg-blue-50 hover:bg-blue-100 text-blue-700 transition-colors">
                <span class="text-xl">👤</span>
                <div>
                    <p class="font-medium text-sm">Adicionar Membro</p>
                    <p class="text-xs text-blue-500">Novo membro na lista de comunicação</p>
                </div>
            </a>
        </div>
    </div>

    <!-- Recent visitors -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <div class="flex items-center justify-between mb-4">
            <h2 class="font-semibold text-gray-800">Últimos Visitantes</h2>
            <a href="/visitantes" class="text-indigo-600 text-xs hover:underline">Ver todos</a>
        </div>
        <?php if (empty($recentVisitors)): ?>
        <p class="text-sm text-gray-400 text-center py-6">Nenhum visitante cadastrado ainda.</p>
        <?php else: ?>
        <ul class="space-y-3">
            <?php foreach ($recentVisitors as $v): ?>
            <li class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 bg-violet-100 rounded-full flex items-center justify-center text-violet-700 font-semibold text-sm">
                        <?= strtoupper(substr($v['nome'], 0, 1)) ?>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-800"><?= htmlspecialchars($v['nome']) ?></p>
                        <p class="text-xs text-gray-400"><?= date('d/m/Y', strtotime($v['visitado_em'])) ?></p>
                    </div>
                </div>
                <a href="/visitantes/fluxo?id=<?= $v['id'] ?>"
                   class="text-xs text-indigo-600 hover:underline">Fluxo</a>
            </li>
            <?php endforeach; ?>
        </ul>
        <?php endif; ?>
    </div>
</div>
