<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Página não encontrada</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 flex items-center justify-center min-h-screen">
    <div class="text-center">
        <p class="text-6xl mb-4">🤷</p>
        <h1 class="text-2xl font-bold text-gray-800 mb-2">Página não encontrada</h1>
        <p class="text-gray-500 mb-6"><?= htmlspecialchars($path) ?></p>
        <a href="/" class="bg-indigo-600 text-white px-6 py-2.5 rounded-xl text-sm font-medium hover:bg-indigo-700 transition-colors">
            Voltar ao início
        </a>
    </div>
</body>
</html>
