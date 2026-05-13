<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TechMada RH — Mon Profil</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet" />
    <link
        href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700&family=DM+Sans:wght@300;400;500&family=DM+Mono:wght@400;500&display=swap"
        rel="stylesheet" />
    <link rel="stylesheet" href="<?= base_url('assets/css/style.css') ?>">
</head>

<body>
    <?php
    $user = isset($user) && is_array($user) ? $user : [];
    $departement = isset($departement) && is_array($departement) ? $departement : [];
    $metrics = isset($metrics) ? $metrics : [];
    $soldes = isset($soldes) ? $soldes : [];
    $fullName = trim((string)($user['prenom'] ?? '') . ' ' . (string)($user['nom'] ?? ''));
    $initials = strtoupper(substr((string)($user['prenom'] ?? ''), 0, 1) . substr((string)($user['nom'] ?? ''), 0, 1)) ?: 'U';
    ?>

    <section id="page-profil-employe">
        <div class="app-wrap">
            <aside class="sidebar">
                <div class="sidebar-brand">
                    <div class="sidebar-logo-icon"><i class="bi bi-briefcase"></i></div>
                    <div class="sidebar-brand-name">TechMada RH<span>Espace employé</span></div>
                </div>
                <div class="sidebar-section">Menu</div>
                <ul class="sidebar-nav">
                    <li><a href="<?= site_url('employe/dashboard') ?>"><i class="bi bi-grid-1x2"></i> Tableau de bord</a></li>
                    <li><a href="<?= site_url('employe/demande') ?>"><i class="bi bi-plus-circle"></i> Nouvelle demande</a></li>
                    <li><a href="<?= site_url('employe/mes-demandes') ?>"><i class="bi bi-calendar3"></i> Mes demandes</a></li>
                    <li><a href="<?= site_url('employe/profil') ?>" class="active"><i class="bi bi-person"></i> Mon profil</a></li>
                </ul>
                <div class="sidebar-user">
                    <div class="s-user-row">
                        <div class="avatar av-green"><?= esc($initials) ?></div>
                        <div>
                            <div class="user-name"><?= esc($fullName) ?></div>
                            <div class="user-role"><?= esc((string)($user['role'] ?? 'Employé')) ?><?php if (! empty($departement['nom'])): ?> · <?= esc((string)$departement['nom']) ?><?php endif; ?></div>
                                <a href="/logout" style="margin-left:auto;color:rgba(255,255,255,.25);font-size:1.1rem"
                            title="Déconnexion"><i class="bi bi-box-arrow-right"></i></a>
                        </div>
                    </div>
                </div>
            </aside>

            <div class="main">
                <div class="topbar">
                    <div>
                        <div class="topbar-title">Mon profil</div>
                        <div class="topbar-breadcrumb"><a href="<?= site_url('employe/dashboard') ?>">Accueil</a> <i class="bi bi-chevron-right" style="font-size:.6rem"></i> Profil</div>
                    </div>
                </div>

                <div class="content">
                    <?php $smsg = session()->getFlashdata('success'); if ($smsg): ?>
                        <div class="flash flash-success">
                            <?php if (is_array($smsg)): foreach ($smsg as $m): ?>
                                <div><?= esc((string)$m) ?></div>
                            <?php endforeach; else: ?>
                                <?= esc((string)$smsg) ?>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                    <?php $emsg = session()->getFlashdata('error'); if ($emsg): ?>
                        <div class="flash flash-error">
                            <?php if (is_array($emsg)): foreach ($emsg as $m): ?>
                                <div><?= esc((string)$m) ?></div>
                            <?php endforeach; else: ?>
                                <?= esc((string)$emsg) ?>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                    <?php $flashErrors = session()->getFlashdata('errors'); if ($flashErrors): ?>
                        <div class="flash flash-error"><ul style="margin:0;padding-left:1.1rem"><?php foreach ($flashErrors as $e) { echo '<li>' . esc((string)$e) . '</li>'; } ?></ul></div>
                    <?php endif; ?>

                    <div class="data-card">
                        <div class="data-card-head">
                            <h3>Informations du profil</h3>
                        </div>
                        <div style="padding:1rem 1.25rem;display:grid;grid-template-columns:repeat(auto-fit,minmax(240px,1fr));gap:1rem">
                            <div class="solde-card" style="margin:0">
                                <div class="solde-header"><span class="solde-type">Nom complet</span></div>
                                <div class="solde-label"><?= esc($fullName ?: '—') ?></div>
                            </div>
                            <div class="solde-card" style="margin:0">
                                <div class="solde-header"><span class="solde-type">Email</span></div>
                                <div class="solde-label"><?= esc((string)($user['email'] ?? '—')) ?></div>
                            </div>
                            <div class="solde-card" style="margin:0">
                                <div class="solde-header"><span class="solde-type">Rôle</span></div>
                                <div class="solde-label"><?= esc((string)($user['role'] ?? 'Employé')) ?></div>
                            </div>
                            <div class="solde-card" style="margin:0">
                                <div class="solde-header"><span class="solde-type">Département</span></div>
                                <div class="solde-label"><?= esc((string)($departement['nom'] ?? '—')) ?></div>
                            </div>
                            <div class="solde-card" style="margin:0">
                                <div class="solde-header"><span class="solde-type">Date d'embauche</span></div>
                                <div class="solde-label"><?= ! empty($user['date_embauche']) ? esc(date('d M Y', strtotime($user['date_embauche']))) : '—' ?></div>
                            </div>
                            <div class="solde-card" style="margin:0">
                                <div class="solde-header"><span class="solde-type">Statut du compte</span></div>
                                <div class="solde-label"><?= ((int)($user['actif'] ?? 0) === 1) ? 'Actif' : 'Inactif' ?></div>
                            </div>
                        </div>
                    </div>

                    <div class="metrics" style="margin-top:1.5rem">
                        <div class="metric">
                            <div class="metric-top"><div class="metric-icon mi-amber"><i class="bi bi-hourglass-split"></i></div></div>
                            <div class="metric-val"><?= esc((string)($metrics['en_attente'] ?? 0)) ?></div>
                            <div class="metric-label">Demandes en attente</div>
                        </div>
                        <div class="metric">
                            <div class="metric-top"><div class="metric-icon mi-green"><i class="bi bi-check-circle"></i></div></div>
                            <div class="metric-val"><?= esc((string)($metrics['approuvee'] ?? 0)) ?></div>
                            <div class="metric-label">Demandes approuvées</div>
                        </div>
                        <div class="metric">
                            <div class="metric-top"><div class="metric-icon mi-forest"><i class="bi bi-calendar-check"></i></div></div>
                            <div class="metric-val"><?= esc((string)($metrics['jours_restants_total'] ?? 0)) ?></div>
                            <div class="metric-label">Jours restants</div>
                        </div>
                        <div class="metric">
                            <div class="metric-top"><div class="metric-icon mi-red"><i class="bi bi-x-circle"></i></div></div>
                            <div class="metric-val"><?= esc((string)($metrics['refusee'] ?? 0)) ?></div>
                            <div class="metric-label">Demandes refusées</div>
                        </div>
                    </div>
                </div>

                <div class="footer-app"><i class="bi bi-c-circle"></i> 2025 <span>TechMada RH</span> — ROVATIANA: 4153 / JEREMIE:4286
            </div>
        </div>
    </section>
</body>

</html>
