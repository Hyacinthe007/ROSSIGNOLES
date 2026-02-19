<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Réinitialisation du mot de passe | École Mandroso</title>
    <link rel="icon" type="image/png" href="<?= url('public/uploads/favicone/favicon.png') ?>">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
    </style>
</head>
<body>
    <div class="w-full max-w-md mx-4">
        <div class="bg-white rounded-2xl shadow-2xl p-8">
            <!-- Logo -->
            <div class="text-center mb-8">
                <i class="fas fa-key text-5xl text-blue-600 mb-4"></i>
                <h1 class="text-3xl font-bold text-gray-800">Réinitialisation</h1>
                <p class="text-gray-600 mt-2">Récupérez votre mot de passe</p>
            </div>

            <?php if (isset($message)): ?>
                <div class="bg-green-50 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded">
                    <div class="flex items-center">
                        <i class="fas fa-check-circle mr-2"></i>
                        <p><?= e($message) ?></p>
                    </div>
                </div>
            <?php endif; ?>

            <form method="POST" action="<?= url('auth/password-reset') ?>" class="space-y-6">
                <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-envelope mr-2 text-gray-500"></i>Adresse email
                    </label>
                    <input type="email" 
                           id="email" 
                           name="email" 
                           required
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                           placeholder="votre@email.com">
                    <p class="mt-2 text-sm text-gray-500">
                        Entrez votre adresse email et nous vous enverrons un lien de réinitialisation.
                    </p>
                </div>

                <button type="submit" 
                        class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-4 rounded-lg transition transform hover:scale-105 shadow-lg">
                    <i class="fas fa-paper-plane mr-2"></i>Envoyer le lien
                </button>
            </form>

            <div class="mt-6 text-center">
                <a href="<?= url('auth/login') ?>" class="text-sm text-blue-600 hover:text-blue-800">
                    <i class="fas fa-arrow-left mr-1"></i>Retour à la connexion
                </a>
            </div>
        </div>
    </div>
</body>
</html>
