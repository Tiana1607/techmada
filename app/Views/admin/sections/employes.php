<!-- Section Employés -->
<div class="data-card" style="margin:0">
    <div class="data-card-head">
        <h3><i class="bi bi-people" style="margin-right:8px"></i>Gestion des employés</h3>
        <div style="display:flex;gap:.5rem;align-items:center">
            <a href="#" class="btn-forest" style="font-size:.8rem;padding:5px 10px" onclick="openCrudForm('employe', null, 'employes'); return false">Ajouter</a>
            <a href="#" class="btn-forest" style="font-size:.8rem;padding:5px 10px" onclick="loadSection('employes'); return false">Actualiser</a>
        </div>
    </div>
    <div class="table-responsive">
        <table class="tbl">
            <thead>
                <tr>
                    <th>Nom</th>
                    <th>Email</th>
                    <th>Rôle</th>
                    <th>Département</th>
                    <th>Statut</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($employes)): ?>
                    <?php foreach ($employes as $emp): ?>
                        <tr>
                            <td>
                                <strong><?= esc((string)($emp['prenom'] . ' ' . $emp['nom'])) ?></strong>
                            </td>
                            <td style="font-size:.85rem"><?= esc((string)($emp['email'] ?? '')) ?></td>
                            <td>
                                <span class="badge bg-<?php 
                                    echo $emp['role'] === 'admin' ? 'danger' : 
                                         ($emp['role'] === 'rh' ? 'info' : 'secondary');
                                ?>">
                                    <?= ucfirst($emp['role']) ?>
                                </span>
                            </td>
                            <td style="font-size:.85rem"><?= esc((string)($emp['departement_id'] ?? 'N/A')) ?></td>
                            <td>
                                <span class="badge <?= $emp['actif'] ? 'bg-success' : 'bg-danger' ?>">
                                    <?= $emp['actif'] ? 'Actif' : 'Inactif' ?>
                                </span>
                            </td>
                            <td>
                                          <a href="#" 
                                              class="btn-xs btn-primary edit" onclick="openCrudForm('employe', <?= (int) $emp['id'] ?>, 'employes'); return false">
                                    <i class="bi bi-pencil"></i> Éditer
                                </a>
                                <?php if ($emp['role'] !== 'admin'): ?>
                                    <a href="#" class="btn-xs btn-danger" onclick="disableEmployee(<?= $emp['id'] ?>); return false">
                                        <i class="bi bi-trash"></i> Désactiver
                                    </a>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" class="text-center" style="padding:20px;color:#999">
                            Aucun employé
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
