<div class="max-w-lg">
    <a href="/membros" class="text-indigo-600 text-sm hover:underline mb-6 inline-flex items-center gap-1">
        ← Voltar para membros
    </a>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8 mt-4">
        <form method="POST" action="<?= $member ? '/membros/editar' : '/membros' ?>" class="space-y-5">
            <?php if ($member): ?>
            <input type="hidden" name="id" value="<?= $member['id'] ?>">
            <?php endif; ?>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Nome completo *</label>
                <input type="text" name="nome" required
                       value="<?= htmlspecialchars($member['nome'] ?? '') ?>"
                       placeholder="Ex: João da Silva"
                       class="w-full border border-gray-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Telefone (WhatsApp) *</label>
                <input type="tel" name="telefone" required
                       value="<?= htmlspecialchars($member['telefone'] ?? '') ?>"
                       placeholder="(11) 99999-9999"
                       class="w-full border border-gray-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Gênero *</label>
                <div class="flex gap-4">
                    <?php foreach ([['M','👨 Masculino'], ['F','👩 Feminino']] as [$val, $label]): ?>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="radio" name="genero" value="<?= $val ?>"
                               <?= ($member['genero'] ?? '') === $val ? 'checked' : ($val === 'M' && !$member ? 'checked' : '') ?>
                               class="accent-indigo-600 w-4 h-4">
                        <span class="text-sm text-gray-700"><?= $label ?></span>
                    </label>
                    <?php endforeach; ?>
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Grupo <span class="text-gray-400 font-normal">(opcional)</span></label>
                <input type="text" name="grupo"
                       value="<?= htmlspecialchars($member['grupo'] ?? '') ?>"
                       placeholder="Ex: jovens, casais, diaconato..."
                       class="w-full border border-gray-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>

            <div class="pt-2 flex gap-3">
                <button type="submit"
                        class="bg-indigo-600 hover:bg-indigo-700 text-white font-semibold px-6 py-2.5 rounded-xl transition-colors text-sm">
                    <?= $member ? 'Salvar alterações' : 'Cadastrar membro' ?>
                </button>
                <a href="/membros"
                   class="border border-gray-300 text-gray-600 hover:bg-gray-50 font-medium px-6 py-2.5 rounded-xl transition-colors text-sm">
                    Cancelar
                </a>
            </div>
        </form>
    </div>
</div>
