<?php
$title = "Détails de l'Absence du Personnel";
$breadcrumbs = [
    ['label' => 'Tableau de bord', 'url' => '/dashboard'],
    ['label' => 'Personnel', 'url' => '/personnel/list'],
    ['label' => 'Absences', 'url' => '/absences_personnel/list'],
    ['label' => 'Détails']
];

$statuts = [
    'demande' => ['label' => 'Demande', 'color' => 'bg-amber-100 text-amber-800 border-amber-200', 'icon' => 'fa-clock'],
    'validee' => ['label' => 'Validée', 'color' => 'bg-emerald-100 text-emerald-800 border-emerald-200', 'icon' => 'fa-check-circle'],
    'refusee' => ['label' => 'Refusée', 'color' => 'bg-rose-100 text-rose-800 border-rose-200', 'icon' => 'fa-times-circle'],
    'annulee' => ['label' => 'Annulée', 'color' => 'bg-slate-100 text-slate-800 border-slate-200', 'icon' => 'fa-ban']
];
$statutInfo = $statuts[$absence['statut']] ?? ['label' => $absence['statut'], 'color' => 'bg-gray-100 text-gray-800 border-gray-200', 'icon' => 'fa-question-circle'];

$types = [
    'conge_annuel' => 'Congé annuel',
    'conge_maladie' => 'Congé maladie',
    'conge_maternite' => 'Congé maternité',
    'conge_paternite' => 'Congé paternité',
    'conge_sans_solde' => 'Congé sans solde',
    'absence_autorisee' => 'Absence autorisée',
    'absence_non_justifiee' => 'Absence non justifiée',
    'formation' => 'Formation',
    'mission' => 'Mission',
    'autre' => 'Autre'
];
$typeLabel = $types[$absence['type_absence']] ?? $absence['type_absence'];
?>

