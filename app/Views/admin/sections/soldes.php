<!-- Section Soldes Annuels -->
<div class="data-card" style="margin:0">
    <div class="data-card-head">
        <h3><i class="bi bi-sliders" style="margin-right:8px"></i>Soldes annuels <?= date('Y') ?></h3>
        <a href="#" class="btn-forest" style="font-size:.8rem;padding:5px 10px" onclick="loadSection('soldes')">Actualiser</a>
    </div>
    <div class="table-responsive">
        <table class="tbl">
            <thead>
                <tr>
                    <th>Employé</th>
                    <th>Type</th>
                    <th>Attribués</th>
                    <th>Pris</th>
                    <th>Restants</th>
                    <th style="text-align:center">Alerte</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($soldes)): ?>
                    <?php foreach ($soldes as $solde): ?>
                        <tr>
                            <td>
                                <strong><?= esc((string)($solde['prenom'] . ' ' . $solde['nom'])) ?></strong>
                            </td>
                            <td>
                                <span class="type-badge" style="font-size:.8rem">
                                    <?= esc((string)($solde['libelle'] ?? '')) ?>
                                </span>
                            </td>
                            <td class="td-mono">
                                <span class="badge bg-info"><?= (float)($solde['jours_attribues']) ?> j</span>
                            </td>
                            <td class="td-mono">
                                <span class="badge bg-warning"><?= (float)($solde['jours_pris']) ?> j</span>
                            </td>
                            <td class="td-mono">
                                <span class="badge <?= $solde['jours_restants'] <= 2 ? 'bg-danger' : 'bg-success' ?>">
                                    <?= (float)($solde['jours_restants']) ?> j
                                </span>
                            </td>
                            <td style="text-align:center">
                                <?php if ($solde['jours_restants'] <= 2): ?>
                                    <i class="bi bi-exclamation-circle-fill" style="color:var(--warn);font-size:1.2rem" title="Solde critique"></i>
                                <?php else: ?>
                                    <span style="color:#999">—</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" class="text-center" style="padding:20px;color:#999">
                            Aucun solde
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <div style="padding:1rem;border-top:1px solid var(--border)">
        <button class="btn-forest" style="font-size:.85rem;padding:8px 14px;border:none;cursor:pointer" onclick="initializeSoldes(); return false">
            <i class="bi bi-arrow-clockwise"></i> Réinitialiser les soldes
        </button>
    </div>
</div>
