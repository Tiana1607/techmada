<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TechMada RH — Tableau de bord RH</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700&family=DM+Sans:wght@300;400;500&family=DM+Mono:wght@400;500&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="<?= base_url('assets/css/style.css') ?>">
</head>
<body>
    <section id="page-dashboard-rh">
        <div class="app-wrap">
            <aside class="sidebar">
                <div class="sidebar-brand">
                    <div class="sidebar-logo-icon"><i class="bi bi-person-check"></i></div>
                    <div class="sidebar-brand-name">TechMada RH<span>Espace responsable</span></div>
                </div>
                <div class="sidebar-section">Menu</div>
                <ul class="sidebar-nav">
                    <li><a href="<?= site_url('/rh/dashboard') ?>" class="active"><i class="bi bi-grid-1x2"></i> Tableau de bord</a></li>
                    <li><a href="<?= site_url('/rh/demandes') ?>"><i class="bi bi-inbox"></i> Demandes à traiter</a></li>
                    <li><a href="<?= site_url('/rh/historique') ?>"><i class="bi bi-archive"></i> Historique</a></li>
                </ul>
                <div class="sidebar-user">
                    <div class="s-user-row">
                        <div class="avatar av-blue"><?= strtoupper(substr((string) (session()->get('prenom') ?? ''), 0, 1) . substr((string) (session()->get('nom') ?? ''), 0, 1)) ?></div>
                        <div>
                            <div class="user-name"><?= esc((string) (session()->get('prenom') ?? '')) ?> <?= esc((string) (session()->get('nom') ?? '')) ?></div>
                            <div class="user-role">Responsable RH</div>
                        </div>
                        <a href="<?= site_url('/logout') ?>" style="margin-left:auto;color:rgba(255,255,255,.25);font-size:1.1rem;text-decoration:none"><i class="bi bi-box-arrow-right"></i></a>
                    </div>
                </div>
            </aside>

            <div class="main">
                <div class="topbar">
                    <div>
                        <div class="topbar-title">Tableau de bord RH</div>
                        <div class="topbar-breadcrumb">Validation des congés</div>
                    </div>
                    <div class="topbar-actions">
                        <a href="#" class="btn-forest" onclick="loadRHSection('demandes'); return false" style="padding:7px 14px;font-size:.82rem"><i class="bi bi-inbox"></i> Traiter les demandes</a>
                    </div>
                </div>

                <div class="content">
                    <div id="rh-ajax-content" style="display:none"></div>
                    <div id="rh-dashboard-content">
                    <div class="metrics">
                        <div class="metric"><div class="metric-top"><div class="metric-icon mi-amber"><i class="bi bi-hourglass-split"></i></div></div><div class="metric-val"><?= $enAttente ?? 0 ?></div><div class="metric-label">En attente</div></div>
                        <div class="metric"><div class="metric-top"><div class="metric-icon mi-green"><i class="bi bi-calendar-check"></i></div></div><div class="metric-val"><?= $approuvee ?? 0 ?></div><div class="metric-label">Approuvées</div></div>
                        <div class="metric"><div class="metric-top"><div class="metric-icon mi-red"><i class="bi bi-x-circle"></i></div></div><div class="metric-val"><?= $refusee ?? 0 ?></div><div class="metric-label">Refusées</div></div>
                    </div>

                    <div class="data-card" style="margin:0">
                        <div class="data-card-head">
                            <h3>Demandes récentes</h3>
                            <a href="<?= site_url('/rh/demandes') ?>" style="font-size:.8rem;color:var(--forest);text-decoration:none">Voir tout</a>
                        </div>
                        <table class="tbl">
                            <thead>
                                <tr>
                                    <th>Employé</th><th>Type</th><th>Période</th><th>Durée</th><th>Statut</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($recentDemandes)): ?>
                                    <?php foreach ($recentDemandes as $demande): ?>
                                        <tr>
                                            <td><strong><?= esc((string)($demande['prenom'] ?? '')) ?> <?= esc((string)($demande['nom'] ?? '')) ?></strong><div class="td-muted" style="font-size:.78rem"><?= esc((string)($demande['departement_nom'] ?? 'N/A')) ?></div></td>
                                            <td><span class="type-badge t-annuel"><?= esc((string)($demande['type_libelle'] ?? 'Congé')) ?></span></td>
                                            <td class="td-muted"><?= date('d/m/Y', strtotime($demande['date_debut'])) ?> – <?= date('d/m/Y', strtotime($demande['date_fin'])) ?></td>
                                            <td class="td-mono"><?= esc((string)($demande['nb_jours'] ?? '0')) ?> j</td>
                                            <td><span class="statut <?= $demande['statut'] === 'approuvee' ? 's-approuvee' : ($demande['statut'] === 'refusee' ? 's-refusee' : 's-attente') ?>"><?= esc((string)($demande['statut'] ?? '')) ?></span></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr><td colspan="5" class="text-center" style="padding:20px;color:#999">Aucune demande</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                    </div>

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
                </div>
                <div class="footer-app"><i class="bi bi-c-circle"></i> 2026 <span>TechMada RH (4153-4286)</span></div>
            </div>
        </div>
    </section>

    <script>
        let currentRHSection = null;
        let currentRefuseDemandeId = null;

        function updateRHNavActive(section) {
            const links = document.querySelectorAll('.sidebar-nav a');
            links.forEach(link => {
                link.classList.remove('active');
            });
            if (section === 'demandes') {
                links.forEach(link => {
                    if (link.getAttribute('href').includes('/rh/demandes')) {
                        link.classList.add('active');
                    }
                });
            } else if (section === 'historique') {
                links.forEach(link => {
                    if (link.getAttribute('href').includes('/rh/historique')) {
                        link.classList.add('active');
                    }
                });
            } else {
                links.forEach(link => {
                    if (link.getAttribute('href').includes('/rh/dashboard')) {
                        link.classList.add('active');
                    }
                });
            }
        }

        function loadRHSection(section, params = {}) {
            currentRHSection = section;
            const target = document.getElementById('rh-ajax-content');
            const dashboard = document.getElementById('rh-dashboard-content');
            dashboard.style.display = 'none';
            target.style.display = 'block';
            updateRHNavActive(section);

            const query = new URLSearchParams(params).toString();
            fetch('<?= site_url('/rh/ajax/') ?>' + section + (query ? '?' + query : ''))
                .then(response => response.text())
                .then(html => {
                    target.innerHTML = html;
                })
                .catch(() => {
                    target.innerHTML = '<div class="flash flash-error">Impossible de charger la section RH.</div>';
                });
        }

        function showRHDashboard() {
            currentRHSection = null;
            document.getElementById('rh-ajax-content').style.display = 'none';
            document.getElementById('rh-ajax-content').innerHTML = '';
            document.getElementById('rh-dashboard-content').style.display = 'block';
            updateRHNavActive('dashboard');
        }

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
                        closeRHModal();
                        if (currentRHSection === 'demandes') {
                            loadRHSection('demandes');
                        } else {
                            window.location.reload();
                        }
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
                        if (currentRHSection === 'demandes') {
                            loadRHSection('demandes');
                        } else {
                            window.location.reload();
                        }
                    }
                })
                .catch(() => {});
        }

        document.addEventListener('DOMContentLoaded', function () {
            const links = document.querySelectorAll('.sidebar-nav a');
            links.forEach(link => {
                link.addEventListener('click', function (e) {
                    const href = this.getAttribute('href');
                    if (href.includes('/rh/dashboard')) {
                        e.preventDefault();
                        showRHDashboard();
                    } else if (href.includes('/rh/demandes')) {
                        e.preventDefault();
                        loadRHSection('demandes');
                    } else if (href.includes('/rh/historique')) {
                        e.preventDefault();
                        loadRHSection('historique');
                    }
                });
            });
        });
    </script>
</body>
</html>
