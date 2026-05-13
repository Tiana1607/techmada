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
    <link rel="stylesheet" href="<?= base_url('assets/css/style.css') ?>">
</head>

<body>
    <?php
    $typesCong = isset($typesCong) ? $typesCong : [];
    $userNom = isset($userNom) ? $userNom : '';
    $userPrenom = isset($userPrenom) ? $userPrenom : '';
    $departement = isset($departement) ? $departement : [];
    $metrics = isset($metrics) ? $metrics : [];
    $soldes = isset($soldes) ? $soldes : [];
    $dashboardUrl = site_url('employe/dashboard');
    $demandeUrl = site_url('employe/demande');
    $mesDemandesUrl = site_url('employe/mes-demandes');
    $profilUrl = site_url('employe/profil');

    // Map soldes by type_conge_id for quick lookup
    $soldesMap = [];
    foreach ($soldes as $s) {
        $soldesMap[$s['type_conge_id']] = $s;
    }
    ?>
    <!-- ╔══════════════════════════════════════════════════════════════╗
     ║  PAGE 3 — FORMULAIRE DEMANDE  (employe/create.php)          ║
     ╚══════════════════════════════════════════════════════════════╝ -->
    <section id="page-form-conge">
        <div class="app-wrap">

            <aside class="sidebar">
                <div class="sidebar-brand">
                    <div class="sidebar-logo-icon"><i class="bi bi-briefcase"></i></div>
                    <div class="sidebar-brand-name">TechMada RH<span>Espace employé</span></div>
                </div>
                <div class="sidebar-section">Menu</div>
                <ul class="sidebar-nav" >
                    <li><a href="<?= $dashboardUrl ?>"><i class="bi bi-grid-1x2"></i> Tableau de bord</a></li>
                    <li><a href="<?= $demandeUrl ?>" class="active"><i class="bi bi-plus-circle"></i> Nouvelle demande</a></li>
                    <li>
                        <a href="<?= $mesDemandesUrl ?>">
                            <i class="bi bi-calendar3"></i> Mes demandes
                            <span class="nav-badge alert"><?php echo isset($metrics['en_attente']) ? (int)$metrics['en_attente'] : 0; ?></span>
                        </a>
                    </li>
                    <li><a href="<?= $profilUrl ?>"><i class="bi bi-person"></i> Mon profil</a></li>
                </ul>
                <div class="sidebar-user">
                    <div class="s-user-row">
                        <div class="avatar av-green">SR</div>
                        <div>
                            <div class="user-name"><?= esc(trim(($userPrenom ?? '') . ' ' . ($userNom ?? ''))) ?></div>
                            <div class="user-role"><?= esc((string)(session()->get('role') ?: 'Employé')) ?><?php if (! empty($departement['nom'])): ?> · <?= esc((string)$departement['nom']) ?><?php endif; ?></div>
                                <a href="/logout" style="margin-left:auto;color:rgba(255,255,255,.25);font-size:1.1rem"
                            title="Déconnexion"><i class="bi bi-box-arrow-right"></i></a>
                        </div>
                    </div>
                </div>
            </aside>

            <div class="main">
                <div class="topbar">
                    <div>
                        <div class="topbar-title">Nouvelle demande de congé</div>
                        <div class="topbar-breadcrumb">
                            <a href="<?= $dashboardUrl ?>">Accueil</a>
                            <i class="bi bi-chevron-right" style="font-size:.6rem"></i> Nouvelle demande
                        </div>
                    </div>
                </div>

                <div class="content">

                    <div style="display:grid;grid-template-columns:1fr 300px;gap:1.5rem;align-items:start" class="form-layout">

                        <!-- Formulaire principal -->
                        <div>
                            <div class="form-section">
                                <h3>Détails de la demande</h3>

                                <?php $smsg = session()->getFlashdata('success');
                                if ($smsg): ?>
                                    <div class="flash flash-success">
                                        <?php if (is_array($smsg)): foreach ($smsg as $m): ?>
                                                <div><?= esc((string)$m) ?></div>
                                            <?php endforeach;
                                        else: ?>
                                            <?= esc((string)$smsg) ?>
                                        <?php endif; ?>
                                    </div>
                                <?php endif; ?>
                                <?php $emsg = session()->getFlashdata('error');
                                if ($emsg): ?>
                                    <div class="flash flash-error">
                                        <?php if (is_array($emsg)): foreach ($emsg as $m): ?>
                                                <div><?= esc((string)$m) ?></div>
                                            <?php endforeach;
                                        else: ?>
                                            <?= esc((string)$emsg) ?>
                                        <?php endif; ?>
                                    </div>
                                <?php endif; ?>
                                <?php $flashErrors = session()->getFlashdata('errors');
                                if ($flashErrors): ?>
                                    <div class="flash flash-error">
                                        <ul style="margin:0;padding-left:1.1rem"><?php foreach ($flashErrors as $e) {
                                                                                        echo '<li>' . esc((string)$e) . '</li>';
                                                                                    } ?></ul>
                                    </div>
                                <?php endif; ?>
                                <form action="<?= site_url('employe/demande') ?>" method="post">
                                    <?= csrf_field() ?>
                                    <div class="f-group" style="margin-bottom:1rem">
                                        <label class="f-label" for="type_conge_id">Type de congé <span style="color:var(--danger)">*</span></label>
                                        <select id="type_conge_id" name="type_conge_id" class="f-select">
                                            <option value="">-- Choisir un type --</option>
                                            <?php foreach ($typesCong as $type) {
                                                $typeId = isset($type['id']) ? (string)$type['id'] : '';
                                                $libelle = isset($type['libelle']) ? (string)$type['libelle'] : '';
                                                $solde = $soldesMap[$type['id']] ?? null;
                                                $restant = $solde ? ((float)$solde['jours_attribues'] - (float)$solde['jours_pris']) : null;
                                                $labelText = $libelle;
                                                if ($restant !== null) {
                                                    $labelText .= ' (' . ((string)$restant) . ' j restants)';
                                                }
                                            ?>
                                                <option value="<?= esc($typeId) ?>"><?= esc($labelText) ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>

                                    <div class="form-grid-2" style="margin-bottom:1rem">
                                        <div class="f-group">
                                            <label class="f-label" for="date_debut">Date de début <span style="color:var(--danger)">*</span></label>
                                            <input id="date_debut" name="date_debut" type="date" class="f-input" value="<?= esc(old('date_debut') ?: '') ?>" />
                                        </div>
                                        <div class="f-group">
                                            <label class="f-label" for="date_fin">Date de fin <span style="color:var(--danger)">*</span></label>
                                            <input id="date_fin" name="date_fin" type="date" class="f-input" value="<?= esc(old('date_fin') ?: '') ?>" />
                                        </div>
                                    </div>

                                    <!-- Calcul automatique côté PHP (affiché après soumission ou en JS) -->
                                    <div class="f-computed">
                                        <div class="f-computed-num">5</div>
                                        <div class="f-computed-label">jours calendaires calculés<br><span
                                                style="font-size:.7rem;opacity:.7">du lundi 23 au vendredi 27 juin 2025</span></div>
                                    </div>

                                    <div class="f-group" style="margin-bottom:1rem">
                                        <label class="f-label" for="motif">Motif (optionnel)</label>
                                        <textarea id="motif" name="motif" class="f-textarea" placeholder="Précisez le motif de votre demande si nécessaire..."><?= esc(old('motif') ?: '') ?></textarea>
                                        <div class="f-hint">Le motif est visible par le responsable RH.</div>
                                    </div>

                                    <div class="form-actions">
                                        <button class="btn-forest" type="submit"><i class="bi bi-send"></i> Soumettre la demande</button>
                                        <a href="<?= $dashboardUrl ?>" class="btn-secondary"><i class="bi bi-x"></i> Annuler</a>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <!-- Panneau latéral : solde & règles -->
                        <div style="display:flex;flex-direction:column;gap:1rem">
                            <div class="data-card" style="margin:0">
                                <div class="data-card-head">
                                    <h3><i class="bi bi-piggy-bank" style="color:var(--forest);margin-right:5px"></i>Vos soldes actuels
                                    </h3>
                                </div>
                                <div style="padding:.75rem 1.1rem;display:flex;flex-direction:column;gap:.75rem">
                                    <div>
                                        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:4px">
                                            <span style="font-size:.8rem;color:var(--ink)"><?= esc((string)($soldes[0]['type_libelle'] ?? 'Congés')) ?></span>
                                            <span
                                                style="font-family:'DM Mono',monospace;font-size:.8rem;color:var(--forest);font-weight:500"><?= esc((string)($soldes[0]['jours_restants'] ?? 0)) ?>
                                                j</span>
                                        </div>
                                        <div class="solde-bar">
                                            <div class="solde-fill" style="width:<?= esc((string)(isset($soldes[0]) && (float)($soldes[0]['jours_attribues'] ?? 0) > 0 ? round(((float)($soldes[0]['jours_pris'] ?? 0) / (float)$soldes[0]['jours_attribues']) * 100) : 0)) ?>%"></div>
                                        </div>
                                    </div>
                                    <div>
                                        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:4px">
                                            <span style="font-size:.8rem;color:var(--ink)"><?= esc((string)($soldes[1]['type_libelle'] ?? 'Solde')) ?></span>
                                            <span
                                                style="font-family:'DM Mono',monospace;font-size:.8rem;color:var(--forest);font-weight:500"><?= esc((string)($soldes[1]['jours_restants'] ?? 0)) ?>
                                                j</span>
                                        </div>
                                        <div class="solde-bar">
                                            <div class="solde-fill" style="width:<?= esc((string)(isset($soldes[1]) && (float)($soldes[1]['jours_attribues'] ?? 0) > 0 ? round(((float)($soldes[1]['jours_pris'] ?? 0) / (float)$soldes[1]['jours_attribues']) * 100) : 0)) ?>%"></div>
                                        </div>
                                    </div>
                                    <div>
                                        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:4px">
                                            <span style="font-size:.8rem;color:var(--ink)"><?= esc((string)($soldes[2]['type_libelle'] ?? 'Solde')) ?></span>
                                            <span style="font-family:'DM Mono',monospace;font-size:.8rem;color:var(--warn);font-weight:500"><?= esc((string)($soldes[2]['jours_restants'] ?? 0)) ?>
                                                j</span>
                                        </div>
                                        <div class="solde-bar">
                                            <div class="solde-fill warn" style="width:<?= esc((string)(isset($soldes[2]) && (float)($soldes[2]['jours_attribues'] ?? 0) > 0 ? round(((float)($soldes[2]['jours_pris'] ?? 0) / (float)$soldes[2]['jours_attribues']) * 100) : 0)) ?>%"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="flash flash-info" style="margin:0">
                                <i class="bi bi-info-circle-fill"></i>
                                <span style="font-size:.8rem">Le solde est déduit uniquement à l'approbation de votre
                                    responsable.</span>
                            </div>
                            <div style="background:var(--cream);border:1px solid var(--border);border-radius:8px;padding:.85rem 1rem">
                                <div style="font-size:.78rem;font-weight:500;color:var(--ink);margin-bottom:.5rem"><i
                                        class="bi bi-clipboard-check" style="color:var(--forest);margin-right:5px"></i>Rappel des règles
                                </div>
                                <ul style="margin:0;padding-left:1rem;font-size:.75rem;color:var(--muted);line-height:1.7">
                                    <li>Préavis minimum : 48h avant la date de début</li>
                                    <li>Pas de chevauchement avec une demande en cours</li>
                                    <li>Solde insuffisant = demande refusée automatiquement</li>
                                </ul>
                            </div>
                        </div>

                    </div>
                </div>
                <div class="footer-app"><i class="bi bi-c-circle"></i> 2025 <span>TechMada RH</span> — ROVATIANA: 4153 / JEREMIE:4286
            </div>

        </div>
    </section>
</body>

</html>
