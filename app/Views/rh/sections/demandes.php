<div class="data-card" style="margin:0">
    <div class="data-card-head">
        <h3>Toutes les demandes</h3>
        <div style="display:flex;gap:.5rem;align-items:center">
            <select class="f-select" style="font-size:.8rem;padding:6px 10px;width:auto" onchange="loadRHSection('demandes', { statut: this.value })">
                <option value="en_attente" <?= ($statutActuel ?? '') === 'en_attente' ? 'selected' : '' ?>>En attente</option>
                <option value="approuvee" <?= ($statutActuel ?? '') === 'approuvee' ? 'selected' : '' ?>>Approuvées</option>
                <option value="refusee" <?= ($statutActuel ?? '') === 'refusee' ? 'selected' : '' ?>>Refusées</option>
                <option value="" <?= ($statutActuel ?? '') === '' ? 'selected' : '' ?>>Toutes</option>
            </select>
        </div>
    </div>
    <table class="tbl">
        <thead>
            <tr>
                <th>Employé</th>
                <th>Type</th>
                <th>Période</th>
                <th>Durée</th>
                <th>Solde dispo</th>
                <th>Statut</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($demandes)): ?>
                <?php foreach ($demandes as $demande): ?>
                    <tr>
                        <td>
                            <div class="profile-row">
                                <div class="avatar av-green" style="width:32px;height:32px;font-size:.7rem"><?= strtoupper(substr((string)($demande['prenom'] ?? ''), 0, 1) . substr((string)($demande['nom'] ?? ''), 0, 1)) ?></div>
                                <div class="profile-info">
                                    <div class="pname"><?= esc((string)($demande['prenom'] ?? '')) ?> <?= esc((string)($demande['nom'] ?? '')) ?></div>
                                    <div class="pdept"><?= esc((string)($demande['departement_nom'] ?? 'N/A')) ?></div>
                                </div>
                            </div>
                        </td>
                        <td><span class="type-badge t-annuel"><?= esc((string)($demande['type_libelle'] ?? 'Congé')) ?></span></td>
                        <td class="td-muted" style="font-size:.8rem"><?= date('d/m/Y', strtotime($demande['date_debut'])) ?> – <?= date('d/m/Y', strtotime($demande['date_fin'])) ?></td>
                        <td class="td-mono"><?= esc((string)($demande['nb_jours'] ?? '0')) ?> j</td>
                        <td>
                            <span style="font-family:'DM Mono',monospace;font-size:.82rem;color:<?= ($demande['jours_restants'] ?? 0) >= 0 ? 'var(--success)' : 'var(--danger)' ?>;font-weight:500"><?= esc((string)($demande['jours_restants'] ?? '0')) ?> j</span>
                        </td>
                        <td><span class="statut <?= $demande['statut'] === 'approuvee' ? 's-approuvee' : ($demande['statut'] === 'refusee' ? 's-refusee' : 's-attente') ?>"><?= esc((string)($demande['statut'] ?? '')) ?></span></td>
                        <td>
                            <?php if (($demande['statut'] ?? '') === 'en_attente'): ?>
                                <div class="action-btns">
                                    <button class="btn-sm btn-approve" type="button" onclick="approveDemande(<?= (int) $demande['id'] ?>)"><i class="bi bi-check-lg"></i> Approuver</button>
                                    <button class="btn-sm btn-refuse" type="button" onclick="openRefuseModal(<?= (int) $demande['id'] ?>, '<?= esc((string)($demande['prenom'] ?? '')) ?> <?= esc((string)($demande['nom'] ?? '')) ?>')"><i class="bi bi-x-lg"></i> Refuser</button>
                                </div>
                            <?php else: ?>
                                <span class="td-muted" style="font-size:.75rem">Traité</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="7" class="text-center" style="padding:20px;color:#999">Aucune demande</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
