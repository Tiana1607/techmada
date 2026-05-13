<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TechMada RH — Dashboard Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet" />
    <link
        href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700&family=DM+Sans:wght@300;400;500&family=DM+Mono:wght@400;500&display=swap"
        rel="stylesheet" />
    <link rel="stylesheet" href="<?= base_url('assets/css/style.css') ?>">

</head>

<body>
    <!-- ╔══════════════════════════════════════════════════════════════╗
     ║  PAGE 6 — DASHBOARD ADMIN  (admin/dashboard.php)            ║
     ╚══════════════════════════════════════════════════════════════╝ -->
    <section id="page-dashboard-admin">
        <div class="app-wrap">

            <aside class="sidebar">
                <div class="sidebar-brand">
                    <div class="sidebar-logo-icon" style="background:var(--ink);border:1px solid rgba(255,255,255,.15)">
                        <i class="bi bi-shield-check" style="color:var(--leaf)"></i>
                    </div>
                    <a href="#" onclick="showDashboard(); return false" style="text-decoration:none;color:inherit">
                        <div class="sidebar-brand-name">TechMada RH
                            <span>Administration</span>
                        </div>
                    </a>
                </div>
                <div class="sidebar-section">Gestion</div>
                <ul class="sidebar-nav">
                    <li><a href="<?= site_url('/admin/dashboard') ?>" class="active"><i class="bi bi-speedometer2"></i>
                            Vue d'ensemble</a></li>
                    <li>
                        <a href="<?= site_url('/rh/demandes') ?>">
                            <i class="bi bi-inbox"></i> Toutes les demandes
                            <span class="nav-badge alert"><?= $nbEnAttente ?? 0 ?></span>
                        </a>
                    </li>
                    <li><a href="<?= site_url('/admin/employes') ?>"><i class="bi bi-people"></i> Employés</a></li>
                    <li><a href="<?= site_url('/admin/departements') ?>"><i class="bi bi-building"></i> Départements</a>
                    </li>
                    <li><a href="<?= site_url('/admin/types') ?>"><i class="bi bi-tags"></i> Types de congé</a></li>
                    <li><a href="<?= site_url('/admin/soldes') ?>"><i class="bi bi-sliders"></i> Soldes annuels</a></li>
                </ul>
                <div class="sidebar-user">
                    <div class="s-user-row">
                        <div class="avatar" style="background:#5a2d82;width:32px;height:32px;font-size:.7rem">
                            <?= strtoupper(substr(session()->get('prenom') ?? '', 0, 1) . substr(session()->get('nom') ?? '', 0, 1)) ?>
                        </div>
                        <div>
                            <div class="user-name"><?= esc((string) (session()->get('prenom') ?? '')) ?>
                                <?= esc((string) (session()->get('nom') ?? '')) ?>
                            </div>
                            <div class="user-role">Administrateur</div>
                        </div>
                        <a href="<?= site_url('/logout') ?>"
                            style="margin-left:auto;color:rgba(255,255,255,.25);font-size:1.1rem;text-decoration:none;cursor:pointer"
                            title="Déconnexion"><i class="bi bi-box-arrow-right"></i></a>
                    </div>
                </div>
            </aside>

            <div class="main">
                <div class="topbar">
                    <div>
                        <div class="topbar-title">Vue d'ensemble</div>
                        <div class="topbar-breadcrumb">Administration</div>
                    </div>
                    <div class="topbar-actions">
                        <a href="#" class="btn-forest" onclick="openCrudForm('employe', null, 'dashboard'); return false"
                            style="padding:7px 14px;font-size:.82rem"><i class="bi bi-person-plus"></i> Ajouter un
                            employé</a>
                    </div>
                </div>

                <div class="content">

                    <div id="ajax-content" style="display:none"></div>

                    <div id="dashboard-content">
                        <div class="metrics">
                            <div class="metric">
                                <div class="metric-top">
                                    <div class="metric-icon mi-forest"><i class="bi bi-people"></i></div>
                                </div>
                                <div class="metric-val"><?= $nbEmployes ?? 0 ?></div>
                                <div class="metric-label">Employés</div>
                            </div>
                            <div class="metric">
                                <div class="metric-top">
                                    <div class="metric-icon mi-amber"><i class="bi bi-hourglass-split"></i></div>
                                </div>
                                <div class="metric-val"><?= $nbEnAttente ?? 0 ?></div>
                                <div class="metric-label">Demandes en attente</div>
                            </div>
                            <div class="metric">
                                <div class="metric-top">
                                    <div class="metric-icon mi-green"><i class="bi bi-calendar-check"></i></div>
                                </div>
                                <div class="metric-val"><?= $nbApprouvee ?? 0 ?></div>
                                <div class="metric-label">Approuvées</div>
                            </div>
                            <div class="metric">
                                <div class="metric-top">
                                    <div class="metric-icon mi-blue"><i class="bi bi-building"></i></div>
                                </div>
                                <div class="metric-val"><?= $nbDepartements ?? 0 ?></div>
                                <div class="metric-label">Départements</div>
                            </div>
                            <div class="metric">
                                <div class="metric-top">
                                    <div class="metric-icon mi-red"><i class="bi bi-tags"></i></div>
                                </div>
                                <div class="metric-val"><?= $nbTypesCong ?? 0 ?></div>
                                <div class="metric-label">Types de congé</div>
                            </div>
                        </div>

                        <div style="display:grid;grid-template-columns:1fr 320px;gap:1.5rem;align-items:start">

                            <div class="data-card" style="margin:0">
                                <div class="data-card-head">
                                    <h3>Demandes récentes</h3>
                                    <a href="<?= site_url('/rh/demandes') ?>"
                                        style="font-size:.8rem;color:var(--forest);text-decoration:none">Tout voir
                                        →</a>
                                </div>
                                <table class="tbl">
                                    <thead>
                                        <tr>
                                            <th>Employé</th>
                                            <th>Type</th>
                                            <th>Durée</th>
                                            <th>Statut</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (!empty($dernieresDemandes)): ?>
                                            <?php foreach ($dernieresDemandes as $demande): ?>
                                                <?php
                                                // Calculer le nombre de jours
                                                $debut = new DateTime($demande['date_debut']);
                                                $fin = new DateTime($demande['date_fin']);
                                                $nbJours = 0;
                                                $current = clone $debut;
                                                while ($current <= $fin) {
                                                    $dayOfWeek = (int) $current->format('N');
                                                    if ($dayOfWeek < 6) {
                                                        $nbJours++;
                                                    }
                                                    $current->modify('+1 day');
                                                }

                                                // Initialess de l'employé
                                                $initials = strtoupper(substr($demande['employe_id'], 0, 1));

                                                // Couleur du badge
                                                if ($demande['type_conge_id'] == 1) {
                                                    $typeColor = 't-annuel';
                                                    $typeLabel = 'Annuel';
                                                } elseif ($demande['type_conge_id'] == 2) {
                                                    $typeColor = 't-maladie';
                                                    $typeLabel = 'Maladie';
                                                } elseif ($demande['type_conge_id'] == 3) {
                                                    $typeColor = 't-sans-solde';
                                                    $typeLabel = 'Sans solde';
                                                } else {
                                                    $typeColor = 't-autre';
                                                    $typeLabel = 'Autre';
                                                }

                                                // Classe du statut
                                                if ($demande['statut'] == 'approuvee') {
                                                    $statutClass = 's-approuvee';
                                                } elseif ($demande['statut'] == 'refusee') {
                                                    $statutClass = 's-refusee';
                                                } elseif ($demande['statut'] == 'annulee') {
                                                    $statutClass = 's-annulee';
                                                } else {
                                                    $statutClass = 's-attente';
                                                }
                                                ?>
                                                <tr>
                                                    <td>
                                                        <div style="display:flex;align-items:center;gap:7px">
                                                            <div class="avatar" style="width:28px;height:28px;font-size:.62rem">
                                                                <?= $initials ?>
                                                            </div>
                                                            <span class="td-name"
                                                                style="font-size:.84rem"><?= esc((string) ($demande['employe_id'] ?? '')) ?></span>
                                                        </div>
                                                    </td>
                                                    <td><span class="type-badge <?= $typeColor ?>"><?= $typeLabel ?></span></td>
                                                    <td class="td-mono"><?= $nbJours ?> j</td>
                                                    <td><span
                                                            class="statut <?= $statutClass ?>"><?= esc((string) ($demande['statut'] ?? '')) ?></span>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <tr>
                                                <td colspan="4" class="text-center" style="padding:20px;color:#999">
                                                    Aucune demande
                                                </td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>

                            <!-- Absents du jour + soldes critiques -->
                            <div style="display:flex;flex-direction:column;gap:1rem">
                                <div class="data-card" style="margin:0">
                                    <div class="data-card-head">
                                        <h3><i class="bi bi-person-slash"
                                                style="color:var(--muted);margin-right:5px"></i>Absents aujourd'hui
                                        </h3>
                                    </div>
                                    <div style="padding:.75rem 1.1rem;display:flex;flex-direction:column;gap:.6rem">
                                        <?php if (!empty($absentAujourdhui)): ?>
                                            <?php foreach ($absentAujourdhui as $absent): ?>
                                                <?php
                                                $initials = strtoupper(substr(($absent['prenom'] ?? ''), 0, 1) . substr(($absent['nom'] ?? ''), 0, 1));
                                                $finDate = date('d/m', strtotime($absent['date_fin']));
                                                ?>
                                                <div style="display:flex;align-items:center;gap:8px">
                                                    <div class="avatar" style="width:30px;height:30px;font-size:.65rem">
                                                        <?= $initials ?>
                                                    </div>
                                                    <div>
                                                        <div style="font-size:.83rem;font-weight:500;color:var(--ink)">
                                                            <?= esc((string) ($absent['prenom'] . ' ' . $absent['nom'])) ?>
                                                        </div>
                                                        <div style="font-size:.72rem;color:var(--muted)">
                                                            <?= esc((string) ($absent['libelle'] ?? 'N/A')) ?> · retour
                                                            <?= $finDate ?>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <div style="text-align:center;padding:1rem;color:#999">
                                                Aucun absent aujourd'hui
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <?php if (!empty($soldesCritiques)): ?>
                                    <div class="flash flash-warn" style="margin:0">
                                        <i class="bi bi-exclamation-triangle-fill"></i>
                                        <span style="font-size:.8rem"><?= count($soldesCritiques) ?> employé(s) ont un solde
                                            critique (≤ 2 jours). <a href="<?= site_url('/admin/soldes') ?>"
                                                style="color:var(--warn);font-weight:500">Voir les soldes →</a></span>
                                    </div>
                                <?php endif; ?>
                            </div>

                        </div>
                    </div>

                    <div id="crud-modal" class="crud-modal" style="display:none" onclick="if (event.target === this) closeCrudModal();">
                        <div class="crud-modal-dialog">
                            <div id="crud-modal-body"></div>
                        </div>
                    </div>

                </div>
                <div class="footer-app"><i class="bi bi-c-circle"></i> 2026 <span>TechMada RH (4153-4286)</span></div>
            </div>

        </div>
    </section>

    <script>
        let currentSection = null;

        function loadSection(section) {
            currentSection = section;
            // Masquer le dashboard
            document.getElementById('dashboard-content').style.display = 'none';
            document.getElementById('ajax-content').style.display = 'block';

            // Charger la section 
            fetch('<?= site_url('/admin/ajax/') ?>' + section)
                .then(response => response.text())
                .then(html => {
                    document.getElementById('ajax-content').innerHTML = html;
                    // Scroll vers le haut du contenu
                    document.querySelector('.main').scrollTop = 0;
                })
                .catch(error => console.error('Erreur AJAX:', error));

            return false;
        }

        /**
         * Retour au dashboard
         */
        function showDashboard() {
            currentSection = null;
            document.getElementById('ajax-content').style.display = 'none';
            document.getElementById('ajax-content').innerHTML = '';
            document.getElementById('dashboard-content').style.display = 'block';
            return false;
        }

        function openCrudForm(entity, id = null, refreshSection = null) {
            const modal = document.getElementById('crud-modal');
            const body = document.getElementById('crud-modal-body');

            modal.style.display = 'flex';
            body.innerHTML = '<div style="padding:1.2rem;text-align:center">Chargement...</div>';

            const suffix = id ? '/' + id : '';
            fetch('<?= site_url('/admin/ajax/form/') ?>' + entity + suffix)
                .then(response => response.text())
                .then(html => {
                    body.innerHTML = html;
                    const form = document.getElementById('crud-form');
                    if (form) {
                        form.dataset.refreshSection = refreshSection || currentSection || 'dashboard';
                    }
                })
                .catch(() => {
                    body.innerHTML = '<div style="padding:1.2rem;color:#b00020">Impossible de charger le formulaire.</div>';
                });
        }

        function closeCrudModal() {
            const modal = document.getElementById('crud-modal');
            const body = document.getElementById('crud-modal-body');
            modal.style.display = 'none';
            body.innerHTML = '';
        }

        function submitCrudForm(form, fallbackSection = null) {
            const formData = new FormData(form);
            const refreshSection = form.dataset.refreshSection || fallbackSection || currentSection || 'dashboard';
            const errorsBox = form.querySelector('.crud-form-errors');

            if (errorsBox) {
                errorsBox.innerHTML = '';
            }

            fetch(form.action, {
                method: form.method || 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: formData
            })
                .then(async response => {
                    const payload = await response.json().catch(() => null);
                    if (!response.ok || !payload) {
                        throw payload || { message: 'Erreur inconnue' };
                    }
                    return payload;
                })
                .then(payload => {
                    if (payload.success) {
                        closeCrudModal();
                        if (refreshSection === 'dashboard') {
                            window.location.reload();
                        } else if (refreshSection) {
                            loadSection(refreshSection);
                        }
                    } else if (errorsBox) {
                        errorsBox.innerHTML = '<div class="alert alert-danger mb-0">' + (payload.message || 'Erreur de sauvegarde') + '</div>';
                    }
                })
                .catch(payload => {
                    const errors = payload && payload.errors ? payload.errors : null;
                    if (errorsBox && errors) {
                        const messages = Object.values(errors).map(message => '<div>' + message + '</div>').join('');
                        errorsBox.innerHTML = '<div class="alert alert-danger mb-0">' + messages + '</div>';
                    } else if (errorsBox) {
                        errorsBox.innerHTML = '<div class="alert alert-danger mb-0">Erreur lors de l\'enregistrement.</div>';
                    }
                });

            return false;
        }

        /**
         * Actions sur les employés
         */
        function disableEmployee(id) {
            if (confirm('Êtes-vous sûr de vouloir désactiver cet employé ?')) {
                fetch('<?= site_url('/admin/employes/') ?>' + id + '/disable', {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Content-Type': 'application/x-www-form-urlencoded'
                    }
                })
                    .then(response => response.json())
                    .then(payload => {
                        if (payload.success) {
                            loadSection('employes');
                        }
                    })
                    .catch(error => console.error('Erreur:', error));
            }
        }

        /**
         * Actions sur les départements
         */
        function deleteDepartment(id) {
            if (confirm('Êtes-vous sûr de vouloir supprimer ce département ?')) {
                fetch('<?= site_url('/admin/departements/') ?>' + id, {
                    method: 'DELETE',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                    .then(response => response.json())
                    .then(payload => {
                        if (payload.success) {
                            loadSection('departements');
                        }
                    })
                    .catch(error => console.error('Erreur:', error));
            }
        }

        function showDepartmentForm() {
            openCrudForm('departement', null, 'departements');
        }

        /**
         * Actions sur les types
         */
        function deleteType(id) {
            if (confirm('Êtes-vous sûr de vouloir supprimer ce type ?')) {
                fetch('<?= site_url('/admin/types/') ?>' + id, {
                    method: 'DELETE',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                    .then(response => response.json())
                    .then(payload => {
                        if (payload.success) {
                            loadSection('types');
                        }
                    })
                    .catch(error => console.error('Erreur:', error));
            }
        }

        function showTypeForm() {
            openCrudForm('type', null, 'types');
        }

        /**
         * Actions sur les soldes
         */
        function initializeSoldes() {
            if (confirm('Êtes-vous sûr de vouloir réinitialiser les soldes pour cette année ?')) {
                fetch('<?= site_url('/admin/soldes/init') ?>', {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                    .then(response => response.json())
                    .then(payload => {
                        if (payload.success) {
                            loadSection('soldes');
                        }
                    })
                    .catch(error => console.error('Erreur:', error));
            }
        }

        document.addEventListener('DOMContentLoaded', function () {
            const navLinks = document.querySelectorAll('.sidebar-nav a');
            navLinks.forEach(link => {
                link.addEventListener('click', function (e) {
                    const href = this.getAttribute('href');

                    if (href.includes('/admin/employes')) {
                        e.preventDefault();
                        loadSection('employes');
                    } else if (href.includes('/admin/departements')) {
                        e.preventDefault();
                        loadSection('departements');
                    } else if (href.includes('/admin/types')) {
                        e.preventDefault();
                        loadSection('types');
                    } else if (href.includes('/admin/soldes')) {
                        e.preventDefault();
                        loadSection('soldes');
                    } else if (href.includes('/admin/dashboard')) {
                        e.preventDefault();
                        showDashboard();
                    }
                });
            });
        });
    </script>

    <style>
        .crud-modal {
            position: fixed;
            inset: 0;
            background: rgba(15, 18, 17, 0.62);
            z-index: 2000;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1rem;
        }

        .crud-modal-dialog {
            width: min(760px, 100%);
            max-height: 90vh;
            overflow: auto;
            background: #fff;
            border-radius: 18px;
            box-shadow: 0 22px 70px rgba(0, 0, 0, 0.3);
            padding: 1rem;
        }

        .modal-form-card {
            padding: 0.25rem 0.25rem 0.5rem;
        }

        .modal-close-btn {
            border: none;
            background: transparent;
            font-size: 1.9rem;
            line-height: 1;
            color: var(--muted);
            cursor: pointer;
        }

        .modal-actions {
            display: flex;
            justify-content: flex-end;
            gap: 0.75rem;
            margin-top: 1rem;
        }

        .crud-form-errors .alert {
            margin-bottom: 1rem;
        }

        .btn-xs {
            display: inline-block;
            padding: 4px 8px;
            font-size: 0.8rem;
            border: none;
            border-radius: 3px;
            cursor: pointer;
            text-decoration: none;
            color: white;
            margin-right: 4px;
        }

        .btn-primary {
            background-color: var(--forest);
            width: fit-content;
        }

        .btn-primary:hover {
            background-color: #3a7c3e;
            text-decoration: none;
        }

        .btn-danger {
            background-color: #d32f2f;
        }

        .btn-danger:hover {
            background-color: #b71c1c;
            text-decoration: none;
        }
    </style>
</body>

</html>