<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Solde — CASH</title>
<link href="<?= base_url('bootstrap/css/bootstrap.css') ?>" rel="stylesheet">
<link href="<?= base_url('bootstrap/css/icons.css') ?>" rel="stylesheet">
<link href="<?= base_url('css/custom.css') ?>" rel="stylesheet">
</head>
<body class="app-body">

<div class="app-shell">
  <?= $this->include('partials/sidebar') ?>

  <!-- Main -->
  <main class="main">
    <div class="page-eyebrow">Aperçu du compte</div>
    <h1 class="page-title">Bonjour, <?= esc($currentUser['numero'] ?? session()->get('numero') ?? 'utilisateur') ?></h1>

    <div class="row g-4">
      <!-- Balance card -->
      <div class="col-lg-7">
        <div class="balance-card">
          <div class="balance-label">Solde disponible</div>
          <div class="balance-amount">Ar <?= number_format((float) ($currentUser['solde'] ?? 0), 0, ',', ' ') ?></div>
          <div class="balance-sub">Mis à jour aujourd'hui à 09:41</div>

          <div class="balance-actions">
            <button class="btn-ledger primary"><i class="bi bi-arrow-up-right"></i> Envoyer</button>
            <button class="btn-ledger"><i class="bi bi-arrow-down-left"></i> Retirer</button>
            <button class="btn-ledger"><i class="bi bi-plus-lg"></i> Recharger</button>
          </div>
        </div>
      </div>

      <div class="col-lg-5">
        <div class="stat-card h-100">
          <div class="icon-badge" style="background:#E7F5EC; color:var(--accent-dark);"><i class="bi bi-wallet2"></i></div>
          <div class="label">Portefeuille</div>
          <div class="value">Ar <?= number_format((float) ($currentUser['solde'] ?? 0), 0, ',', ' ') ?></div>
          <div class="balance-sub" style="color:var(--muted);">Solde actualisé depuis la base</div>
        </div>
      </div>
    </div>

    <!-- Recent transactions preview -->
    <div class="section-heading">
      <h2>3 dernières transactions</h2>
      <a href="<?= base_url('transactions') ?>" class="see-all">Voir tout <i class="bi bi-arrow-right"></i></a>
    </div>

    <div class="card-ledger">
      <?php foreach ($recentTransactions as $transaction) : ?>
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

  </main>
</div>

</body>
</html>