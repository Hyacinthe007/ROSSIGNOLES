<div class="p-4 md:p-8">
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">
                <i class="fas fa-magic text-blue-600 mr-2"></i>Générer les Bulletins par Classe
            </h1>
            <p class="text-gray-600">Sélectionnez une classe pour générer automatiquement tous les bulletins des élèves</p>
        </div>
        <a href="<?= url('bulletins/list') ?>" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-lg transition flex items-center gap-2">
            <i class="fas fa-arrow-left"></i>
            <span>Retour à la liste</span>
        </a>
    </div>

    <?php if (isset($stats) && $stats): ?>
    <!-- Section Statistiques après génération -->
    <div class="mb-8 bg-gradient-to-r from-green-50 to-blue-50 rounded-xl shadow-lg p-6 border-l-4 border-green-500">
        <div class="flex items-center gap-3 mb-6">
            <div class="bg-green-500 text-white rounded-full p-3">
                <i class="fas fa-check-circle text-2xl"></i>
            </div>
            <div class="flex-grow">
                <h2 class="text-xl font-bold text-gray-800">Génération Réussie !</h2>
                <p class="text-gray-600">Bulletins générés en <?= $stats['execution_time'] ?? 0 ?>s</p>
            </div>
            <?php if (isset($stats['auto_valide']) && $stats['auto_valide']): ?>
            <div class="bg-green-600 text-white px-4 py-2 rounded-lg flex items-center gap-2 shadow-lg">
                <i class="fas fa-check-double"></i>
                <span class="font-bold">Validés automatiquement</span>
            </div>
            <?php endif; ?>
        </div>

        <!-- Statistiques Principales -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
            <div class="bg-white rounded-lg p-4 shadow">
                <div class="text-sm text-gray-600 mb-1">Total Élèves</div>
                <div class="text-3xl font-bold text-blue-600"><?= $stats['total_eleves'] ?? 0 ?></div>
            </div>
            <div class="bg-white rounded-lg p-4 shadow">
                <div class="text-sm text-gray-600 mb-1">Moyenne Classe</div>
                <div class="text-3xl font-bold text-purple-600"><?= number_format($stats['moyenne_classe'] ?? 0, 2) ?></div>
            </div>
            <div class="bg-white rounded-lg p-4 shadow">
                <div class="text-sm text-gray-600 mb-1">Taux de Réussite</div>
                <div class="text-3xl font-bold text-green-600"><?= number_format($stats['taux_reussite'] ?? 0, 1) ?>%</div>
                <div class="text-xs text-gray-500"><?= $stats['reussis'] ?? 0 ?>/<?= $stats['total_eleves'] ?? 0 ?> élèves</div>
            </div>
            <div class="bg-white rounded-lg p-4 shadow">
                <div class="text-sm text-gray-600 mb-1">Médiane</div>
                <div class="text-3xl font-bold text-orange-600"><?= number_format($stats['mediane'] ?? 0, 2) ?></div>
            </div>
        </div>

        <!-- Distribution des Mentions -->
        <div class="bg-white rounded-lg p-6 shadow mb-6">
            <h3 class="font-bold text-gray-800 mb-4 flex items-center gap-2">
                <i class="fas fa-chart-pie text-blue-600"></i>
                Distribution des Mentions
            </h3>
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-3">
                <?php 
                $mentionColors = [
                    'Excellent' => 'bg-purple-100 text-purple-800 border-purple-300',
                    'Très bien' => 'bg-blue-100 text-blue-800 border-blue-300',
                    'Bien' => 'bg-green-100 text-green-800 border-green-300',
                    'Assez bien' => 'bg-yellow-100 text-yellow-800 border-yellow-300',
                    'Passable' => 'bg-orange-100 text-orange-800 border-orange-300',
                    'Insuffisant' => 'bg-red-100 text-red-800 border-red-300'
                ];
                foreach ($stats['mentions'] ?? [] as $mention => $count): 
                ?>
                <div class="border-2 rounded-lg p-3 text-center <?= $mentionColors[$mention] ?? '' ?>">
                    <div class="text-2xl font-bold"><?= $count ?></div>
                    <div class="text-xs font-medium"><?= $mention ?></div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Top 3 Élèves -->
        <?php if (!empty($stats['top3'])): ?>
        <div class="bg-white rounded-lg p-6 shadow">
            <h3 class="font-bold text-gray-800 mb-4 flex items-center gap-2">
                <i class="fas fa-trophy text-yellow-500"></i>
                Top 3 de la Classe
            </h3>
            <div class="space-y-3">
                <?php foreach ($stats['top3'] as $index => $eleve): ?>
                <div class="flex items-center gap-4 p-3 bg-gray-50 rounded-lg">
                    <div class="flex-shrink-0">
                        <?php if ($index === 0): ?>
                            <div class="w-12 h-12 bg-yellow-400 rounded-full flex items-center justify-center text-white font-bold text-xl">
                                <i class="fas fa-crown"></i>
                            </div>
                        <?php elseif ($index === 1): ?>
                            <div class="w-12 h-12 bg-gray-400 rounded-full flex items-center justify-center text-white font-bold text-xl">
                                2
                            </div>
                        <?php else: ?>
                            <div class="w-12 h-12 bg-orange-400 rounded-full flex items-center justify-center text-white font-bold text-xl">
                                3
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="flex-grow">
                        <div class="font-bold text-gray-800">Élève #<?= $eleve['eleve_id'] ?></div>
                        <div class="text-sm text-gray-600"><?= $eleve['appreciation'] ?? '' ?></div>
                    </div>
                    <div class="text-right">
                        <div class="text-2xl font-bold text-blue-600"><?= number_format($eleve['moyenne_generale'], 2) ?></div>
                        <div class="text-xs text-gray-500">/ 20</div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>

        <div class="mt-6 flex justify-end gap-3">
            <a href="<?= url('bulletins/list') ?>" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-bold shadow-lg transition flex items-center gap-2">
                <i class="fas fa-list"></i>
                Voir tous les bulletins
            </a>
        </div>
    </div>
    <?php endif; ?>

    <!-- Interface Unifiée de Génération -->
    <div class="max-w-4xl mx-auto">
        <div class="bg-white rounded-xl shadow-lg p-8 border-t-4 border-blue-600">
            <h2 class="text-xl font-bold text-gray-800 mb-6 flex items-center gap-2">
                <i class="fas fa-magic text-blue-600"></i>
                Génération des Bulletins pour toute la Classe
            </h2>
            
            <form method="POST" action="<?= url('bulletins/generer') ?>" id="formGeneration">
                <?= csrf_field() ?>
                
                <div class="space-y-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Année Scolaire</label>
                        <select name="annee_scolaire_id" id="annee_scolaire_id" required class="w-full border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                            <?php foreach($anneesScolaires as $annee): ?>
                                <option value="<?= $annee['id'] ?>" <?= ($annee['actif'] ?? false) ? 'selected' : '' ?>><?= e($annee['libelle']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Classe</label>
                            <select name="classe_id" id="classe_id" required class="w-full border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                                <option value="">Sélectionner une classe</option>
                                <?php 
                                $groupedClasses = [];
                                foreach($classes as $classe) {
                                    $groupName = $classe['cycle_libelle'] ?? 'Autres';
                                    if ($groupName == 'Collège') $groupName = 'Secondaire';
                                    $groupedClasses[$groupName][] = $classe;
                                }
                                ?>
                                <?php foreach($groupedClasses as $groupName => $group): ?>
                                    <optgroup label="<?= e($groupName) ?>">
                                        <?php foreach($group as $classe): ?>
                                            <option value="<?= $classe['id'] ?>"><?= e($classe['code']) ?></option>
                                        <?php endforeach; ?>
                                    </optgroup>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Période</label>
                            <select name="periode_id" id="periode_id" required class="w-full border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                                <option value="">Sélectionner une période</option>
                                <?php foreach($periodes as $periode): ?>
                                    <option value="<?= $periode['id'] ?>"><?= e($periode['nom']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <!-- Aperçu du nombre d'élèves -->
                    <div id="apercu-eleves" class="hidden bg-gradient-to-r from-blue-50 to-indigo-50 p-5 rounded-lg border-2 border-blue-300 shadow-sm">
                        <div class="flex items-start gap-4">
                            <div class="flex-shrink-0">
                                <div class="bg-blue-600 text-white rounded-full p-3">
                                    <i class="fas fa-users text-2xl"></i>
                                </div>
                            </div>
                            <div class="flex-grow">
                                <div class="font-bold text-gray-800 text-lg mb-2">
                                    <i class="fas fa-check-circle text-green-600 mr-1"></i>
                                    <span id="nombre-eleves">0</span> bulletin(s) seront générés
                                </div>
                                <div class="text-sm text-gray-700 space-y-1">
                                    <div>
                                        <i class="fas fa-book text-blue-600 mr-2"></i>
                                        <span id="nombre-matieres">0</span> matière(s) configurée(s)
                                    </div>
                                    <div class="text-xs text-gray-600 mt-2 ">
                                        <i class="fas fa-info-circle mr-1"></i>
                                        Tous les élèves de la classe recevront leur bulletin
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Option de validation automatique -->
                    <div class="bg-gradient-to-r from-green-50 to-emerald-50 p-5 rounded-lg border-2 border-green-300">
                        <div class="flex items-start gap-3">
                            <input type="checkbox" name="valider_automatiquement" id="valider_automatiquement" value="1" class="mt-1 w-5 h-5 text-green-600 border-gray-300 rounded focus:ring-green-500">
                            <div class="flex-grow">
                                <label for="valider_automatiquement" class="font-bold text-gray-800 cursor-pointer flex items-center gap-2">
                                    <i class="fas fa-check-double text-green-600"></i>
                                    Valider automatiquement après génération
                                </label>
                                <p class="text-sm text-gray-600 mt-1">
                                    Les bulletins seront figés et prêts pour l'impression officielle
                                </p>
                                <div class="mt-3 text-xs text-green-800 bg-green-100 p-3 rounded">
                                    <p class="font-bold mb-1">⚠️ Si vous cochez cette option :</p>
                                    <ul class="list-disc ml-4 space-y-1">
                                        <li>Les moyennes et rangs seront figés</li>
                                        <li>Les notes ne pourront plus être modifiées</li>
                                        <li>L'impression officielle sera autorisée</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-blue-50 p-4 rounded-lg flex items-start gap-3">
                        <i class="fas fa-info-circle text-blue-600 mt-1"></i>
                        <div class="text-sm text-blue-800">
                            <p class="font-bold mb-1">Processus automatique pour toute la classe :</p>
                            <ul class="list-disc ml-4 space-y-1">
                                <li>Calcul des moyennes par matière pour chaque élève</li>
                                <li>Calcul de la moyenne générale pondérée</li>
                                <li>Attribution des rangs dans la classe</li>
                                <li>Génération des appréciations automatiques</li>
                                <li>Écrase les bulletins existants en état "Brouillon"</li>
                            </ul>
                        </div>
                    </div>

                    <div class="pt-4 border-t flex justify-end gap-3">
                        <a href="<?= url('bulletins/list') ?>" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-6 py-3 rounded-lg font-medium transition flex items-center gap-2">
                            <i class="fas fa-times"></i>
                            Annuler
                        </a>
                        <button type="submit" id="btnGenerer" class="bg-blue-600 hover:bg-blue-700 text-white px-8 py-3 rounded-lg font-bold shadow-lg transition flex items-center gap-2">
                            <i class="fas fa-sync-alt"></i>
                            Générer tous les bulletins
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal de progression -->
<div id="modalProgression" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white rounded-xl shadow-2xl p-8 max-w-md w-full mx-4">
        <div class="text-center">
            <div class="mb-6">
                <i class="fas fa-cog fa-spin text-blue-600 text-6xl"></i>
            </div>
            <h3 class="text-2xl font-bold text-gray-800 mb-2">Génération en cours...</h3>
            <p class="text-gray-600 mb-6">Veuillez patienter pendant le calcul des bulletins</p>
            
            <div class="w-full bg-gray-200 rounded-full h-4 mb-4">
                <div id="barreProgression" class="bg-blue-600 h-4 rounded-full transition-all duration-300" style="width: 0%"></div>
            </div>
            
            <div class="text-sm text-gray-600">
                <span id="etapeProgression">Initialisation...</span>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const classeSelect = document.getElementById('classe_id');
    const anneeSelect = document.getElementById('annee_scolaire_id');
    const apercuDiv = document.getElementById('apercu-eleves');
    const nombreElevesSpan = document.getElementById('nombre-eleves');
    const nombreMatieresSpan = document.getElementById('nombre-matieres');
    const formGeneration = document.getElementById('formGeneration');
    const modalProgression = document.getElementById('modalProgression');
    const barreProgression = document.getElementById('barreProgression');
    const etapeProgression = document.getElementById('etapeProgression');

    // Fonction pour charger l'aperçu
    function chargerApercu() {
        const classeId = classeSelect.value;
        const anneeId = anneeSelect.value;

        if (!classeId || !anneeId) {
            apercuDiv.classList.add('hidden');
            return;
        }

        // Appel AJAX pour récupérer le nombre d'élèves
        fetch(`<?= url('bulletins/api/eleves-count') ?>?classe_id=${classeId}&annee_scolaire_id=${anneeId}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    nombreElevesSpan.textContent = data.nombre_eleves;
                    nombreMatieresSpan.textContent = data.nombre_matieres;
                    apercuDiv.classList.remove('hidden');
                    
                    // Ajouter une animation
                    apercuDiv.classList.add('animate-pulse');
                    setTimeout(() => apercuDiv.classList.remove('animate-pulse'), 500);
                } else {
                    apercuDiv.classList.add('hidden');
                }
            })
            .catch(error => {
                console.error('Erreur:', error);
                apercuDiv.classList.add('hidden');
            });
    }

    // Écouter les changements
    classeSelect.addEventListener('change', chargerApercu);
    anneeSelect.addEventListener('change', chargerApercu);

    // Gestion de la barre de progression
    formGeneration.addEventListener('submit', function(e) {
        const nombreEleves = parseInt(nombreElevesSpan.textContent) || 0;
        
        if (nombreEleves === 0) {
            e.preventDefault();
            alert('⚠️ Aucun élève trouvé pour cette classe.\n\nVeuillez vérifier vos sélections (classe et année scolaire).');
            return;
        }

        // Afficher le modal
        modalProgression.classList.remove('hidden');
        
        // Simuler la progression (car le traitement est côté serveur)
        let progression = 0;
        const etapes = [
            'Récupération de tous les élèves de la classe...',
            'Chargement des matières et coefficients...',
            'Calcul des moyennes pour chaque élève...',
            'Attribution des rangs dans la classe...',
            'Génération des appréciations...',
            'Sauvegarde de tous les bulletins...',
            'Finalisation...'
        ];
        
        const interval = setInterval(() => {
            progression += Math.random() * 20;
            if (progression > 90) progression = 90; // Ne pas aller à 100% avant la vraie fin
            
            barreProgression.style.width = progression + '%';
            
            const etapeIndex = Math.floor((progression / 100) * etapes.length);
            if (etapeIndex < etapes.length) {
                etapeProgression.textContent = etapes[etapeIndex];
            }
        }, 500);
        
        // Le formulaire se soumettra normalement et la page se rechargera
    });
});
</script>

<style>
@keyframes pulse {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.5; }
}
.animate-pulse {
    animation: pulse 0.5s ease-in-out;
}
</style>

