<div class="max-w-2xl" x-data="{
    target: 'all',
    charCount: 0,
    updateCount(e) { this.charCount = e.target.value.length }
}">
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8">
        <p class="text-gray-500 text-sm mb-6">
            Envie mensagens pelo WhatsApp para grupos segmentados ou pessoas específicas.
            Use <code class="bg-gray-100 px-1 rounded">{nome}</code> para personalizar com o nome de cada membro.
        </p>

        <form method="POST" action="/comunicacao" class="space-y-6">

            <!-- Target selector -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-3">Enviar para</label>
                <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                    <?php
                    $targets = [
                        ['all',      '👥', 'Todos os membros'],
                        ['men',      '👨', 'Somente homens'],
                        ['women',    '👩', 'Somente mulheres'],
                        ['group',    '🏠', 'Um grupo'],
                        ['specific', '✅', 'Específicos'],
                    ];
                    foreach ($targets as [$val, $icon, $label]):
                    ?>
                    <label class="cursor-pointer">
                        <input type="radio" name="target" value="<?= $val ?>" x-model="target" class="sr-only">
                        <div :class="target === '<?= $val ?>'
                                ? 'border-indigo-500 bg-indigo-50 text-indigo-700'
                                : 'border-gray-200 hover:border-indigo-300 text-gray-600'"
                             class="border-2 rounded-xl p-3 text-center transition-all">
                            <p class="text-2xl mb-1"><?= $icon ?></p>
                            <p class="text-xs font-medium"><?= $label ?></p>
                        </div>
                    </label>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Group selector -->
            <div x-show="target === 'group'" x-transition>
                <label class="block text-sm font-medium text-gray-700 mb-2">Nome do grupo</label>
                <?php if (empty($groups)): ?>
                <p class="text-sm text-gray-400">Nenhum grupo cadastrado. Adicione o campo "grupo" nos membros.</p>
                <?php else: ?>
                <select name="extra" class="w-full border border-gray-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <?php foreach ($groups as $g): ?>
                    <option value="<?= htmlspecialchars($g) ?>"><?= htmlspecialchars($g) ?></option>
                    <?php endforeach; ?>
                </select>
                <?php endif; ?>
            </div>

            <!-- Specific members selector -->
            <div x-show="target === 'specific'" x-transition>
                <label class="block text-sm font-medium text-gray-700 mb-2">IDs dos membros (um por linha)</label>
                <textarea name="extra_ids" rows="3"
                          placeholder="mbr_abc123&#10;mbr_def456"
                          class="w-full border border-gray-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 resize-none"></textarea>
            </div>

            <!-- Message -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Mensagem</label>
                <textarea name="message" rows="5" required
                          @input="updateCount"
                          placeholder="Olá {nome}! Culto especial neste domingo às 19h. Estamos te esperando! 🙏"
                          class="w-full border border-gray-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 resize-none"></textarea>
                <p class="text-xs text-gray-400 mt-1" x-text="`${charCount} caracteres`"></p>
            </div>

            <!-- Submit -->
            <div class="flex items-center gap-4">
                <button type="submit"
                        class="bg-indigo-600 hover:bg-indigo-700 text-white font-semibold px-6 py-2.5 rounded-xl transition-colors text-sm flex items-center gap-2">
                    <span>📤</span> Enviar via WhatsApp
                </button>
                <p class="text-xs text-gray-400">As mensagens são enviadas individualmente para cada destinatário.</p>
            </div>
        </form>
    </div>
</div>

<script>
// Converte IDs de textarea em array no submit
document.querySelector('form').addEventListener('submit', function(e) {
    const idsArea = this.querySelector('[name="extra_ids"]');
    const target  = document.querySelector('[name="target"]:checked')?.value;

    if (target === 'specific' && idsArea) {
        const ids = idsArea.value.split('\n').map(s => s.trim()).filter(Boolean);
        idsArea.name = 'extra[]';
        ids.forEach((id, i) => {
            if (i > 0) {
                const inp = document.createElement('input');
                inp.type = 'hidden';
                inp.name = 'extra[]';
                inp.value = id;
                this.appendChild(inp);
            } else {
                idsArea.value = id;
            }
        });
    }
});
</script>
