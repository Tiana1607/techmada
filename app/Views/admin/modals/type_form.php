<div class="modal-form-card">
    <?php $type = $type ?? []; ?>
    <div class="data-card-head">
        <h3><?= !empty($type) ? 'Modifier le type de congé' : 'Nouveau type de congé' ?></h3>
        <button type="button" class="modal-close-btn" onclick="closeCrudModal()">×</button>
    </div>

    <?php
        $isEdit = !empty($type);
        $action = $isEdit ? site_url('/admin/types/' . $type['id']) : site_url('/admin/types');
    ?>

    <form id="crud-form" action="<?= $action ?>" method="post" onsubmit="return submitCrudForm(this, 'types')">
        <?= csrf_field() ?>
        <div class="crud-form-errors"></div>

        <div class="mb-3">
            <label class="form-label">Libellé</label>
            <input type="text" name="libelle" class="form-control" value="<?= esc((string)($type['libelle'] ?? '')) ?>" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Jours annuels</label>
            <input type="number" step="1" min="0" name="jours_annuels" class="form-control" value="<?= esc((string)($type['jours_annuels'] ?? '0')) ?>" required>
        </div>

        <div class="form-check mb-3">
            <input type="checkbox" name="deductible" value="1" class="form-check-input" id="deductibleCheck" <?= !empty($type['deductible']) ? 'checked' : '' ?>>
            <label class="form-check-label" for="deductibleCheck">Déductible du solde</label>
        </div>

        <div class="modal-actions">
            <button type="button" class="btn btn-outline-secondary" onclick="closeCrudModal()">Annuler</button>
            <button type="submit" class="btn btn-primary">Enregistrer</button>
        </div>
    </form>
</div>
