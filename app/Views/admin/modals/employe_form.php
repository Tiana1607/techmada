<div class="modal-form-card">
    <?php $employe = $employe ?? []; ?>
    <?php $departements = $departements ?? []; ?>
    <div class="data-card-head">
        <h3><?= !empty($employe) ? 'Modifier l\'employé' : 'Nouvel employé' ?></h3>
        <button type="button" class="modal-close-btn" onclick="closeCrudModal()">×</button>
    </div>

    <?php
        $isEdit = !empty($employe);
        $action = $isEdit ? site_url('/admin/employes/' . $employe['id']) : site_url('/admin/employes');
    ?>

    <form id="crud-form" action="<?= $action ?>" method="post" onsubmit="return submitCrudForm(this, 'employes')">
        <?= csrf_field() ?>
        <div class="crud-form-errors"></div>
        <?php if ($isEdit): ?>
            <input type="hidden" name="_method" value="POST">
        <?php endif; ?>

        <div class="form-grid">
            <div class="mb-3">
                <label class="form-label">Nom</label>
                <input type="text" name="nom" class="form-control" value="<?= esc((string)($employe['nom'] ?? '')) ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Prénom</label>
                <input type="text" name="prenom" class="form-control" value="<?= esc((string)($employe['prenom'] ?? '')) ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-control" value="<?= esc((string)($employe['email'] ?? '')) ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Rôle</label>
                <select name="role" class="form-select" required>
                    <?php $currentRole = $employe['role'] ?? 'employe'; ?>
                    <option value="admin" <?= $currentRole === 'admin' ? 'selected' : '' ?>>Admin</option>
                    <option value="rh" <?= $currentRole === 'rh' ? 'selected' : '' ?>>RH</option>
                    <option value="employe" <?= $currentRole === 'employe' ? 'selected' : '' ?>>Employé</option>
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label">Département</label>
                <select name="departement_id" class="form-select">
                    <option value="">Aucun</option>
                    <?php $currentDepartement = (string)($employe['departement_id'] ?? ''); ?>
                    <?php foreach ($departements as $departement): ?>
                        <option value="<?= esc((string)$departement['id']) ?>" <?= $currentDepartement === (string)$departement['id'] ? 'selected' : '' ?>>
                            <?= esc((string)$departement['nom']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label">Mot de passe <?= $isEdit ? '(laisser vide pour garder l\'actuel)' : '' ?></label>
                <input type="password" name="password" class="form-control" <?= $isEdit ? '' : 'required' ?>>
            </div>
        </div>

        <div class="modal-actions">
            <button type="button" class="btn btn-outline-secondary" onclick="closeCrudModal()">Annuler</button>
            <button type="submit" class="btn btn-primary">Enregistrer</button>
        </div>
    </form>
</div>
