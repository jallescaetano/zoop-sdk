<div class="flex items-center justify-between mb-6">
    <p class="text-sm text-gray-500"><?= count($visitors) ?> visitante(s) cadastrado(s)</p>
    <a href="/visitantes/novo"
       class="bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium px-5 py-2.5 rounded-xl transition-colors flex items-center gap-2">
        <span>+</span> Cadastrar Visitante
    </a>
</div>

<?php if (empty($visitors)): ?>
<div class="bg-white rounded-2xl border border-gray-100 p-12 text-center">
    <p class="text-4xl mb-3">🙋</p>
    <p class="text-gray-500">Nenhum visitante cadastrado ainda.</p>
    <a href="/visitantes/novo" class="mt-4 inline-block text-indigo-600 text-sm hover:underline">Cadastrar primeiro visitante</a>
</div>
<?php else: ?>
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 border-b border-gray-100">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Nome</th>
                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Telefone</th>
                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Como Conheceu</th>
                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Visitou em</th>
                <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wide">Fluxo</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-50">
            <?php foreach ($visitors as $v): ?>
            <tr class="hover:bg-gray-50 transition-colors">
                <td class="px-6 py-4">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 bg-violet-100 rounded-full flex items-center justify-center text-violet-700 font-semibold text-sm">
                            <?= strtoupper(substr($v['nome'], 0, 1)) ?>
                        </div>
                        <span class="font-medium text-gray-800"><?= htmlspecialchars($v['nome']) ?></span>
                    </div>
                </td>
                <td class="px-6 py-4 text-gray-500"><?= htmlspecialchars($v['telefone']) ?></td>
                <td class="px-6 py-4 text-gray-500"><?= htmlspecialchars($v['como_conheceu'] ?? '—') ?></td>
                <td class="px-6 py-4 text-gray-500"><?= date('d/m/Y H:i', strtotime($v['visitado_em'])) ?></td>
                <td class="px-6 py-4 text-right">
                    <a href="/visitantes/fluxo?id=<?= $v['id'] ?>"
                       class="text-indigo-600 hover:text-indigo-800 text-xs font-medium px-3 py-1.5 rounded-lg hover:bg-indigo-50 transition-colors">
                        Ver fluxo →
                    </a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php endif; ?>
