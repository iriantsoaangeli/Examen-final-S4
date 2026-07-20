<?php
$tauxMoyen = ! empty($configurationCommissions)
    ? array_sum(array_column($configurationCommissions, 'commission_rate')) / count($configurationCommissions)
    : 0;
$tauxMax = ! empty($configurationCommissions)
    ? max(array_column($configurationCommissions, 'commission_rate'))
    : 0;
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Barème des commissions — CASH</title>
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
    <h1 class="page-title">Barème des commissions</h1>

    <div class="row g-3">
      <div class="col-md-4">
        <div class="stat-card">
          <div class="icon-badge" style="background:#FDF3E3; color:#B9770E;"><i class="bi bi-list-columns-reverse"></i></div>
          <div class="label">Barèmes configurés</div>
          <div class="value"><?= count($configurationCommissions) ?></div>
        </div>
      </div>
      <div class="col-md-4">
        <div class="stat-card">
          <div class="icon-badge" style="background:#E9EEFB; color:#2A4FD6;"><i class="bi bi-percent"></i></div>
          <div class="label">Taux moyen</div>
          <div class="value"><?= number_format($tauxMoyen * 100, 2, ',', ' ') ?> %</div>
        </div>
      </div>
      <div class="col-md-4">
        <div class="stat-card">
          <div class="icon-badge" style="background:#FBEAE7; color:var(--neg);"><i class="bi bi-graph-up-arrow"></i></div>
          <div class="label">Taux le plus élevé</div>
          <div class="value"><?= number_format($tauxMax * 100, 2, ',', ' ') ?> %</div>
        </div>
      </div>
    </div>

    <div class="section-heading">
      <h2>Détail par couple d'opérateurs</h2>
    </div>
    <div class="card-ledger">
      <?php if (empty($configurationCommissions)) : ?>
        <div class="tx-row"><div class="tx-info"><div class="tx-name">Aucun barème configuré</div></div></div>
      <?php else : ?>
        <?php foreach ($configurationCommissions as $commission) : ?>
          <?php $meme = $commission['type_commission'] === 'meme_operateur'; ?>
          <div class="tx-row">
            <div class="tx-icon <?= $meme ? 'merchant-pay' : 'merchant-refund' ?>">
              <i class="bi <?= $meme ? 'bi-arrow-repeat' : 'bi-shuffle' ?>"></i>
            </div>
            <div class="tx-info">
              <div class="tx-name"><?= esc($commission['operator_sender_name']) ?> → <?= esc($commission['operator_receiver_name']) ?></div>
              <div class="tx-date"><?= $meme ? 'Même opérateur' : 'Inter-opérateur' ?></div>
            </div>
            <div class="tx-amount pos"><?= number_format((float) $commission['commission_rate'] * 100, 2, ',', ' ') ?> %</div>
          </div>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>
  </main>
</div>

</body>
</html>
