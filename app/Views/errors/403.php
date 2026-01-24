<div class="flex flex-col items-center justify-center min-height-screen py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8 text-center bg-white p-10 rounded-2xl shadow-xl border border-gray-100">
        <div class="flex justify-center">
            <div class="p-4 bg-red-100 rounded-full">
                <i class="fas fa-shield-alt text-5xl text-red-600"></i>
            </div>
        </div>
        <div>
            <h2 class="mt-6 text-3xl font-extrabold text-gray-900">
                Accès Interdit
            </h2>
            <p class="mt-4 text-sm text-gray-600">
                Désolé, vous n'avez pas les permissions nécessaires pour accéder à cette page.
            </p>
            <?php if (isset($permission)): ?>
                <div class="mt-4 inline-block px-3 py-1 bg-gray-50 text-gray-500 text-xs font-mono rounded border">
                    Permission requise : <?= e($permission) ?>
                </div>
            <?php endif; ?>
        </div>
        <div class="mt-8">
            <a href="<?= url('dashboard') ?>" class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-xl text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-200">
                <i class="fas fa-arrow-left mr-2"></i>
                Retour au tableau de bord
            </a>
        </div>
    </div>
</div>
