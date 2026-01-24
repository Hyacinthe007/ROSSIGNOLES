<?php $title = "Détails du personnel - " . htmlspecialchars($personnel['nom'] . ' ' . $personnel['prenom']); ?>

<div class="mt-6 mb-8 px-6 flex justify-between items-center">
    <div class="flex items-center gap-4">
        <h1 class="text-3xl font-bold text-gray-800">
            <?= htmlspecialchars($personnel['nom'] . ' ' . $personnel['prenom']) ?>
            <span class="text-sm font-normal text-gray-500 ml-3">(<?= htmlspecialchars($personnel['matricule']) ?>)</span>
        </h1>
    </div>
    <div class="flex gap-3">
        <a href="<?= url('liste-personnel') ?>" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center gap-2 shadow-md">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Retour
        </a>
        <a href="<?= url('personnel/edit/' . $personnel['id']) ?>" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
            Modifier
        </a>
    </div>
</div>

<div class="px-6 grid grid-cols-1 lg:grid-cols-4 gap-6">
    <!-- Sidebar / Photo -->
    <div class="lg:col-span-1">
        <div class="bg-white rounded-xl shadow-sm p-6 text-center">
            <?php if (!empty($personnel['photo'])): ?>
                <img src="<?= public_url($personnel['photo']) ?>" alt="Photo" class="w-32 h-32 rounded-full mx-auto object-cover border-4 border-gray-100 mb-4">
            <?php else: ?>
                <div class="w-32 h-32 rounded-full mx-auto bg-gray-200 flex items-center justify-center text-gray-400 mb-4">
                    <svg class="w-16 h-16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                </div>
            <?php endif; ?>
            
            <h2 class="text-xl font-semibold text-gray-800"><?= htmlspecialchars($personnel['prenom']) ?></h2>
            <p class="text-gray-500 uppercase text-sm font-bold tracking-wide"><?= htmlspecialchars($personnel['nom']) ?></p>
            
            <div class="mt-4 pt-4 border-t border-gray-100 space-y-2 text-left">
                <div class="flex items-center gap-2 text-gray-600">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                    <span class="text-sm"><?= htmlspecialchars($personnel['telephone']) ?></span>
                </div>
                <div class="flex items-center gap-2 text-gray-600">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                    <span class="text-sm truncate"><?= htmlspecialchars($personnel['email']) ?></span>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content Tabs -->
    <div class="lg:col-span-3">
        <div class="bg-white rounded-xl shadow-sm overflow-hidden" x-data="{ tab: 'info' }">
            <div class="border-b border-gray-200 flex">
                <button @click="tab = 'info'" :class="{'border-blue-600 text-blue-600': tab === 'info', 'border-transparent text-gray-500 hover:text-gray-700': tab !== 'info'}" class="flex-1 py-4 px-6 text-center border-b-2 font-medium transition-colors">Informations</button>
                <button @click="tab = 'docs'" :class="{'border-blue-600 text-blue-600': tab === 'docs', 'border-transparent text-gray-500 hover:text-gray-700': tab !== 'docs'}" class="flex-1 py-4 px-6 text-center border-b-2 font-medium transition-colors">Documents</button>
                <!-- <button @click="tab = 'absences'" :class="{'border-blue-600 text-blue-600': tab === 'absences', 'border-transparent text-gray-500 hover:text-gray-700': tab !== 'absences'}" class="flex-1 py-4 px-6 text-center border-b-2 font-medium transition-colors">Absences</button> -->
            </div>

            <!-- Tab: Informations -->
            <div x-show="tab === 'info'" class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-6">
                    <div>
                        <h3 class="text-sm font-medium text-gray-500 mb-1">Date de naissance</h3>
                        <p class="text-gray-900"><?= date('d/m/Y', strtotime($personnel['date_naissance'])) ?></p>
                        <p class="text-sm text-gray-500">à <?= htmlspecialchars($personnel['lieu_naissance']) ?></p>
                    </div>
                    <div>
                        <h3 class="text-sm font-medium text-gray-500 mb-1">CIN</h3>
                        <p class="text-gray-900"><?= htmlspecialchars($personnel['cin']) ?></p>
                    </div>
                    <div>
                        <h3 class="text-sm font-medium text-gray-500 mb-1">Sexe</h3>
                        <p class="text-gray-900"><?= $personnel['sexe'] === 'M' ? 'Masculin' : 'Féminin' ?></p>
                    </div>
                    <div>
                        <h3 class="text-sm font-medium text-gray-500 mb-1">Adresse</h3>
                        <p class="text-gray-900"><?= nl2br(htmlspecialchars($personnel['adresse'])) ?></p>
                    </div>
                    <div class="md:col-span-2 border-t border-gray-100 pt-4 mt-2">
                        <h3 class="text-sm font-medium text-gray-500 mb-1">Informations professionnelles</h3>
                    </div>
                    <div>
                        <h3 class="text-sm font-medium text-gray-500 mb-1">Type de personnel</h3>
                        <p class="text-gray-900"><?= ucfirst(str_replace('_', ' ', htmlspecialchars($personnel['type_personnel']))) ?></p>
                    </div>
                    <div>
                        <h3 class="text-sm font-medium text-gray-500 mb-1">Diplôme</h3>
                        <p class="text-gray-900"><?= htmlspecialchars($personnel['diplome']) ?></p>
                    </div>
                    <div>
                        <h3 class="text-sm font-medium text-gray-500 mb-1">Date d'embauche</h3>
                        <p class="text-gray-900"><?= date('d/m/Y', strtotime($personnel['date_embauche'])) ?></p>
                    </div>
                </div>
            </div>

            <!-- Tab: Documents -->
            <div x-show="tab === 'docs'" class="p-6" style="display: none;">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-lg font-semibold text-gray-800">Documents enregistrés</h3>
                    <button onclick="document.getElementById('modal-add-doc').showModal()" class="px-3 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded text-sm font-medium">
                        + Ajouter un document
                    </button>
                </div>

                <?php if (empty($documents)): ?>
                    <div class="text-center py-8 text-gray-500 bg-gray-50 rounded-lg border border-dashed border-gray-300">
                        Aucun document disponible
                    </div>
                <?php else: ?>
                    <ul class="divide-y divide-gray-100">
                        <?php foreach ($documents as $doc): ?>
                        <li class="py-3 flex justify-between items-center">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded bg-blue-50 text-blue-600 flex items-center justify-center">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-900"><?= htmlspecialchars($doc['type_libelle'] ?? 'Document') ?></p>
                                    <p class="text-xs text-gray-500"><?= date('d/m/Y', strtotime($doc['created_at'])) ?></p>
                                </div>
                            </div>
                            <div class="flex gap-2">
                                <a href="<?= public_url($doc['chemin_fichier']) ?>" target="_blank" class="text-blue-600 hover:text-blue-800 text-sm">Voir</a>
                                <a href="<?= url('personnel/deleteDocument/' . $doc['id']) ?>" onclick="return confirm('Supprimer ce document ?')" class="text-red-600 hover:text-red-800 text-sm">Supprimer</a>
                            </div>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
                
                <!-- Modal Add Document -->
                <dialog id="modal-add-doc" class="modal rounded-lg p-0 shadow-xl w-full max-w-md">
                    <form action="<?= url('personnel/addDocument/' . $personnel['id']) ?>" method="POST" enctype="multipart/form-data" class="p-6">
                        <h3 class="font-bold text-lg mb-4">Nouveau document</h3>
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Type</label>
                                <select name="type_document_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    <option value="1">Contrat</option>
                                    <option value="2">CV</option>
                                    <option value="3">Diplôme</option>
                                    <option value="4">Pièce d'identité</option>
                                    <option value="5">Autre</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Fichier</label>
                                <input type="file" name="fichier" required class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Notes (optionnel)</label>
                                <textarea name="notes" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" rows="2"></textarea>
                            </div>
                        </div>
                        <div class="mt-6 flex justify-end gap-2">
                            <button type="button" onclick="document.getElementById('modal-add-doc').close()" class="px-4 py-2 text-gray-600 hover:text-gray-800">Annuler</button>
                            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Enregistrer</button>
                        </div>
                    </form>
                </dialog>
            </div>

            <!-- Tab: Absences -->
            <div x-show="tab === 'absences'" class="p-6" style="display: none;">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-lg font-semibold text-gray-800">Historique des absences</h3>
                    <button onclick="document.getElementById('modal-add-abs').showModal()" class="px-3 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded text-sm font-medium">
                        + Déclarer une absence
                    </button>
                </div>

                <?php if (empty($absences)): ?>
                    <div class="text-center py-8 text-gray-500 bg-gray-50 rounded-lg border border-dashed border-gray-300">
                        Aucune absence enregistrée
                    </div>
                <?php else: ?>
                    <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Dates</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Motif</th>
                                <th class="px-4 py-2 text-center text-xs font-medium text-gray-500 uppercase">Justifié</th>
                                <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            <?php foreach ($absences as $abs): ?>
                            <tr>
                                <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-900">
                                    Du <?= date('d/m/y', strtotime($abs['date_debut'])) ?> au <?= date('d/m/y', strtotime($abs['date_fin'])) ?>
                                </td>
                                <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-500"><?= htmlspecialchars($abs['motif']) ?></td>
                                <td class="px-4 py-2 whitespace-nowrap text-center">
                                    <?php if ($abs['justifie']): ?>
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Oui</span>
                                    <?php else: ?>
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">Non</span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-4 py-2 text-right text-sm font-medium">
                                    <?php if (!empty($abs['fichier_justificatif'])): ?>
                                        <a href="<?= public_url($abs['fichier_justificatif']) ?>" target="_blank" class="text-blue-600 hover:text-blue-900 mr-2">Justif.</a>
                                    <?php endif; ?>
                                    <a href="<?= url('personnel/deleteAbsence/' . $abs['id']) ?>" onclick="return confirm('Supprimer ?')" class="text-red-600 hover:text-red-900">Suppr.</a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    </div>
                <?php endif; ?>

                <!-- Modal Add Absence -->
                <dialog id="modal-add-abs" class="modal rounded-lg p-0 shadow-xl w-full max-w-md">
                    <form action="<?= url('personnel/addAbsence/' . $personnel['id']) ?>" method="POST" enctype="multipart/form-data" class="p-6">
                        <h3 class="font-bold text-lg mb-4">Déclarer une absence</h3>
                        <div class="space-y-4">
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Du</label>
                                    <input type="date" name="date_debut" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Au</label>
                                    <input type="date" name="date_fin" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Motif</label>
                                <input type="text" name="motif" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            </div>
                            <div class="flex items-center">
                                <input type="checkbox" name="justifie" id="chk_justifie" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                <label for="chk_justifie" class="ml-2 block text-sm text-gray-900">Absence justifiée</label>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Justificatif (PDF/IMG)</label>
                                <input type="file" name="fichier_justificatif" class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                            </div>
                        </div>
                        <div class="mt-6 flex justify-end gap-2">
                            <button type="button" onclick="document.getElementById('modal-add-abs').close()" class="px-4 py-2 text-gray-600 hover:text-gray-800">Annuler</button>
                            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Valider</button>
                        </div>
                    </form>
                </dialog>
            </div>
        </div>
    </div>
</div>

