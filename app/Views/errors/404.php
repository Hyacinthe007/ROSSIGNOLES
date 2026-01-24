<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - Page non trouvée | École Mandroso</title>
    <link rel="icon" type="image/png" href="<?= url('public/uploads/favicone/favicon.png') ?>">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Outfit', 'sans-serif'],
                        mono: ['Outfit', 'sans-serif'],
                        serif: ['Outfit', 'sans-serif'],
                    },
                }
            }
        }
    </script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Outfit', sans-serif;
        }
    </style>
</head>
<body>
    <div class="text-center px-4">
        <div class="bg-white rounded-2xl shadow-2xl p-12 max-w-2xl">
            <div class="mb-8">
                <i class="fas fa-exclamation-triangle text-8xl text-yellow-500 mb-4"></i>
                <h1 class="text-6xl font-bold text-gray-800 mb-4">404</h1>
                <h2 class="text-2xl font-semibold text-gray-700 mb-4">Page non trouvée</h2>
                <p class="text-gray-600 mb-8">
                    Désolé, la page que vous recherchez n'existe pas ou a été déplacée.
                </p>
            </div>
            
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="<?= url('dashboard') ?>" 
                   class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-6 rounded-lg transition flex items-center justify-center gap-2">
                    <i class="fas fa-home"></i>
                    <span>Retour au tableau de bord</span>
                </a>
                <button onclick="window.history.back()" 
                        class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-semibold py-3 px-6 rounded-lg transition flex items-center justify-center gap-2">
                    <i class="fas fa-arrow-left"></i>
                    <span>Page précédente</span>
                </button>
            </div>
        </div>
    </div>
</body>
</html>
