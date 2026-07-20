<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Transactions — CASH</title>
<link href="<?= base_url('bootstrap/css/bootstrap.css') ?>" rel="stylesheet">
<link href="<?= base_url('bootstrap/css/icons.css') ?>" rel="stylesheet">
<link href="<?= base_url('css/custom.css') ?>" rel="stylesheet">
</head>
<body class="app-body">

<div class="app-shell">
  <?= $this->include('partials/sidebar') ?>

  <!-- Main -->
  <main class="main">
    <div class="page-eyebrow">Registre</div>
    <h1 class="page-title">Historique des transactions</h1>

    <div class="section-heading">
      <h2>Compte <?= esc($currentUser['numero'] ?? '') ?></h2>
      <div class="see-all">Solde : Ar <?= number_format((float) ($currentUser['solde'] ?? 0), 0, ',', ' ') ?></div>
    </div>

    <div class="row g-3 align-items-center mb-2">
      <div class="col-md-8">
        <div class="search-bar">
          <i class="bi bi-search"></i>
          <input type="text" placeholder="Rechercher une transaction, un contact...">
        </div>
      </div>
      <div class="col-md-4 d-flex gap-2 justify-content-md-end">
        <button class="filter-btn"><i class="bi bi-funnel"></i></button>
        <button class="filter-btn"><i class="bi bi-calendar3"></i></button>
        <button class="filter-btn"><i class="bi bi-download"></i></button>
      </div>
    </div>

    <div class="card-ledger">
      <?php foreach ($transactions as $transaction) : ?>
        <div class="tx-row">
          <div class="tx-icon <?= $transaction['isPositive'] ? 'merchant-sale' : 'merchant-refund' ?>">
            <i class="bi <?= $transaction['isPositive'] ? 'bi-arrow-down-left' : 'bi-arrow-up-right' ?>"></i>
          </div>
          <div class="tx-info">
            <div class="tx-name"><?= esc($transaction['label']) ?> · <?= esc($transaction['counterparty']) ?></div>
            <div class="tx-date"><?= esc(date('d/m/Y · H:i', strtotime($transaction['instant']))) ?></div>
          </div>
          <div class="tx-amount <?= $transaction['isPositive'] ? 'pos' : 'neg' ?>">
            <?= $transaction['isPositive'] ? '+' : '−' ?>Ar <?= number_format(abs((float) $transaction['amount']), 0, ',', ' ') ?>
          </div>
        </div>
      <?php endforeach; ?>
    </div>

    <div class="d-flex justify-content-center mt-4">
      <?= $pager->links('transactions', 'bootstrap_full') ?>
    </div>

  </main>
</div>

</body>
</html>