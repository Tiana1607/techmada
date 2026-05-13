<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TechMada RH — Détail demande</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700&family=DM+Sans:wght@300;400;500&family=DM+Mono:wght@400;500&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="<?= base_url('assets/css/style.css') ?>">
</head>
<body>
    <section>
        <div class="app-wrap">
            <div class="main" style="width:100%">
                <?php $conge = $conge ?? []; ?>
                <?php $employe = $employe ?? []; ?>
                <?php $solde = $solde ?? null; ?>
                <div class="topbar">
                    <div>
                        <div class="topbar-title">Détail de la demande</div>
                        <div class="topbar-breadcrumb"><a href="<?= site_url('/rh/demandes') ?>">Demandes</a> <i class="bi bi-chevron-right" style="font-size:.6rem"></i> Détail</div>
                    </div>
                </div>

                <div class="content">
                    <div class="data-card" style="margin:0;max-width:900px">
                        <div class="data-card-head">
                            <h3><?= esc((string)($employe['prenom'] ?? '')) ?> <?= esc((string)($employe['nom'] ?? '')) ?></h3>
                        </div>
                        <div style="padding:1rem 1.2rem">
                            <p><strong>Type :</strong> <?= esc((string)($conge['type_libelle'] ?? '')) ?></p>
                            <p><strong>Période :</strong> <?= date('d/m/Y', strtotime($conge['date_debut'])) ?> → <?= date('d/m/Y', strtotime($conge['date_fin'])) ?></p>
                            <p><strong>Jours :</strong> <?= esc((string)$conge['nb_jours']) ?></p>
                            <p><strong>Statut :</strong> <?= esc((string)$conge['statut']) ?></p>
                            <p><strong>Département :</strong> <?= esc((string)($conge['departement_nom'] ?? 'N/A')) ?></p>
                            <p><strong>Email :</strong> <?= esc((string)($employe['email'] ?? '')) ?></p>
                            <p><strong>Solde actuel :</strong> <?= $solde ? esc((string)($solde['jours_restants'] ?? '0')) . ' jours restants' : 'N/A' ?></p>
                            <p><strong>Motif :</strong><br><?= nl2br(esc((string)($conge['motif'] ?? ''))) ?></p>
                            <p><strong>Commentaire RH :</strong><br><?= nl2br(esc((string)($conge['commentaire_rh'] ?? '—'))) ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</body>
</html>
