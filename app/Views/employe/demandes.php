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
  $demandes = isset($demandes) ? $demandes : [];
  $userNom = isset($userNom) ? $userNom : '';
  $userPrenom = isset($userPrenom) ? $userPrenom : '';
  $departement = isset($departement) ? $departement : [];
  $metrics = isset($metrics) ? $metrics : [];
  ?>
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
  <!-- ╔══════════════════════════════════════════════════════════════╗
     ║  PAGE 4 — MES DEMANDES EMPLOYÉ  (employe/index.php)         ║
     ╚══════════════════════════════════════════════════════════════╝ -->
  <section id="page-mes-conges">
    <div class="app-wrap">

      <aside class="sidebar">
        <div class="sidebar-brand">
          <div class="sidebar-logo-icon"><i class="bi bi-briefcase"></i></div>
          <div class="sidebar-brand-name">TechMada RH<span>Espace employé</span></div>
        </div>
        <div class="sidebar-section">Menu</div>
        <ul class="sidebar-nav" >
          <li><a href="<?= site_url('employe/dashboard') ?>"><i class="bi bi-grid-1x2"></i> Tableau de bord</a></li>
          <li><a href="<?= site_url('employe/demande') ?>"><i class="bi bi-plus-circle"></i> Nouvelle demande</a></li>
          <li>
            <a href="<?= site_url('employe/mes-demandes') ?>" class="active">
              <i class="bi bi-calendar3"></i> Mes demandes
              <span class="nav-badge alert"><?php echo isset($metrics['en_attente']) ? (int)$metrics['en_attente'] : 0; ?></span>
            </a>
          </li>
          <li><a href="<?= site_url('employe/profil') ?>"><i class="bi bi-person"></i> Mon profil</a></li>
        </ul>
        <div class="sidebar-user">
          <div class="s-user-row">
            <div class="avatar av-green"><?= esc(strtoupper(substr($userPrenom, 0, 1) . substr($userNom, 0, 1)) ?: 'U') ?></div>
            <div>
              <div class="user-name"><?= esc(trim($userPrenom . ' ' . $userNom)) ?></div>
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
            <div class="topbar-title">Mes demandes de congé</div>
            <div class="topbar-breadcrumb"><a href="<?= site_url('employe/dashboard') ?>">Accueil</a> <i class="bi bi-chevron-right"
                style="font-size:.6rem"></i> Mes demandes</div>
          </div>
          <div class="topbar-actions">
            <a href="<?= site_url('employe/demande') ?>" class="btn-forest" style="padding:7px 14px;font-size:.82rem"><i
                class="bi bi-plus-lg"></i> Nouvelle demande</a>
          </div>
        </div>

        <div class="content">
          <div class="data-card">
            <div class="data-card-head">
              <h3>Toutes mes demandes</h3>
              <div style="display:flex;gap:6px">
                <select class="f-select" style="font-size:.8rem;padding:6px 10px;width:auto">
                  <option>Tous les statuts</option>
                  <option>En attente</option>
                  <option>Approuvée</option>
                  <option>Refusée</option>
                  <option>Annulée</option>
                </select>
              </div>
            </div>
            <table class="tbl">
              <thead>
                <tr>
                  <th>Type</th>
                  <th>Début</th>
                  <th>Fin</th>
                  <th>Durée</th>
                  <th>Statut</th>
                  <th>Commentaire RH</th>
                  <th>Action</th>
                </tr>
              </thead>
              <tbody>
                <?php if (empty($demandes)): ?>
                  <tr>
                    <td colspan="7" class="td-muted">Aucune demande enregistrée.</td>
                  </tr>
                <?php else: ?>
                  <?php foreach ($demandes as $d): ?>
                    <tr>
                      <td><span class="type-badge"><?= esc((string)($d['type_libelle'] ?? '—')) ?></span></td>
                      <td class="td-muted"><?= esc((string)date('d M Y', strtotime($d['date_debut']))) ?></td>
                      <td class="td-muted"><?= esc((string)date('d M Y', strtotime($d['date_fin']))) ?></td>
                      <td class="td-mono"><?= esc((string)($d['nb_jours'] ?? '0')) ?> j</td>
                      <td><span class="statut"><?= esc((string)($d['statut'] ?? '')) ?></span></td>
                      <td class="td-muted" style="font-size:.78rem"><?= esc((string)($d['commentaire_rh'] ?? '—')) ?></td>
                      <td>
                        <?php if (($d['statut'] ?? '') === 'en_attente'): ?>
                          <form style="display:inline" method="post" action="<?php echo site_url('employe/demande/' . (int)$d['id'] . '/cancel') ?>">
                            <?= csrf_field() ?>
                            <button class="btn-sm btn-cancel" type="submit"><i class="bi bi-x"></i> Annuler</button>
                          </form>
                        <?php else: ?>
                          <span class="td-muted" style="font-size:.75rem">—</span>
                        <?php endif; ?>
                      </td>
                    </tr>
                  <?php endforeach; ?>
                <?php endif; ?>
              </tbody>
            </table>
          </div>
        </div>
        <div class="footer-app"><i class="bi bi-c-circle"></i> 2025 <span>TechMada RH</span> — ROVATIANA: 4153 / JEREMIE:4286
      </div>

    </div>
  </section>
</body>

</html>
