<div class="max-w-lg">
    <a href="/visitantes" class="text-indigo-600 text-sm hover:underline mb-6 inline-flex items-center gap-1">
        ← Voltar para visitantes
    </a>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8 mt-4">
        <p class="text-sm text-gray-500 mb-6">
            Ao cadastrar, o sistema agenda automaticamente um fluxo de mensagens de acompanhamento para a semana.
        </p>

        <form method="POST" action="/visitantes" class="space-y-5">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Nome completo *</label>
                <input type="text" name="nome" required
                       placeholder="Ex: Maria da Silva"
                       class="w-full border border-gray-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Telefone (WhatsApp) *</label>
                <input type="tel" name="telefone" required
                       placeholder="(11) 99999-9999"
                       class="w-full border border-gray-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">E-mail <span class="text-gray-400 font-normal">(opcional)</span></label>
                <input type="email" name="email"
                       placeholder="email@exemplo.com"
                       class="w-full border border-gray-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Como conheceu a igreja? <span class="text-gray-400 font-normal">(opcional)</span></label>
                <select name="como_conheceu"
                        class="w-full border border-gray-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <option value="">Selecione...</option>
                    <option>Indicação de amigo</option>
                    <option>Redes sociais</option>
                    <option>Passando pela rua</option>
                    <option>Evento / culto especial</option>
                    <option>Google / internet</option>
                    <option>Outro</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Observações <span class="text-gray-400 font-normal">(opcional)</span></label>
                <textarea name="observacoes" rows="3"
                          placeholder="Anotações sobre a visita..."
                          class="w-full border border-gray-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 resize-none"></textarea>
            </div>

            <!-- Flow preview -->
            <div class="bg-indigo-50 rounded-xl p-4">
                <p class="text-xs font-semibold text-indigo-700 mb-2">📅 Fluxo automático que será agendado:</p>
                <ul class="space-y-1 text-xs text-indigo-600">
                    <li>✉️ Após 2h — Mensagem de boas-vindas</li>
                    <li>✉️ Após 48h — Mensagem de acompanhamento</li>
                    <li>✉️ Após 96h — Programação da semana</li>
                    <li>✉️ Após 168h — Convite para o próximo domingo</li>
                </ul>
            </div>

            <div class="pt-2 flex gap-3">
                <button type="submit"
                        class="bg-indigo-600 hover:bg-indigo-700 text-white font-semibold px-6 py-2.5 rounded-xl transition-colors text-sm flex items-center gap-2">
                    🙋 Cadastrar Visitante
                </button>
                <a href="/visitantes"
                   class="border border-gray-300 text-gray-600 hover:bg-gray-50 font-medium px-6 py-2.5 rounded-xl transition-colors text-sm">
                    Cancelar
                </a>
            </div>
        </form>
    </div>
</div>
