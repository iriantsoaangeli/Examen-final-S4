<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Reçu de transfert — Encaisse</title>
<link href="bootstrap/css/bootstrap.css" rel="stylesheet">
<link href="bootstrap/css/icons.css" rel="stylesheet">
<link href="css/custom.css" rel="stylesheet">
</head>
<body class="app-body">

<div class="app-shell">

  <!-- Sidebar -->
  <aside class="sidebar">
    <div class="brand">
      <div class="brand-mark">E</div>
      <div class="brand-name">Encaisse</div>
      <div class="brand-sub">Caisse en ligne</div>
    </div>

    <ul class="nav-ledger">
      <li><a href="dashboard.html" class="nav-link"><i class="bi bi-wallet2"></i> Solde</a></li>
      <li><a href="transactions.html" class="nav-link"><i class="bi bi-clock-history"></i> Transactions</a></li>
      <li><a href="receipt.html" class="nav-link active"><i class="bi bi-receipt"></i> Reçus</a></li>
      <li><a href="#" class="nav-link"><i class="bi bi-graph-up"></i> Statistiques</a></li>
      <li><a href="#" class="nav-link"><i class="bi bi-gear"></i> Paramètres</a></li>
    </ul>

    <div class="sidebar-footer">
      <div class="avatar">TK</div>
      <div>
        <div style="color:var(--paper); font-weight:500;">Tanjiro Kamado</div>
        <div>Compte personnel</div>
      </div>
    </div>
  </aside>

  <!-- Main -->
  <main class="main">
    <div class="page-eyebrow">Confirmation</div>
    <h1 class="page-title">Reçu de transfert</h1>

    <div class="receipt-wrap">
      <div class="receipt">
        <div class="receipt-stamp">Payé</div>

        <div class="receipt-top">
          <div class="receipt-status"><i class="bi bi-check-lg"></i></div>
          <div class="receipt-headline">Vous avez envoyé <b>$32.00</b> à</div>
          <div class="receipt-headline"><b style="font-size:16px;">Nezuko Kamado</b></div>
          <div class="receipt-amount font-mono">$32.00</div>
        </div>

        <div class="receipt-body">
          <div class="receipt-line">
            <span class="k">Date</span>
            <span class="v">20 mai 2024, 10:48</span>
          </div>
          <div class="receipt-line">
            <span class="k">Type</span>
            <span class="v">BI-FAST</span>
          </div>
          <div class="receipt-line">
            <span class="k">Montant nominal</span>
            <span class="v">$32.00</span>
          </div>
          <div class="receipt-line">
            <span class="k">Frais</span>
            <span class="v" style="color:var(--accent-dark);">Gratuit</span>
          </div>
          <div class="receipt-line">
            <span class="k">Référence</span>
            <span class="v">EN-38291-0472</span>
          </div>
        </div>

        <div class="receipt-perf"></div>

        <div class="receipt-actions">
          <button class="btn-full" style="flex:1;"><i class="bi bi-share me-2"></i>Partager le reçu</button>
          <button class="btn-outline-ledger" style="flex:0 0 auto; width:auto; padding:11px 16px;">
            <i class="bi bi-download"></i>
          </button>
        </div>
      </div>
    </div>

  </main>
</div>

</body>
</html>