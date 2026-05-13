<!-- Section Types de Congé -->
<div class="data-card" style="margin:0">
    <div class="data-card-head">
        <h3><i class="bi bi-tags" style="margin-right:8px"></i>Gestion des types de congé</h3>
        <div style="display:flex;gap:.5rem;align-items:center">
            <a href="#" class="btn-forest" style="font-size:.8rem;padding:5px 10px" onclick="openCrudForm('type', null, 'types'); return false">Ajouter</a>
            <a href="#" class="btn-forest" style="font-size:.8rem;padding:5px 10px" onclick="loadSection('types'); return false">Actualiser</a>
        </div>
    </div>
    <div class="table-responsive">
        <table class="tbl">
            <thead>
                <tr>
                    <th>Type</th>
                    <th>Jours annuels</th>
                    <th>Description</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($types)): ?>
                    <?php foreach ($types as $type): ?>
                        <tr>
                            <td>
                                <strong><?= esc((string)($type['libelle'] ?? '')) ?></strong>
                            </td>
                            <td class="td-mono">
                                <span class="badge bg-info"><?= (int)($type['jours_annuels'] ?? 0) ?> j</span>
                            </td>
                            <td style="font-size:.85rem;max-width:250px">
                                <?= esc((string)($type['description'] ?? '')) ?>
                            </td>
                            <td>
                                          <a href="#" 
                                              class="btn-xs btn-primary edit" onclick="openCrudForm('type', <?= (int) $type['id'] ?>, 'types'); return false">
                                    <i class="bi bi-pencil"></i> Éditer
                                </a>
                                <a href="#" class="btn-xs btn-danger" onclick="deleteType(<?= $type['id'] ?>); return false">
                                    <i class="bi bi-trash"></i> Supprimer
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="4" class="text-center" style="padding:20px;color:#999">
                            Aucun type de congé
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <div style="padding:1rem;border-top:1px solid var(--border)">
        <a href="#" class="btn-forest" style="font-size:.85rem;padding:8px 14px" onclick="showTypeForm(); return false">
            <i class="bi bi-plus"></i> Nouveau type
        </a>
    </div>
</div>
