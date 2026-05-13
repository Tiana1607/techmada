<!-- Section Départements -->
<div class="data-card" style="margin:0">
    <div class="data-card-head">
        <h3><i class="bi bi-building" style="margin-right:8px"></i>Gestion des départements</h3>
        <div style="display:flex;gap:.5rem;align-items:center">
            <a href="#" class="btn-forest" style="font-size:.8rem;padding:5px 10px" onclick="openCrudForm('departement', null, 'departements'); return false">Ajouter</a>
            <a href="#" class="btn-forest" style="font-size:.8rem;padding:5px 10px" onclick="loadSection('departements'); return false">Actualiser</a>
        </div>
    </div>
    <div class="table-responsive">
        <table class="tbl">
            <thead>
                <tr>
                    <th>Département</th>
                    <th>Description</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($departements)): ?>
                    <?php foreach ($departements as $dept): ?>
                        <tr>
                            <td>
                                <strong><?= esc((string)($dept['nom'] ?? '')) ?></strong>
                            </td>
                            <td style="font-size:.85rem;max-width:300px">
                                <?= esc((string)($dept['description'] ?? '')) ?>
                            </td>
                            <td>
                                          <a href="#" 
                                              class="btn-xs btn-primary edit" onclick="openCrudForm('departement', <?= (int) $dept['id'] ?>, 'departements'); return false">
                                    <i class="bi bi-pencil"></i> Éditer
                                </a>
                                <a href="#" class="btn-xs btn-danger" onclick="deleteDepartment(<?= $dept['id'] ?>); return false">
                                    <i class="bi bi-trash"></i> Supprimer
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="3" class="text-center" style="padding:20px;color:#999">
                            Aucun département
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <div style="padding:1rem;border-top:1px solid var(--border)">
        <a href="#" class="btn-forest" style="font-size:.85rem;padding:8px 14px" onclick="showDepartmentForm(); return false">
            <i class="bi bi-plus"></i> Nouveau département
        </a>
    </div>
</div>
