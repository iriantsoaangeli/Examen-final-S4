<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Transactions — CASH</title>
<link href="bootstrap/css/bootstrap.css" rel="stylesheet">
<link href="bootstrap/css/icons.css" rel="stylesheet">
<link href="css/custom.css" rel="stylesheet">
</head>
<body class="app-body">

<div class="app-shell">

  <!-- Sidebar -->
  <aside class="sidebar">
    <div class="brand">
      <div class="brand-mark">C</div>
      <div class="brand-name">CASH</div>
      <div class="brand-sub">Caisse en ligne</div>
    </div>

    <ul class="nav-ledger">
      <li><a href="dashboard.html" class="nav-link"><i class="bi bi-wallet2"></i> Solde</a></li>
      <li><a href="transactions.html" class="nav-link active"><i class="bi bi-clock-history"></i> Transactions</a></li>
      <li><a href="receipt.html" class="nav-link"><i class="bi bi-receipt"></i> Reçus</a></li>
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

    <!-- Aujourd'hui -->
    <div class="tx-group-label">Aujourd'hui</div>
    <div class="card-ledger">
      <div class="tx-row">
        <div class="tx-icon merchant-vimeo"><i class="bi bi-vimeo"></i></div>
        <div class="tx-info">
          <div class="tx-name">Abonnement Vimeo</div>
          <div class="tx-date">20 mai · 13:28</div>
        </div>
        <div class="tx-amount neg">−$20.00</div>
      </div>
      <div class="tx-row">
        <div class="tx-icon merchant-video"><i class="bi bi-youtube"></i></div>
        <div class="tx-info">
          <div class="tx-name">Paiements créateur</div>
          <div class="tx-date">20 mai · 10:32</div>
        </div>
        <div class="tx-amount pos">+$12.99</div>
      </div>
      <div class="tx-row">
        <div class="tx-icon merchant-pay"><i class="bi bi-paypal"></i></div>
        <div class="tx-info">
          <div class="tx-name">Paiement d'achat</div>
          <div class="tx-date">20 mai · 09:24</div>
        </div>
        <div class="tx-amount neg">−$32.00</div>
      </div>
      <div class="tx-row">
        <div class="tx-icon merchant-sale"><i class="bi bi-cash-coin"></i></div>
        <div class="tx-info">
          <div class="tx-name">Revenus de vente</div>
          <div class="tx-date">20 mai · 09:01</div>
        </div>
        <div class="tx-amount pos">+$23.99</div>
      </div>
    </div>

    <!-- 19 mai -->
    <div class="tx-group-label">19 mai 2025</div>
    <div class="card-ledger">
      <div class="tx-row">
        <div class="tx-icon merchant-refund"><i class="bi bi-arrow-counterclockwise"></i></div>
        <div class="tx-info">
          <div class="tx-name">Remboursement reçu</div>
          <div class="tx-date">19 mai · 13:28</div>
        </div>
        <div class="tx-amount pos">+$45.50</div>
      </div>
      <div class="tx-row">
        <div class="tx-icon merchant-transfer"><i class="bi bi-paypal"></i></div>
        <div class="tx-info">
          <div class="tx-name">Virement entrant</div>
          <div class="tx-date">19 mai · 09:24</div>
        </div>
        <div class="tx-amount pos">+$89.75</div>
      </div>
    </div>

    <div class="d-flex justify-content-center mt-4">
      <button class="btn-ledger" style="background:var(--card); color:var(--ink); border-color:var(--paper-line);">
        Charger plus <i class="bi bi-chevron-down"></i>
      </button>
    </div>

  </main>
</div>

</body>
</html>