<div class="p-4 md:p-8 space-y-6">
    <!-- Quick Actions Header -->
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6">
        <div>
            <div class="flex items-center gap-3 mb-2">
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold border <?= $statutInfo['color'] ?> shadow-sm">
                    <i class="fas <?= $statutInfo['icon'] ?> mr-1.5"></i>
                    <?= $statutInfo['label'] ?>
                </span>
                <h1 class="text-2xl md:text-3xl font-extrabold text-gray-900 tracking-tight"><?= $typeLabel ?></h1>
            </div>
            <div class="flex flex-wrap items-center gap-y-2 text-gray-500 font-medium">
                <span class="flex items-center">
                    <i class="fas fa-user-circle text-blue-500 mr-2 text-lg"></i>
                    <?= htmlspecialchars($absence['personnel_nom'] . ' ' . $absence['personnel_prenom']) ?>
                </span>
                <span class="mx-3 text-gray-300 hidden sm:inline">•</span>
                <span class="flex items-center text-sm bg-gray-100 px-2 py-0.5 rounded-md">
                    <i class="fas fa-id-card-alt mr-1.5 text-gray-400"></i>
                    <?= htmlspecialchars($absence['matricule']) ?>
                </span>
            </div>
        </div>
        
        <div class="flex items-center gap-3">
            <a href="/absences_personnel/list" class="flex items-center gap-2 px-5 py-2.5 text-sm font-bold text-gray-700 bg-white border border-gray-200 rounded-xl hover:bg-gray-50 hover:border-gray-300 transition-all shadow-sm active:scale-95">
                <i class="fas fa-arrow-left text-gray-400"></i> Retour
            </a>
            <a href="/absences_personnel/edit/<?= $absence['id'] ?>" class="flex items-center gap-2 px-5 py-2.5 text-sm font-bold text-amber-700 bg-amber-50 border border-amber-200 rounded-xl hover:bg-amber-100 transition-all shadow-sm active:scale-95">
                <i class="fas fa-edit"></i> Modifier
            </a>
            <a href="/absences_personnel/delete/<?= $absence['id'] ?>" class="flex items-center gap-2 px-5 py-2.5 text-sm font-bold text-rose-700 bg-rose-50 border border-rose-200 rounded-xl hover:bg-rose-100 transition-all shadow-sm active:scale-95">
                <i class="fas fa-trash"></i> Supprimer
            </a>
        </div>
    </div>

    <!-- Main Content Layout -->
    <div class="grid grid-cols-1 xl:grid-cols-3 gap-8">
        
        <!-- Left Column: Primary Details -->
        <div class="xl:col-span-2 space-y-8">
            
            <!-- Details Card -->
            <div class="bg-white rounded-3xl shadow-xl shadow-gray-200/50 border border-gray-100 overflow-hidden">
                <div class="px-8 py-5 border-b border-gray-50 bg-gradient-to-r from-gray-50 to-white flex items-center justify-between">
                    <h2 class="font-black text-gray-800 uppercase tracking-widest text-xs flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-blue-500"></span>
                        Informations sur l'absence
                    </h2>
                    <div class="flex items-center gap-2">
                         <span class="text-[10px] font-black uppercase text-gray-400">Durée totale</span>
                         <span class="text-sm font-bold px-3 py-1 bg-blue-600 text-white rounded-lg shadow-md shadow-blue-100">
                             <?= htmlspecialchars($absence['nb_jours']) ?> jours
                         </span>
                    </div>
                </div>
                
                <div class="p-8">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-12">
                        <!-- Dates info -->
                        <div class="space-y-6">
                            <div class="group">
                                <p class="text-[10px] text-gray-400 uppercase font-black tracking-[0.2em] mb-3">Période d'absence</p>
                                <div class="flex items-center gap-4 bg-gray-50 p-4 rounded-2xl border border-transparent group-hover:border-blue-100 group-hover:bg-blue-50/30 transition-all">
                                    <div class="flex-shrink-0 w-12 h-12 bg-white rounded-xl shadow-sm flex items-center justify-center border border-gray-100">
                                        <i class="fas fa-calendar-day text-blue-500 text-xl"></i>
                                    </div>
                                    <div>
                                        <p class="text-sm text-gray-500 font-medium">Du <?= date('d/m/Y', strtotime($absence['date_debut'])) ?></p>
                                        <p class="text-lg text-gray-900 font-black">Au <?= date('d/m/Y', strtotime($absence['date_fin'])) ?></p>
                                    </div>
                                </div>
                            </div>

                            <div class="group">
                                <p class="text-[10px] text-gray-400 uppercase font-black tracking-[0.2em] mb-3">Catégorie</p>
                                <div class="flex items-center gap-4 bg-gray-50 p-4 rounded-2xl border border-transparent group-hover:border-indigo-100 group-hover:bg-indigo-50/30 transition-all">
                                    <div class="flex-shrink-0 w-12 h-12 bg-white rounded-xl shadow-sm flex items-center justify-center border border-gray-100">
                                        <i class="fas fa-bookmark text-indigo-500 text-xl"></i>
                                    </div>
                                    <div>
                                        <p class="text-sm text-gray-500 font-medium">Type d'absence</p>
                                        <p class="text-lg text-gray-900 font-black"><?= $typeLabel ?></p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Additional Info -->
                        <div class="space-y-6">
                            <div class="group">
                                <p class="text-[10px] text-gray-400 uppercase font-black tracking-[0.2em] mb-3">Justification</p>
                                <div class="flex items-center gap-4 bg-gray-50 p-4 rounded-2xl border border-transparent group-hover:border-slate-100 group-hover:bg-slate-50/30 transition-all">
                                    <div class="flex-shrink-0 w-12 h-12 bg-white rounded-xl shadow-sm flex items-center justify-center border border-gray-100">
                                        <i class="fas fa-file-signature text-slate-500 text-xl"></i>
                                    </div>
                                    <div>
                                        <p class="text-sm text-gray-500 font-medium">Référence pièce</p>
                                        <p class="text-lg text-gray-900 font-black truncate max-w-[150px]">
                                            <?= !empty($absence['piece_justificative']) ? htmlspecialchars($absence['piece_justificative']) : 'Aucune' ?>
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <div class="group">
                                <p class="text-[10px] text-gray-400 uppercase font-black tracking-[0.2em] mb-3">Validation Temporelle</p>
                                <div class="flex items-center gap-4 bg-gray-50 p-4 rounded-2xl border border-transparent group-hover:border-emerald-100 group-hover:bg-emerald-50/30 transition-all">
                                    <div class="flex-shrink-0 w-12 h-12 bg-white rounded-xl shadow-sm flex items-center justify-center border border-gray-100">
                                        <i class="fas fa-hourglass-half text-emerald-500 text-xl"></i>
                                    </div>
                                    <div>
                                        <p class="text-sm text-gray-500 font-medium">Total décompté</p>
                                        <p class="text-lg text-gray-900 font-black"><?= htmlspecialchars($absence['nb_jours']) ?> Joursouvrés</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Motif Section -->
                    <?php if (!empty($absence['motif'])): ?>
                    <div class="mt-10">
                        <div class="relative p-6 bg-gradient-to-br from-gray-50 to-gray-100/50 rounded-3xl border border-gray-100">
                            <div class="absolute -top-3 -left-2 bg-white px-3 py-1 rounded-full border border-gray-200 shadow-sm">
                                <p class="text-[9px] text-blue-600 uppercase font-black tracking-widest leading-none">Motif explicatif</p>
                            </div>
                            <p class="text-gray-700 leading-relaxed font-medium italic text-sm">
                                "<?= nl2br(htmlspecialchars($absence['motif'])) ?>"
                            </p>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Refusal Alert (Enhanced) -->
            <?php if (!empty($absence['motif_refus'])): ?>
            <div class="group bg-gradient-to-br from-rose-50 to-white rounded-3xl border-2 border-rose-100 p-8 flex flex-col md:flex-row items-center md:items-start gap-6 shadow-xl shadow-rose-50 transition-all hover:border-rose-200">
                <div class="flex-shrink-0 w-16 h-16 bg-rose-600 rounded-2xl shadow-lg shadow-rose-200 flex items-center justify-center transform group-hover:rotate-12 transition-transform">
                    <i class="fas fa-hand-paper text-white text-2xl"></i>
                </div>
                <div class="flex-grow">
                    <div class="flex items-center gap-3 mb-2">
                        <h3 class="font-black text-rose-900 text-lg uppercase tracking-tight">Demande Refusée par la Direction</h3>
                        <span class="px-2 py-0.5 bg-rose-100 text-rose-600 text-[10px] font-bold rounded-lg uppercase">Action Requise</span>
                    </div>
                    <p class="text-sm text-rose-700/80 mb-4 font-medium leading-relaxed">Cette demande n'a pas été approuvée. Motif communiqué :</p>
                    <div class="p-5 bg-white rounded-2xl text-rose-900 font-bold text-base italic border border-rose-200 shadow-inner">
                        <i class="fas fa-quote-left text-rose-200 mr-2"></i>
                        <?= nl2br(htmlspecialchars($absence['motif_refus'])) ?>
                        <i class="fas fa-quote-right text-rose-200 ml-2"></i>
                    </div>
                </div>
            </div>
            <?php endif; ?>

        </div>

        <!-- Right Column: Context & Workflow -->
        <div class="space-y-8">
            
            <!-- Workflow Status -->
            <div class="bg-white rounded-3xl shadow-xl shadow-gray-200/50 border border-gray-100 p-8 flex flex-col h-full">
                <h3 class="font-black text-gray-800 mb-8 flex items-center gap-3 uppercase tracking-widest text-xs">
                    <span class="p-2 bg-purple-50 rounded-lg"><i class="fas fa-sync-alt text-purple-500"></i></span>
                    Workflow de validation
                </h3>

                <div class="relative space-y-12 before:absolute before:left-5 before:top-2 before:bottom-2 before:w-0.5 before:bg-gradient-to-b before:from-blue-200 before:via-gray-100 before:to-gray-50 flex-grow">
                    <!-- Step 1: Requested -->
                    <div class="relative pl-14 group">
                        <div class="absolute left-0 top-0 w-11 h-11 bg-white rounded-2xl border-2 border-blue-500 shadow-lg shadow-blue-100 flex items-center justify-center transition-all group-hover:scale-110 z-10">
                            <i class="fas fa-paper-plane text-blue-500 text-sm"></i>
                        </div>
                        <div>
                            <p class="text-[10px] text-blue-500 font-black uppercase tracking-widest mb-1">Initée par</p>
                            <p class="text-sm text-gray-900 font-bold leading-none">
                                <?= !empty($absence['demande_par_nom']) ? htmlspecialchars($absence['demande_par_nom']) : 'Administrateur' ?>
                            </p>
                            <p class="text-[11px] text-gray-400 mt-2 font-medium bg-gray-50 px-2 py-1 rounded w-fit">
                                <i class="far fa-clock mr-1"></i>
                                <?= !empty($absence['date_demande']) ? date('d/m/Y à H:i', strtotime($absence['date_demande'])) : 'Date inconnue' ?>
                            </p>
                        </div>
                    </div>

                    <!-- Step 2: Decision -->
                    <div class="relative pl-14 group">
                        <?php 
                            $isDone = !empty($absence['valide_par_nom']);
                            $isRefused = ($absence['statut'] == 'refusee');
                            $ringColor = $isDone ? ($isRefused ? 'border-rose-500 shadow-rose-100' : 'border-emerald-500 shadow-emerald-100') : 'border-gray-200 shadow-gray-50';
                            $iconColor = $isDone ? ($isRefused ? 'text-rose-500' : 'text-emerald-500') : 'text-gray-300';
                            $icon = $isDone ? ($isRefused ? 'fa-times' : 'fa-check') : 'fa-hourglass-start';
                        ?>
                        <div class="absolute left-0 top-0 w-11 h-11 bg-white rounded-2xl border-2 <?= $ringColor ?> shadow-lg flex items-center justify-center transition-all group-hover:scale-110 z-10">
                            <i class="fas <?= $icon ?> <?= $iconColor ?> text-sm"></i>
                        </div>
                        <div>
                            <p class="text-[10px] <?= $isDone ? ($isRefused ? 'text-rose-500' : 'text-emerald-500') : 'text-gray-400' ?> font-black uppercase tracking-widest mb-1">
                                <?= $isRefused ? 'Refusé par' : 'Décision Finale' ?>
                            </p>
                            <p class="text-sm <?= $isDone ? 'text-gray-900 font-bold' : 'text-gray-400 font-medium italic' ?> leading-none">
                                <?= !empty($absence['valide_par_nom']) ? htmlspecialchars($absence['valide_par_nom']) : 'En attente de revue' ?>
                            </p>
                            <?php if (!empty($absence['date_validation'])): ?>
                            <p class="text-[11px] text-gray-400 mt-2 font-medium bg-gray-50 px-2 py-1 rounded w-fit">
                                <i class="far fa-calendar-check mr-1"></i>
                                le <?= date('d/m/Y à H:i', strtotime($absence['date_validation'])) ?>
                            </p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Validation Buttons (Interactive) -->
                <?php if ($absence['statut'] == 'demande'): ?>
                <div class="mt-12 space-y-3">
                    <button type="button" class="w-full flex items-center justify-center gap-3 px-6 py-4 rounded-2xl bg-gradient-to-r from-emerald-600 to-emerald-500 text-white font-black uppercase text-xs tracking-widest hover:from-emerald-700 hover:to-emerald-600 transition-all shadow-xl shadow-emerald-100 active:scale-95 group" data-toggle="modal" data-target="#validerModal">
                        <i class="fas fa-check-double group-hover:scale-125 transition-transform"></i> 
                        Approuver
                    </button>
                    <button type="button" class="w-full flex items-center justify-center gap-3 px-6 py-4 rounded-2xl bg-white text-rose-600 border-2 border-rose-50 font-black uppercase text-xs tracking-widest hover:bg-rose-50 hover:border-rose-100 transition-all active:scale-95 group" data-toggle="modal" data-target="#refuserModal">
                        <i class="fas fa-times group-hover:rotate-90 transition-transform"></i> 
                        Rejeter
                    </button>
                </div>
                <?php endif; ?>
            </div>

            <!-- Replacement Card (Modular Design) -->
            <?php if (!empty($absence['remplace_nom'])): ?>
            <div class="group bg-gradient-to-br from-indigo-600 to-indigo-700 rounded-3xl p-8 shadow-xl shadow-indigo-100 relative overflow-hidden transition-all hover:shadow-indigo-200">
                <!-- Decorative Elements -->
                <div class="absolute -right-6 -bottom-6 w-32 h-32 bg-white/10 rounded-full blur-3xl group-hover:bg-white/20 transition-all"></div>
                <div class="absolute left-0 top-0 w-full h-1 bg-white/20"></div>

                <div class="relative z-10 flex flex-col gap-6">
                    <div class="flex items-center justify-between">
                        <h3 class="font-black text-white/90 text-[10px] uppercase tracking-widest flex items-center gap-2">
                             <i class="fas fa-user-friends"></i>
                             Logistique Remplacement
                        </h3>
                        <span class="w-8 h-8 rounded-xl bg-white/20 flex items-center justify-center border border-white/30 backdrop-blur-sm">
                            <i class="fas fa-shield-alt text-white text-xs"></i>
                        </span>
                    </div>

                    <div class="flex items-center gap-5">
                        <div class="w-14 h-14 rounded-2xl bg-white shadow-xl flex items-center justify-center text-indigo-700 font-black text-xl border-4 border-indigo-500/30">
                            <?= mb_strtoupper(mb_substr($absence['remplace_nom'], 0, 1)) ?><?= mb_strtoupper(mb_substr($absence['remplace_prenom'], 0, 1)) ?>
                        </div>
                        <div>
                            <p class="font-black text-white text-lg leading-tight tracking-tight"><?= htmlspecialchars($absence['remplace_nom'] . ' ' . $absence['remplace_prenom']) ?></p>
                            <p class="text-xs text-indigo-200 mt-1 font-bold uppercase tracking-wide">Remplaçant désigné</p>
                        </div>
                    </div>

                    <?php if (!empty($absence['commentaire_remplacement'])): ?>
                    <div class="bg-black/10 rounded-2xl p-4 border border-white/10 backdrop-blur-md">
                         <p class="text-[9px] text-indigo-200 uppercase font-black tracking-widest mb-2">Instructions</p>
                         <p class="text-sm text-indigo-50 leading-relaxed italic font-medium">
                             "<?= htmlspecialchars($absence['commentaire_remplacement']) ?>"
                         </p>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            <?php endif; ?>

        </div>

    </div>
