<div class="page-header">
    <h1>Inscription d'un élève</h1>
</div>

<form method="POST" action="<?= url('eleves/inscription') ?>" class="form">
    <?= csrf_field() ?>
    <!-- Formulaire d'inscription à compléter -->
    <div class="form-actions">
        <button type="submit" class="btn btn-primary">Enregistrer l'inscription</button>
        <a href="<?= url('eleves/list') ?>" class="btn btn-secondary">Annuler</a>
    </div>
</form>

