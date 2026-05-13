<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TechMada RH — Historique des demandes</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700&family=DM+Sans:wght@300;400;500&family=DM+Mono:wght@400;500&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="<?= base_url('assets/css/style.css') ?>">
</head>
<body>
    <section>
        <div class="app-wrap">
            <div class="main" style="width:100%">
                <div class="topbar">
                    <div>
                        <div class="topbar-title">Historique des demandes</div>
                        <div class="topbar-breadcrumb"><a href="<?= site_url('/rh/dashboard') ?>">Accueil</a> <i class="bi bi-chevron-right" style="font-size:.6rem"></i> Historique</div>
                    </div>
                </div>
                <div class="content">
                    <div class="data-card" style="margin:0">
                        <div class="data-card-head">
                            <h3>Historique complet</h3>
                        </div>
                        <table class="tbl">
                            <thead>
                                <tr><th>Employé</th><th>Type</th><th>Période</th><th>Durée</th><th>Statut</th></tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($demandes)): ?>
                                    <?php foreach ($demandes as $demande): ?>
                                        <tr>
                                            <td><?= esc((string)($demande['prenom'] ?? '')) ?> <?= esc((string)($demande['nom'] ?? '')) ?><div class="td-muted" style="font-size:.78rem"><?= esc((string)($demande['departement_nom'] ?? 'N/A')) ?></div></td>
                                            <td><span class="type-badge t-annuel"><?= esc((string)($demande['type_libelle'] ?? '')) ?></span></td>
                                            <td class="td-muted"><?= date('d/m/Y', strtotime($demande['date_debut'])) ?> – <?= date('d/m/Y', strtotime($demande['date_fin'])) ?></td>
                                            <td class="td-mono"><?= esc((string)($demande['nb_jours'] ?? '')) ?> j</td>
                                            <td><span class="statut <?= $demande['statut'] === 'approuvee' ? 's-approuvee' : ($demande['statut'] === 'refusee' ? 's-refusee' : 's-attente') ?>"><?= esc((string)$demande['statut']) ?></span></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr><td colspan="5" class="text-center" style="padding:20px;color:#999">Aucune demande</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>
</body>
</html>
