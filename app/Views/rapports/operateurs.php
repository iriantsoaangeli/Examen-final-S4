<?php
$totalOperateurs = count($operateursProviders);
$avecFournisseur = count(array_filter($operateursProviders, static fn (array $o): bool => $o['provider_status'] === 'avec_fournisseur'));
$sansFournisseur = $totalOperateurs - $avecFournisseur;
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Opérateurs & fournisseurs — CASH</title>
<link href="<?= base_url('bootstrap/css/bootstrap.css') ?>" rel="stylesheet">
<link href="<?= base_url('bootstrap/css/icons.css') ?>" rel="stylesheet">
<link href="<?= base_url('css/custom.css') ?>" rel="stylesheet">
</head>
<body class="app-body">

<div class="app-shell">
  <?= $this->include('partials/sidebar') ?>

  <main class="main">
    <div class="page-eyebrow">
      <a href="<?= base_url('rapports') ?>" style="color: var(--muted); text-decoration:none;">
        <i class="bi bi-arrow-left"></i> Rapports
      </a>
    </div>
    <h1 class="page-title">Opérateurs &amp; fournisseurs</h1>

    <div class="row g-3">
      <div class="col-md-4">
        <div class="stat-card">
          <div class="icon-badge" style="background:#E9EEFB; color:#2A4FD6;"><i class="bi bi-sim"></i></div>
          <div class="label">Opérateurs</div>
          <div class="value"><?= $totalOperateurs ?></div>
        </div>
      </div>
      <div class="col-md-4">
        <div class="stat-card">
          <div class="icon-badge" style="background:#E7F5EC; color:var(--accent-dark);"><i class="bi bi-check-circle"></i></div>
          <div class="label">Avec fournisseur</div>
          <div class="value"><?= $avecFournisseur ?></div>
        </div>
      </div>
      <div class="col-md-4">
        <div class="stat-card">
          <div class="icon-badge" style="background:#FBEAE7; color:var(--neg);"><i class="bi bi-exclamation-circle"></i></div>
          <div class="label">Sans fournisseur</div>
          <div class="value"><?= $sansFournisseur ?></div>
        </div>
      </div>
    </div>

    <div class="section-heading">
      <h2>Liste des opérateurs</h2>
    </div>
    <div class="card-ledger">
      <?php if (empty($operateursProviders)) : ?>
        <div class="tx-row"><div class="tx-info"><div class="tx-name">Aucun opérateur enregistré</div></div></div>
      <?php else : ?>
        <?php foreach ($operateursProviders as $operateur) : ?>
          <?php $avec = $operateur['provider_status'] === 'avec_fournisseur'; ?>
          <div class="tx-row">
            <div class="tx-icon <?= $avec ? 'merchant-sale' : 'merchant-refund' ?>">
              <i class="bi bi-sim"></i>
            </div>
            <div class="tx-info">
              <div class="tx-name"><?= esc($operateur['operator_name']) ?></div>
              <div class="tx-date"><?= esc($operateur['provider_name'] ?? 'Aucun fournisseur') ?></div>
            </div>
            <div class="tx-amount <?= $avec ? 'pos' : 'neg' ?>"><?= esc($operateur['provider_numero'] ?? '—') ?></div>
          </div>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>
  </main>
</div>

</body>
</html>
