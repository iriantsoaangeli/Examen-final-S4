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

    <?php if (empty($groupes)) : ?>
      <div class="card-ledger">
        <div class="tx-row">
          <div class="tx-info">
            <div class="tx-name">Aucune transaction pour le moment</div>
          </div>
        </div>
      </div>
    <?php else : ?>
      <?php foreach ($groupes as $groupe) : ?>
        <div class="tx-group-label"><?= esc($groupe['label']) ?></div>
        <div class="card-ledger">
          <?php foreach ($groupe['items'] as $item) : ?>
            <div class="tx-row">
              <div class="tx-icon <?= esc($item['merchantClass'], 'attr') ?>"><i class="bi <?= esc($item['icon'], 'attr') ?>"></i></div>
              <div class="tx-info">
                <div class="tx-name"><?= esc($item['label']) ?> · <?= esc($item['counterparty']) ?></div>
                <div class="tx-date"><?= esc($item['heure']) ?></div>
              </div>
              <div class="tx-amount <?= $item['isPositive'] ? 'pos' : 'neg' ?>">
                <?= $item['isPositive'] ? '+' : '−' ?>$<?= number_format(abs($item['amount']), 2) ?>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      <?php endforeach; ?>

      <div class="d-flex justify-content-center mt-4">
        <?= $pager->links('default', 'ledger') ?>
      </div>
    <?php endif; ?>

  </main>
</div>

</body>
</html>
