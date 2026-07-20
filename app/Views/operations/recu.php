<?php
$numero = (string) (session()->get('numero') ?? '');

$libelles = [
    'depot' => 'Dépôt',
    'retrait' => 'Retrait',
    'transfert' => 'Transfert',
];

if ($receipt !== null) {
    $type = $receipt['type'];
    $isSender = $receipt['sender'] === $numero;
    $contrepartie = $isSender ? $receipt['receiver'] : $receipt['sender'];
    $verbe = $type === 'retrait' ? 'retiré' : ($isSender ? 'envoyé' : 'reçu');
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Reçu — CASH</title>
<link href="<?= base_url('bootstrap/css/bootstrap.css') ?>" rel="stylesheet">
<link href="<?= base_url('bootstrap/css/icons.css') ?>" rel="stylesheet">
<link href="<?= base_url('css/custom.css') ?>" rel="stylesheet">
</head>
<body class="app-body">

<div class="app-shell">
  <?= $this->include('partials/sidebar') ?>

  <!-- Main -->
  <main class="main">
    <div class="page-eyebrow">Confirmation</div>
    <h1 class="page-title">Reçu de transaction</h1>

    <?php if ($receipt === null) : ?>
      <div class="card-ledger p-4">
        <div class="tx-row">
          <div class="tx-info">
            <div class="tx-name">Aucun reçu récent à afficher</div>
            <div class="tx-date">Effectuez un dépôt, un retrait ou un transfert pour voir un reçu ici.</div>
          </div>
        </div>
      </div>
    <?php else : ?>
      <div class="receipt-wrap">
        <div class="receipt">
          <div class="receipt-stamp">Payé</div>

          <div class="receipt-top">
            <div class="receipt-status"><i class="bi bi-check-lg"></i></div>
            <div class="receipt-headline">
              Vous avez <?= esc($verbe) ?> <b><?= number_format((float) $receipt['montant'], 2) ?>Ar</b>
              <?= $type === 'retrait' ? '' : ($isSender ? 'à' : 'de') ?>
            </div>
            <?php if ($type !== 'retrait') : ?>
              <div class="receipt-headline"><b style="font-size:16px;"><?= esc($contrepartie) ?></b></div>
            <?php endif; ?>
            <div class="receipt-amount font-mono"><?= number_format((float) $receipt['montant'], 2) ?>Ar</div>
          </div>

          <div class="receipt-body">
            <div class="receipt-line">
              <span class="k">Date</span>
              <span class="v"><?= esc(date('d M Y, H:i', strtotime($receipt['instant']))) ?></span>
            </div>
            <div class="receipt-line">
              <span class="k">Type</span>
              <span class="v"><?= esc($libelles[$type] ?? ucfirst($type)) ?></span>
            </div>
            <div class="receipt-line">
              <span class="k">Montant nominal</span>
              <span class="v"><?= number_format((float) $receipt['montant'], 2) ?>Ar</span>
            </div>
            <div class="receipt-line">
              <span class="k">Frais</span>
              <span class="v" style="color:var(--accent-dark);">
                <?= (float) $receipt['frais'] > 0 ? number_format((float) $receipt['frais'], 2) . 'Ar' : 'Gratuit' ?>
              </span>
            </div>
            <?php if (!empty($receipt['commission']) && (float) $receipt['commission'] > 0) : ?>
              <div class="receipt-line">
                <span class="k">Commission</span>
                <span class="v" style="color:var(--accent-dark);">
                  <?= number_format((float) $receipt['commission'], 2) ?>Ar
                </span>
              </div>
            <?php endif; ?>
            <div class="receipt-line">
              <span class="k">Description</span>
              <span class="v"><?= esc($receipt['description'] !== '' ? $receipt['description'] : '—') ?></span>
            </div>
            <div class="receipt-line">
              <span class="k">Référence</span>
              <span class="v">MVT-<?= esc(str_pad((string) $receipt['id_mvt'], 6, '0', STR_PAD_LEFT)) ?></span>
            </div>
          </div>

          <div class="receipt-perf"></div>

          <div class="receipt-actions">
            <a href="<?= base_url('transactions') ?>" class="btn-full" style="flex:1; text-align:center;">
              <i class="bi bi-clock-history me-2"></i>Voir l'historique
            </a>
            <a href="<?= base_url('dashboard') ?>" class="btn-outline-ledger" style="flex:0 0 auto; width:auto; padding:11px 16px;">
              <i class="bi bi-house"></i>
            </a>
          </div>
        </div>
      </div>
    <?php endif; ?>

  </main>
</div>

</body>
</html>
