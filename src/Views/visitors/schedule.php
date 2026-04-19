<div class="max-w-2xl">
    <a href="/visitantes" class="text-indigo-600 text-sm hover:underline mb-6 inline-flex items-center gap-1">
        ← Voltar para visitantes
    </a>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8 mt-4 mb-6">
        <div class="flex items-center gap-4">
            <div class="w-14 h-14 bg-violet-100 rounded-full flex items-center justify-center text-violet-700 font-bold text-xl">
                <?= strtoupper(substr($visitor['nome'], 0, 1)) ?>
            </div>
            <div>
                <h2 class="font-bold text-lg text-gray-900"><?= htmlspecialchars($visitor['nome']) ?></h2>
                <p class="text-sm text-gray-500"><?= htmlspecialchars($visitor['telefone']) ?></p>
                <p class="text-xs text-gray-400 mt-0.5">Visitou em <?= date('d/m/Y \à\s H:i', strtotime($visitor['visitado_em'])) ?></p>
            </div>
        </div>
    </div>

    <!-- Timeline -->
    <h3 class="font-semibold text-gray-700 mb-4">Fluxo de Acompanhamento</h3>

    <?php if (empty($schedule)): ?>
    <p class="text-sm text-gray-400">Nenhuma mensagem agendada.</p>
    <?php else: ?>
    <div class="space-y-4">
        <?php
        $statusConfig = [
            'pending'   => ['bg' => 'bg-yellow-50 border-yellow-200', 'badge' => 'bg-yellow-100 text-yellow-700', 'label' => 'Aguardando', 'icon' => '⏳'],
            'sent'      => ['bg' => 'bg-green-50 border-green-200',   'badge' => 'bg-green-100 text-green-700',   'label' => 'Enviada',    'icon' => '✅'],
            'failed'    => ['bg' => 'bg-red-50 border-red-200',       'badge' => 'bg-red-100 text-red-700',       'label' => 'Falhou',     'icon' => '❌'],
            'skipped'   => ['bg' => 'bg-gray-50 border-gray-200',     'badge' => 'bg-gray-100 text-gray-600',     'label' => 'Pulada',     'icon' => '⏭️'],
            'cancelled' => ['bg' => 'bg-gray-50 border-gray-200',     'badge' => 'bg-gray-100 text-gray-500',     'label' => 'Cancelada',  'icon' => '🚫'],
        ];
        foreach ($schedule as $item):
            $sc = $statusConfig[$item['status']] ?? $statusConfig['pending'];
        ?>
        <div class="border rounded-xl p-5 <?= $sc['bg'] ?>">
            <div class="flex items-start justify-between gap-4">
                <div class="flex-1">
                    <div class="flex items-center gap-2 mb-2">
                        <span class="text-lg"><?= $sc['icon'] ?></span>
                        <span class="font-semibold text-sm text-gray-800">Mensagem <?= $item['step'] ?></span>
                        <span class="inline-flex px-2.5 py-0.5 rounded-full text-xs font-medium <?= $sc['badge'] ?>">
                            <?= $sc['label'] ?>
                        </span>
                    </div>
                    <p class="text-sm text-gray-700 bg-white bg-opacity-60 rounded-lg p-3 italic">
                        "<?= htmlspecialchars($item['message']) ?>"
                    </p>
                    <?php if ($item['error']): ?>
                    <p class="text-xs text-red-600 mt-2">Erro: <?= htmlspecialchars($item['error']) ?></p>
                    <?php endif; ?>
                </div>
                <div class="text-right text-xs text-gray-500 flex-shrink-0">
                    <p class="font-medium">Agendado para</p>
                    <p><?= date('d/m/Y', strtotime($item['scheduled_at'])) ?></p>
                    <p><?= date('H:i', strtotime($item['scheduled_at'])) ?></p>
                    <?php if ($item['sent_at']): ?>
                    <p class="text-green-600 mt-1">Enviado às <?= date('H:i d/m', strtotime($item['sent_at'])) ?></p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>
</div>
