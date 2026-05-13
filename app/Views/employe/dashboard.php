<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TechMada RH — Gestion des congés CI4</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet" />
    <link
        href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700&family=DM+Sans:wght@300;400;500&family=DM+Mono:wght@400;500&display=swap"
        rel="stylesheet" />
    <link rel="stylesheet" href="<?=  base_url('assets/css/style.css') ?>">
</head>

<body>
    <?php
    // Ensure variables exist to avoid undefined variable notices when view is included elsewhere
    $userNom = isset($userNom) ? $userNom : '';
    $userPrenom = isset($userPrenom) ? $userPrenom : '';
    $departement = isset($departement) ? $departement : [];
    $soldes = isset($soldes) ? $soldes : [];
    $dernieresDemandes = isset($dernieresDemandes) ? $dernieresDemandes : [];
    $metrics = isset($metrics) ? $metrics : [];
    ?>
    <!-- ╔══════════════════════════════════════════════════════════════╗
     ║  PAGE 2 — DASHBOARD EMPLOYÉ  (employe/dashboard.php)        ║
     ╚══════════════════════════════════════════════════════════════╝ -->
    <section id="page-dashboard-employe">
        <div class="app-wrap">

            <!-- SIDEBAR EMPLOYÉ -->
            <aside class="sidebar">
                <div class="sidebar-brand">
                    <div class="sidebar-logo-icon"><i class="bi bi-briefcase"></i></div>
                    <div class="sidebar-brand-name">TechMada RH<span>Espace employé</span></div>
                </div>
                <div class="sidebar-section">Menu</div>
                <ul class="sidebar-nav">
                    <li><a href="<?= site_url('employe/dashboard') ?>" class="active"><i class="bi bi-grid-1x2"></i> Tableau de bord</a></li>
                    <li><a href="<?= site_url('employe/demande') ?>"><i class="bi bi-plus-circle"></i> Nouvelle demande</a></li>
                    <li>
                        <a href="<?= site_url('employe/mes-demandes') ?>">
                            <i class="bi bi-calendar3"></i> Mes demandes
                            <span class="nav-badge alert"><?php echo isset($metrics['en_attente']) ? (int)$metrics['en_attente'] : 0; ?></span>
                        </a>
                    </li>
                    <li><a href="<?= site_url('employe/profil') ?>"><i class="bi bi-person"></i> Mon profil</a></li>
                </ul>
                <div class="sidebar-user">
                    <div class="s-user-row">
                        <?php
                        $initials = '';
                        if (! empty($userNom) || ! empty($userPrenom)) {
                            $initials = strtoupper((substr($userNom, 0, 1) . substr($userPrenom, 0, 1)));
                        }
                        ?>
                        <div class="avatar av-green"><?php echo $initials ?: 'U'; ?></div>
                        <div>
                            <div class="user-name"><?php echo trim($userPrenom . ' ' . $userNom); ?></div>
                            <div class="user-role"><?php echo esc((string)(session()->get('role') ?: 'Employé')); ?>
                                <?php if (! empty($departement['nom'])): ?>· <?php echo esc((string)$departement['nom']); ?><?php endif; ?></div>
                        </div>
                        <a href="/logout" style="margin-left:auto;color:rgba(255,255,255,.25);font-size:1.1rem"
                            title="Déconnexion"><i class="bi bi-box-arrow-right"></i></a>
                    </div>
                </div>
            </aside>

            <div class="main">
                <div class="topbar">
                    <div>
                        <div class="topbar-title">Tableau de bord</div>
                        <div class="topbar-breadcrumb">Accueil</div>
                    </div>
                    <div class="topbar-actions">
                        <a href="<?= site_url('employe/demande') ?>" class="btn-forest" style="padding:7px 14px;font-size:.82rem">
                            <i class="bi bi-plus-lg"></i> Nouvelle demande
                        </a>
                    </div>
                </div>

                <div class="content">

                    <!-- Flash messages -->
                    <?php $smsg = session()->getFlashdata('success'); if ($smsg): ?>
                        <div class="flash flash-success">
                            <i class="bi bi-check-circle-fill"></i>
                            <?php if (is_array($smsg)): foreach ($smsg as $m): ?>
                                <div><?= esc((string)$m) ?></div>
                            <?php endforeach; else: ?>
                                <?= esc((string)$smsg) ?>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                    <?php $emsg = session()->getFlashdata('error'); if ($emsg): ?>
                        <div class="flash flash-error">
                            <i class="bi bi-x-circle-fill"></i>
                            <?php if (is_array($emsg)): foreach ($emsg as $m): ?>
                                <div><?= esc((string)$m) ?></div>
                            <?php endforeach; else: ?>
                                <?= esc((string)$emsg) ?>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                    <?php $flashErrors = session()->getFlashdata('errors'); if ($flashErrors): ?>
                        <div class="flash flash-error">
                            <i class="bi bi-exclamation-triangle-fill"></i>
                            <ul style="margin:0;padding-left:1.1rem">
                                <?php foreach ($flashErrors as $err): ?>
                                    <li><?= esc((string)$err) ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>

                    <!-- Métriques -->
                    <div class="metrics">
                        <div class="metric">
                            <div class="metric-top">
                                <div class="metric-icon mi-amber"><i class="bi bi-hourglass-split"></i></div>
                            </div>
                            <div class="metric-val"><?php echo isset($metrics['en_attente']) ? (int)$metrics['en_attente'] : 0; ?></div>
                            <div class="metric-label">En attente</div>
                        </div>
                        <div class="metric">
                            <div class="metric-top">
                                <div class="metric-icon mi-green"><i class="bi bi-check-circle"></i></div>
                            </div>
                            <div class="metric-val"><?php echo isset($metrics['approuvee']) ? (int)$metrics['approuvee'] : 0; ?></div>
                            <div class="metric-label">Approuvées</div>
                        </div>
                        <div class="metric">
                            <div class="metric-top">
                                <div class="metric-icon mi-forest"><i class="bi bi-calendar-check"></i></div>
                            </div>
                            <div class="metric-val"><?php echo isset($metrics['jours_restants_total']) ? (int)$metrics['jours_restants_total'] : 0; ?></div>
                            <div class="metric-label">Jours restants</div>
                            <div class="metric-sub">sur <?php echo array_sum(array_column($soldes, 'jours_attribues') ?: [0]); ?> cette année</div>
                        </div>
                        <div class="metric">
                            <div class="metric-top">
                                <div class="metric-icon mi-red"><i class="bi bi-x-circle"></i></div>
                            </div>
                            <div class="metric-val"><?php echo isset($metrics['refusee']) ? (int)$metrics['refusee'] : 0; ?></div>
                            <div class="metric-label">Refusée</div>
                        </div>
                    </div>

                    <!-- Soldes de congés -->
                    <div class="data-card">
                        <div class="data-card-head">
                            <h3>Mes soldes de congés — 2026</h3>
                        </div>
                        <div
                            style="padding:1rem 1.25rem;display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:1rem">
                            <?php if (! empty($soldes)): ?>
                                <?php foreach ($soldes as $s):
                                    $attribues = (float) ($s['jours_attribues'] ?? 0);
                                    $pris = (float) ($s['jours_pris'] ?? 0);
                                    $restants = $attribues - $pris;
                                    $pct = $attribues > 0 ? round(($pris / $attribues) * 100) : 0;
                                ?>
                                    <div class="solde-card" style="margin:0">
                                            <div class="solde-header">
                                                <span class="solde-type"><?php echo esc((string)($s['type_libelle'] ?? '—')); ?></span>
                                            <span class="solde-nums"><strong><?php echo (int)$restants; ?></strong> / <?php echo (int)$attribues; ?> j</span>
                                        </div>
                                        <div class="solde-bar">
                                            <div class="solde-fill" style="width:<?php echo $pct; ?>%"></div>
                                        </div>
                                        <div class="solde-label"><?php echo (int)$restants; ?> jours restants · <?php echo (int)$pris; ?> pris</div>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <div>Aucun solde trouvé pour cette année.</div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Dernières demandes -->
                    <div class="data-card">
                        <div class="data-card-head">
                            <h3>Mes dernières demandes</h3>
                            <a href="<?= site_url('employe/mes-demandes') ?>" style="font-size:.8rem;color:var(--forest);text-decoration:none">Voir tout
                                →</a>
                        </div>
                        <table class="tbl">
                            <thead>
                                <tr>
                                    <th>Type</th>
                                    <th>Du</th>
                                    <th>Au</th>
                                    <th>Durée</th>
                                    <th>Statut</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (! empty($dernieresDemandes)): ?>
                                    <?php foreach ($dernieresDemandes as $d): ?>
                                        <tr>
                                            <td><span class="type-badge"><?php echo esc((string)($d['type_libelle'] ?? '—')); ?></span></td>
                                            <td class="td-muted"><?php echo date('d M Y', strtotime($d['date_debut'])); ?></td>
                                            <td class="td-muted"><?php echo date('d M Y', strtotime($d['date_fin'])); ?></td>
                                            <td class="td-mono"><?php echo (int)$d['nb_jours']; ?> j</td>
                                            <td><span class="statut"><?php echo esc((string)($d['statut'] ?? '')); ?></span></td>
                                            <td>
                                                <?php if (($d['statut'] ?? '') === 'en_attente'): ?>
                                                    <form style="display:inline" method="post" action="<?php echo site_url('employe/demande/'.(int)$d['id'].'/cancel') ?>">
                                                        <?= csrf_field() ?>
                                                        <button class="btn-sm btn-cancel" type="submit">Annuler</button>
                                                    </form>
                                                <?php else: ?>
                                                    <span class="td-muted" style="font-size:.75rem">—</span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr><td colspan="6">Aucune demande trouvée.</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>

                </div>
                <div class="footer-app"><i class="bi bi-c-circle"></i> 2025 <span>TechMada RH</span> — ROVATIANA: 4153 / JEREMIE:4286
                </div>
            </div>

        </div>
    </section>
</body>

</html>
