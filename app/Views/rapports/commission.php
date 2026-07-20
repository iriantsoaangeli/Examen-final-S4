<?php
$totalCommission = array_sum(array_column($commissionsParOperateur, 'total_commission'));
$totalAEnvoyer = array_sum(array_column($montantsAEnvoyer, 'montant_total'));
$totalCommissionAEnvoyer = array_sum(array_column($montantsAEnvoyer, 'commission_total'));
$configurationLimitee = array_slice($configurationCommissions, 0, 10);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Rapports — CASH</title>
<link href="<?= base_url('bootstrap/css/bootstrap.css') ?>" rel="stylesheet">
<link href="<?= base_url('bootstrap/css/icons.css') ?>" rel="stylesheet">
<link href="<?= base_url('css/custom.css') ?>" rel="stylesheet">
</head>
<body class="app-body">

<div class="app-shell">
  <?= $this->include('partials/sidebar') ?>

  <main class="main">
    <div class="page-eyebrow">Back office</div>
    <h1 class="page-title">Rapport des commissions</h1>

    <div class="row g-3">
      <div class="col-md-4">
        <div class="stat-card">
          <div class="icon-badge" style="background:#E7F5EC; color:var(--accent-dark);"><i class="bi bi-cash-coin"></i></div>
          <div class="label">Commissions calculées</div>
          <div class="value">Ar <?= number_format((float) $totalCommission, 0, ',', ' ') ?></div>
        </div>
      </div>
      <div class="col-md-4">
        <div class="stat-card">
          <div class="icon-badge" style="background:#FBEAE7; color:var(--neg);"><i class="bi bi-send"></i></div>
          <div class="label">Montants transférés</div>
          <div class="value">Ar <?= number_format((float) $totalAEnvoyer, 0, ',', ' ') ?></div>
        </div>
      </div>
      <div class="col-md-4">
        <div class="stat-card">
          <div class="icon-badge" style="background:#E9EEFB; color:#2A4FD6;"><i class="bi bi-percent"></i></div>
          <div class="label">Commission à reverser</div>
          <div class="value">Ar <?= number_format((float) $totalCommissionAEnvoyer, 0, ',', ' ') ?></div>
        </div>
      </div>
    </div>

    <div class="section-heading">
      <h2>Commissions par opérateur</h2>
    </div>
    <div class="card-ledger">
      <?php if (empty($commissionsParOperateur)) : ?>
        <div class="tx-row"><div class="tx-info"><div class="tx-name">Aucune commission enregistrée</div></div></div>
      <?php else : ?>
        <?php foreach ($commissionsParOperateur as $ligne) : ?>
          <div class="tx-row">
            <div class="tx-icon merchant-sale"><i class="bi bi-diagram-3"></i></div>
            <div class="tx-info">
              <div class="tx-name"><?= esc($ligne['sender_operator_name']) ?> → <?= esc($ligne['receiver_operator_name']) ?></div>
              <div class="tx-date"><?= (int) $ligne['nombre_operations'] ?> opération(s)</div>
            </div>
            <div class="tx-amount pos">+Ar <?= number_format((float) $ligne['total_commission'], 0, ',', ' ') ?></div>
          </div>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>

    <div class="section-heading">
      <h2>Montants à envoyer par opérateur</h2>
    </div>
    <div class="card-ledger">
      <?php if (empty($montantsAEnvoyer)) : ?>
        <div class="tx-row"><div class="tx-info"><div class="tx-name">Aucun montant à reverser</div></div></div>
      <?php else : ?>
        <?php foreach ($montantsAEnvoyer as $ligne) : ?>
          <div class="tx-row">
            <div class="tx-icon merchant-transfer"><i class="bi bi-building"></i></div>
            <div class="tx-info">
              <div class="tx-name"><?= esc($ligne['operator_name']) ?></div>
              <div class="tx-date">
                <?= (int) $ligne['nombre_transferts'] ?> transfert(s)
                · commission Ar <?= number_format((float) $ligne['commission_total'], 0, ',', ' ') ?>
              </div>
            </div>
            <div class="tx-amount neg">−Ar <?= number_format((float) $ligne['montant_total'], 0, ',', ' ') ?></div>
          </div>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>

    <div class="section-heading">
      <h2>Opérateurs fournisseurs</h2>
    </div>
    <div class="card-ledger">
      <?php foreach ($operateursProviders as $operateur) : ?>
        <div class="tx-row">
          <div class="tx-icon merchant-pay"><i class="bi bi-sim"></i></div>
          <div class="tx-info">
            <div class="tx-name"><?= esc($operateur['operator_name']) ?></div>
            <div class="tx-date"><?= esc($operateur['provider_name'] ?? 'Aucun fournisseur') ?></div>
          </div>
          <div class="tx-amount"><?= esc($operateur['provider_numero'] ?? '-') ?></div>
        </div>
      <?php endforeach; ?>
    </div>

    <div class="section-heading">
      <h2>Barème commissions</h2>
    </div>
    <div class="card-ledger">
      <?php if (empty($configurationLimitee)) : ?>
        <div class="tx-row"><div class="tx-info"><div class="tx-name">Aucun barème configuré</div></div></div>
      <?php else : ?>
        <?php foreach ($configurationLimitee as $commission) : ?>
          <div class="tx-row">
            <div class="tx-icon merchant-refund"><i class="bi bi-percent"></i></div>
            <div class="tx-info">
              <div class="tx-name"><?= esc($commission['operator_sender_name']) ?> → <?= esc($commission['operator_receiver_name']) ?></div>
              <div class="tx-date">Taux configuré</div>
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