</div>

<!-- Modals with Premium Styling -->
<div class="modal fade" id="validerModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow-2xl rounded-3xl overflow-hidden shadow-emerald-900/10">
            <div class="bg-gradient-to-r from-emerald-600 to-emerald-500 p-8 text-white">
                <div class="flex items-center justify-between mb-2">
                    <h5 class="font-black text-2xl tracking-tighter uppercase"><i class="fas fa-check-circle mr-3"></i> Approbation</h5>
                    <button type="button" class="w-8 h-8 rounded-full bg-black/10 flex items-center justify-center hover:bg-black/20 transition-all shadow-inner" data-dismiss="modal">
                        <i class="fas fa-times text-sm"></i>
                    </button>
                </div>
                <p class="text-emerald-100 font-medium text-sm">Confirmation de la demande d'absence du personnel.</p>
            </div>
            <div class="p-10 bg-white">
                <div class="space-y-8">
                    <div class="space-y-2">
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest" for="remplace_par_modal">Collaborateur remplaçant</label>
                        <div class="relative">
                            <i class="fas fa-user-tag absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"></i>
                            <select name="remplace_par_modal" id="remplace_par_modal" class="w-full bg-gray-50 border-2 border-gray-100 rounded-2xl pl-12 pr-4 py-4 text-sm font-bold text-gray-700 focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 transition-all outline-none appearance-none">
                                <option value="">Aucun remplaçant nécessaire</option>
                                <?php 
                                $personnelModel = new \App\Models\Personnel();
                                $personnels = $personnelModel->query(
                                    "SELECT id, matricule, nom, prenom FROM personnels WHERE statut = 'actif' ORDER BY nom ASC"
                                );
                                foreach ($personnels as $p): ?>
                                    <option value="<?= $p['id'] ?>">
                                        <?= htmlspecialchars($p['matricule']) ?> - <?= htmlspecialchars($p['nom'] . ' ' . $p['prenom']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <i class="fas fa-chevron-down absolute right-4 top-1/2 -translate-y-1/2 text-gray-300 pointer-events-none"></i>
                        </div>
                    </div>
                    <div class="space-y-2">
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest" for="commentaire_remplacement_modal">Notes opérationnelles</label>
                        <textarea name="commentaire_remplacement_modal" id="commentaire_remplacement_modal" class="w-full bg-gray-50 border-2 border-gray-100 rounded-2xl p-5 text-sm font-medium text-gray-700 focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 transition-all outline-none" rows="3" placeholder="Indiquez ici les instructions pour le remplaçant ou les notes de validation..."></textarea>
                    </div>
                </div>
            </div>
            <div class="p-8 bg-gray-50/80 backdrop-blur-sm flex flex-col-reverse sm:flex-row justify-end gap-3 border-t border-gray-100">
                <button type="button" class="px-8 py-4 rounded-2xl font-black uppercase text-[10px] tracking-widest text-gray-500 hover:bg-gray-200 transition-all active:scale-95" data-dismiss="modal">Annuler</button>
                <form method="POST" action="/absences_personnel/valider/<?= $absence['id'] ?>" class="flex">
                    <?= csrf_field() ?>
                    <input type="hidden" name="remplace_par" id="remplace_par_hidden">
                    <input type="hidden" name="commentaire_remplacement" id="commentaire_remplacement_hidden">
                    <button type="submit" class="w-full sm:w-auto px-10 py-4 rounded-2xl bg-emerald-600 text-white font-black uppercase text-[10px] tracking-[0.2em] hover:bg-emerald-700 transition-all shadow-xl shadow-emerald-200 active:scale-95">
                        Valider définitivement
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="refuserModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow-2xl rounded-3xl overflow-hidden shadow-rose-900/10">
            <div class="bg-gradient-to-r from-rose-600 to-rose-500 p-8 text-white">
                <div class="flex items-center justify-between mb-2">
                    <h5 class="font-black text-2xl tracking-tighter uppercase"><i class="fas fa-exclamation-triangle mr-3"></i> Rejet Formalisé</h5>
                    <button type="button" class="w-8 h-8 rounded-full bg-black/10 flex items-center justify-center hover:bg-black/20 transition-all shadow-inner" data-dismiss="modal">
                        <i class="fas fa-times text-sm"></i>
                    </button>
                </div>
                <p class="text-rose-100 font-medium text-sm">Veuillez justifier le refus de cette demande de congé.</p>
            </div>
            <div class="p-10 bg-white">
                <div class="space-y-4">
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest transition-colors group-focus-within:text-rose-500" for="motif_refus_modal">Motif du refus (Obligatoire)</label>
                    <div class="relative group">
                         <textarea name="motif_refus_modal" id="motif_refus_modal" class="w-full bg-gray-50 border-2 border-gray-100 rounded-2xl p-6 text-sm font-bold text-rose-900 placeholder:text-gray-300 focus:ring-4 focus:ring-rose-500/10 focus:border-rose-500 transition-all outline-none" rows="5" required placeholder="Exemple : Chevauchement avec un événement majeur, quota dépassé, etc."></textarea>
                         <div class="absolute right-4 bottom-4 w-2 h-2 rounded-full bg-rose-500 animate-pulse"></div>
                    </div>
                </div>
            </div>
            <div class="p-8 bg-gray-50/80 backdrop-blur-sm flex flex-col-reverse sm:flex-row justify-end gap-3 border-t border-gray-100">
                <button type="button" class="px-8 py-4 rounded-2xl font-black uppercase text-[10px] tracking-widest text-gray-500 hover:bg-gray-200 transition-all active:scale-95" data-dismiss="modal">Annuler</button>
                <form method="POST" action="/absences_personnel/refuser/<?= $absence['id'] ?>" class="flex">
                    <?= csrf_field() ?>
                    <input type="hidden" name="motif_refus" id="motif_refus_hidden">
                    <button type="submit" class="w-full sm:w-auto px-10 py-4 rounded-2xl bg-rose-600 text-white font-black uppercase text-[10px] tracking-[0.2em] hover:bg-rose-700 transition-all shadow-xl shadow-rose-200 active:scale-95">
                        Confirmer le rejet
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
// Transfert des valeurs des modals vers les champs cachés
document.getElementById('validerModal').addEventListener('shown.bs.modal', function () {
    const rInput = document.getElementById('remplace_par_modal');
    const cInput = document.getElementById('commentaire_remplacement_modal');
    document.getElementById('remplace_par_hidden').value = rInput.value;
    document.getElementById('commentaire_remplacement_hidden').value = cInput.value;
});

document.getElementById('refuserModal').addEventListener('shown.bs.modal', function () {
    document.getElementById('motif_refus_hidden').value = document.getElementById('motif_refus_modal').value;
});

// Mise à jour temps réel des inputs cachés
document.getElementById('remplace_par_modal').addEventListener('change', function () {
    document.getElementById('remplace_par_hidden').value = this.value;
});

document.getElementById('commentaire_remplacement_modal').addEventListener('input', function () {
    document.getElementById('commentaire_remplacement_hidden').value = this.value;
});

document.getElementById('motif_refus_modal').addEventListener('input', function () {
    document.getElementById('motif_refus_hidden').value = this.value;
});
</script>