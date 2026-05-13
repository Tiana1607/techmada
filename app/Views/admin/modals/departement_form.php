<div class="modal-form-card">
    <?php $departement = $departement ?? []; ?>
    <div class="data-card-head">
        <h3><?= !empty($departement) ? 'Modifier le département' : 'Nouveau département' ?></h3>
        <button type="button" class="modal-close-btn" onclick="closeCrudModal()">×</button>
    </div>

    <?php
        $isEdit = !empty($departement);
        $action = $isEdit ? site_url('/admin/departements/' . $departement['id']) : site_url('/admin/departements');
    ?>

    <form id="crud-form" action="<?= $action ?>" method="post" onsubmit="return submitCrudForm(this, 'departements')">
        <?= csrf_field() ?>
        <div class="crud-form-errors"></div>

        <div class="mb-3">
            <label class="form-label">Nom</label>
            <input type="text" name="nom" class="form-control" value="<?= esc((string)($departement['nom'] ?? '')) ?>" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Description</label>
            <textarea name="description" class="form-control" rows="4"><?= esc((string)($departement['description'] ?? '')) ?></textarea>
        </div>

        <div class="modal-actions">
            <button type="button" class="btn btn-outline-secondary" onclick="closeCrudModal()">Annuler</button>
            <button type="submit" class="btn btn-primary">Enregistrer</button>
        </div>
    </form>
</div>
