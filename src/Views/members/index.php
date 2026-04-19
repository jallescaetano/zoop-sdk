<div class="flex items-center justify-between mb-6">
    <!-- Filter tabs -->
    <div class="flex gap-2">
        <?php foreach ([['todos','Todos'], ['homens','Homens'], ['mulheres','Mulheres']] as [$f, $l]): ?>
        <a href="/membros?filtro=<?= $f ?>"
           class="px-4 py-2 rounded-xl text-sm font-medium transition-colors
                  <?= $filter === $f ? 'bg-indigo-600 text-white' : 'bg-white text-gray-600 border border-gray-200 hover:border-indigo-300' ?>">
            <?= $l ?>
        </a>
        <?php endforeach; ?>
    </div>
    <a href="/membros/novo"
       class="bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium px-5 py-2.5 rounded-xl transition-colors flex items-center gap-2">
        <span>+</span> Novo Membro
    </a>
</div>

<?php if (empty($members)): ?>
<div class="bg-white rounded-2xl border border-gray-100 p-12 text-center">
    <p class="text-4xl mb-3">👥</p>
    <p class="text-gray-500">Nenhum membro encontrado.</p>
    <a href="/membros/novo" class="mt-4 inline-block text-indigo-600 text-sm hover:underline">Cadastrar primeiro membro</a>
</div>
<?php else: ?>
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 border-b border-gray-100">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Nome</th>
                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Telefone</th>
                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Gênero</th>
                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Grupo</th>
                <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wide">Ações</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-50">
            <?php foreach ($members as $m): ?>
            <tr class="hover:bg-gray-50 transition-colors">
                <td class="px-6 py-4">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-full flex items-center justify-center font-semibold text-sm
                                    <?= $m['genero'] === 'M' ? 'bg-blue-100 text-blue-700' : 'bg-pink-100 text-pink-700' ?>">
                            <?= strtoupper(substr($m['nome'], 0, 1)) ?>
                        </div>
                        <span class="font-medium text-gray-800"><?= htmlspecialchars($m['nome']) ?></span>
                    </div>
                </td>
                <td class="px-6 py-4 text-gray-500"><?= htmlspecialchars($m['telefone']) ?></td>
                <td class="px-6 py-4">
                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium
                                 <?= $m['genero'] === 'M' ? 'bg-blue-50 text-blue-700' : 'bg-pink-50 text-pink-700' ?>">
                        <?= $m['genero'] === 'M' ? '👨 Homem' : '👩 Mulher' ?>
                    </span>
                </td>
                <td class="px-6 py-4 text-gray-500"><?= htmlspecialchars($m['grupo'] ?? '—') ?></td>
                <td class="px-6 py-4 text-right">
                    <div class="flex items-center justify-end gap-2">
                        <a href="/membros/editar?id=<?= $m['id'] ?>"
                           class="text-indigo-600 hover:text-indigo-800 text-xs font-medium px-3 py-1.5 rounded-lg hover:bg-indigo-50 transition-colors">
                            Editar
                        </a>
                        <form method="POST" action="/membros/deletar"
                              onsubmit="return confirm('Remover <?= htmlspecialchars(addslashes($m['nome'])) ?>?')">
                            <input type="hidden" name="id" value="<?= $m['id'] ?>">
                            <button type="submit"
                                    class="text-red-500 hover:text-red-700 text-xs font-medium px-3 py-1.5 rounded-lg hover:bg-red-50 transition-colors">
                                Remover
                            </button>
                        </form>
                    </div>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <div class="px-6 py-3 bg-gray-50 border-t border-gray-100 text-xs text-gray-400">
        <?= count($members) ?> membro(s)
    </div>
</div>
<?php endif; ?>
