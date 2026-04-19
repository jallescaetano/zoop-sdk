<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title) ?> — <?= htmlspecialchars($config['church_name']) ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: { DEFAULT: '#6366f1', dark: '#4f46e5', light: '#e0e7ff' },
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-gray-50 text-gray-800 font-sans" x-data>

<!-- Sidebar -->
<div class="flex h-screen overflow-hidden">
    <aside class="w-64 bg-indigo-900 text-white flex flex-col flex-shrink-0">
        <div class="px-6 py-6 border-b border-indigo-700">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-indigo-500 rounded-full flex items-center justify-center text-xl">✝</div>
                <div>
                    <p class="font-bold text-sm leading-tight"><?= htmlspecialchars($config['church_name']) ?></p>
                    <p class="text-indigo-300 text-xs">Sistema de Comunicação</p>
                </div>
            </div>
        </div>

        <nav class="flex-1 px-4 py-6 space-y-1 overflow-y-auto">
            <?php
            $nav = [
                ['/', 'dashboard',   '📊', 'Dashboard'],
                ['/comunicacao', 'comunicacao', '💬', 'Comunicação'],
                ['/membros',  'membros',   '👥', 'Membros'],
                ['/visitantes', 'visitantes', '🙋', 'Visitantes'],
            ];
            foreach ($nav as [$href, $key, $icon, $label]):
                $active = $page === $key;
            ?>
            <a href="<?= $href ?>"
               class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm transition-all
                      <?= $active ? 'bg-indigo-600 text-white font-semibold' : 'text-indigo-200 hover:bg-indigo-800 hover:text-white' ?>">
                <span class="text-base"><?= $icon ?></span>
                <?= $label ?>
            </a>
            <?php endforeach; ?>
        </nav>

        <div class="px-4 py-4 border-t border-indigo-700 text-xs text-indigo-400">
            Church Communication System
        </div>
    </aside>

    <!-- Main content -->
    <div class="flex-1 flex flex-col overflow-hidden">
        <!-- Top bar -->
        <header class="bg-white border-b border-gray-200 px-8 py-4 flex items-center justify-between flex-shrink-0">
            <h1 class="text-xl font-semibold text-gray-900"><?= htmlspecialchars($title) ?></h1>
            <span class="text-sm text-gray-400"><?= date('d/m/Y') ?></span>
        </header>

        <!-- Flash message -->
        <?php if (!empty($flash)): ?>
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)"
             class="mx-8 mt-4 px-4 py-3 rounded-lg text-sm flex items-center justify-between
                    <?= $flash['type'] === 'error' ? 'bg-red-50 text-red-700 border border-red-200' : 'bg-green-50 text-green-700 border border-green-200' ?>">
            <span><?= htmlspecialchars($flash['message']) ?></span>
            <button @click="show = false" class="ml-4 text-current opacity-60 hover:opacity-100 text-lg leading-none">&times;</button>
        </div>
        <?php endif; ?>

        <!-- Page content -->
        <main class="flex-1 overflow-y-auto p-8">
            <?php require __DIR__ . '/' . $view . '.php'; ?>
        </main>
    </div>
</div>

</body>
</html>
