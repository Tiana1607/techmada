<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TechMada RH — Demandes RH</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700&family=DM+Sans:wght@300;400;500&family=DM+Mono:wght@400;500&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="<?= base_url('assets/css/style.css') ?>">
</head>
<body>
    <section id="page-liste-rh">
        <div class="app-wrap">
            <aside class="sidebar">
                <div class="sidebar-brand">
                    <div class="sidebar-logo-icon"><i class="bi bi-person-check"></i></div>
                    <div class="sidebar-brand-name">TechMada RH<span>Espace responsable</span></div>
                </div>
                <div class="sidebar-section">Menu</div>
                <ul class="sidebar-nav">
                    <li><a href="<?= site_url('/rh/dashboard') ?>"><i class="bi bi-grid-1x2"></i> Tableau de bord</a></li>
                    <li><a href="<?= site_url('/rh/demandes') ?>" class="active"><i class="bi bi-inbox"></i> Demandes à traiter</a></li>
                    <li><a href="<?= site_url('/rh/historique') ?>"><i class="bi bi-archive"></i> Historique</a></li>
                </ul>
                <div class="sidebar-user">
                    <div class="s-user-row">
                        <div class="avatar av-blue"><?= strtoupper(substr((string)(session()->get('prenom') ?? ''), 0, 1) . substr((string)(session()->get('nom') ?? ''), 0, 1)) ?></div>
                        <div>
                            <div class="user-name"><?= esc((string)(session()->get('prenom') ?? '')) ?> <?= esc((string)(session()->get('nom') ?? '')) ?></div>
                            <div class="user-role">Responsable RH</div>
                        </div>
                    </div>
                </div>
            </aside>

            <div class="main">
                <div class="topbar">
                    <div>
                        <div class="topbar-title">Demandes à traiter</div>
                        <div class="topbar-breadcrumb"><a href="<?= site_url('/rh/dashboard') ?>">Accueil</a> <i class="bi bi-chevron-right" style="font-size:.6rem"></i> Demandes</div>
                    </div>
                    <div class="topbar-actions">
                        <span style="font-size:.8rem;color:var(--muted);background:var(--warn-bg);border:1px solid var(--warn-br);border-radius:6px;padding:5px 10px;display:flex;align-items:center;gap:5px;color:var(--warn)"><i class="bi bi-hourglass-split"></i> <?= $totalEnAttente ?? 0 ?> en attente</span>
                    </div>
                </div>

                <div class="content">
                    <div class="metrics">
                        <div class="metric"><div class="metric-top"><div class="metric-icon mi-amber"><i class="bi bi-hourglass-split"></i></div></div><div class="metric-val"><?= $totalEnAttente ?? 0 ?></div><div class="metric-label">En attente</div></div>
                        <div class="metric"><div class="metric-top"><div class="metric-icon mi-green"><i class="bi bi-calendar-check"></i></div></div><div class="metric-val"><?= $totalApprouvee ?? 0 ?></div><div class="metric-label">Approuvées</div></div>
                        <div class="metric"><div class="metric-top"><div class="metric-icon mi-red"><i class="bi bi-x-circle"></i></div></div><div class="metric-val"><?= $totalRefusee ?? 0 ?></div><div class="metric-label">Refusées</div></div>
                    </div>

                    <div class="data-card" style="margin:0">
                        <div class="data-card-head">
                            <h3>Toutes les demandes</h3>
                        </div>
                        <table class="tbl">
                            <thead>
                                <tr>
                                    <th>Employé</th><th>Type</th><th>Période</th><th>Durée</th><th>Solde dispo</th><th>Statut</th><th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($demandes)): ?>
                                    <?php foreach ($demandes as $demande): ?>
                                        <tr>
                                            <td>
                                                <div class="profile-row">
                                                    <div class="avatar av-green" style="width:32px;height:32px;font-size:.7rem"><?= strtoupper(substr((string)($demande['prenom'] ?? ''),0,1) . substr((string)($demande['nom'] ?? ''),0,1)) ?></div>
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
                                                        <button class="btn-sm btn-refuse" type="button" onclick="openRHRefuseModal(<?= (int) $demande['id'] ?>, '<?= esc((string)($demande['prenom'] ?? '')) ?> <?= esc((string)($demande['nom'] ?? '')) ?>')"><i class="bi bi-x-lg"></i> Refuser</button>
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

                    
                </div>
                <div class="footer-app"><i class="bi bi-c-circle"></i> 2026 <span>TechMada RH</span></div>
            </div>
        </div>
    </section>

    <div id="rh-modal" class="crud-modal" style="display:none" onclick="if (event.target === this) closeRHModal();">
        <div class="crud-modal-dialog" style="width:min(620px,100%)">
            <div class="modal-form-card">
                <div class="data-card-head">
                    <h3>Refuser la demande</h3>
                    <button type="button" class="modal-close-btn" onclick="closeRHModal()">×</button>
                </div>
                <div class="crud-form-errors"></div>
                <p id="rh-modal-text" style="margin:.5rem 0 1rem;color:var(--ink)"></p>
                <div class="mb-3">
                    <label class="form-label">Commentaire RH</label>
                    <textarea id="rh-commentaire" class="form-control" rows="4" placeholder="Motif du refus..."></textarea>
                </div>
                <div class="modal-actions">
                    <button type="button" class="btn btn-outline-secondary" onclick="closeRHModal()">Annuler</button>
                    <button type="button" class="btn btn-danger" onclick="submitRHRefuse()">Confirmer le refus</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        let currentRefuseDemandeId = null;

        function openRHRefuseModal(demandeId, nomComplet) {
            currentRefuseDemandeId = demandeId;
            document.getElementById('rh-modal-text').textContent = 'Refus de la demande de ' + nomComplet + '.';
            document.getElementById('rh-commentaire').value = '';
            document.getElementById('rh-modal').style.display = 'flex';
            return false;
        }

        function closeRHModal() {
            currentRefuseDemandeId = null;
            document.getElementById('rh-modal').style.display = 'none';
        }

        function submitRHRefuse() {
            if (!currentRefuseDemandeId) {
                return;
            }

            const commentaire = document.getElementById('rh-commentaire').value;
            const formData = new FormData();
            formData.append('commentaire_rh', commentaire);

            fetch('<?= site_url('/rh/demandes/') ?>' + currentRefuseDemandeId + '/refuser', {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: formData
            })
                .then(response => response.json())
                .then(payload => {
                    if (payload.success) {
                        window.location.reload();
                    }
                })
                .catch(() => {});
        }

        function approveDemande(demandeId) {
            fetch('<?= site_url('/rh/demandes/') ?>' + demandeId + '/approuver', {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
                .then(response => response.json())
                .then(payload => {
                    if (payload.success) {
                        window.location.reload();
                    }
                })
                .catch(() => {});
        }
    </script>
</body>
</html>
