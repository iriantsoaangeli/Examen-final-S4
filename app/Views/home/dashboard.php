<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Solde — Encaisse</title>
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
      <li><a href="dashboard.html" class="nav-link active"><i class="bi bi-wallet2"></i> Solde</a></li>
      <li><a href="transactions.html" class="nav-link"><i class="bi bi-clock-history"></i> Transactions</a></li>
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
    <div class="page-eyebrow">Aperçu du compte</div>
    <h1 class="page-title">Bonjour, Tanjiro</h1>

    <div class="row g-4">
      <!-- Balance card -->
      <div class="col-lg-7">
        <div class="balance-card">
          <div class="balance-label">Solde disponible</div>
          <div class="balance-amount">$32,149<span class="cents font-mono">.00</span></div>
          <div class="balance-sub">Mis à jour aujourd'hui à 09:41</div>

          <div class="balance-actions">
            <button class="btn-ledger primary"><i class="bi bi-arrow-up-right"></i> Envoyer</button>
            <button class="btn-ledger"><i class="bi bi-arrow-down-left"></i> Retirer</button>
            <button class="btn-ledger"><i class="bi bi-graph-up-arrow"></i> Investir</button>
            <button class="btn-ledger"><i class="bi bi-plus-lg"></i> Ajouter</button>
          </div>
        </div>
      </div>

      <!-- Stat cards -->
      <div class="col-lg-5">
        <div class="row g-3 h-100">
          <div class="col-6">
            <div class="stat-card">
              <div class="icon-badge" style="background:#E7F5EC; color:var(--accent-dark);"><i class="bi bi-arrow-down-circle"></i></div>
              <div class="label">Entrées (mois)</div>
              <div class="value">$81.98</div>
            </div>
          </div>
          <div class="col-6">
            <div class="stat-card">
              <div class="icon-badge" style="background:#FBEAE7; color:var(--neg);"><i class="bi bi-arrow-up-circle"></i></div>
              <div class="label">Sorties (mois)</div>
              <div class="value">$52.00</div>
            </div>
          </div>
          <div class="col-12">
            <div class="stat-card">
              <div class="d-flex justify-content-between align-items-start">
                <div>
                  <div class="icon-badge" style="background:#FBF3E1; color:var(--gold);"><i class="bi bi-piggy-bank"></i></div>
                  <div class="label">Dépenses hebdo.</div>
                  <div class="value">$320 <span style="font-size:13px; color:var(--muted); font-weight:400;">/ budget</span></div>
                </div>
                <span class="badge rounded-pill" style="background:#E7F5EC; color:var(--accent-dark); font-weight:500;">Dans les clous</span>
              </div>
              <div class="progress mt-3" style="height:6px; background:var(--paper-alt);">
                <div class="progress-bar" style="width:64%; background:var(--accent);"></div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Recent transactions preview -->
    <div class="section-heading">
      <h2>Transactions récentes</h2>
      <a href="transactions.html" class="see-all">Voir tout <i class="bi bi-arrow-right"></i></a>
    </div>

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
        <div class="tx-icon merchant-sale"><i class="bi bi-cash-coin"></i></div>
        <div class="tx-info">
          <div class="tx-name">Revenus de vente</div>
          <div class="tx-date">19 mai · 09:01</div>
        </div>
        <div class="tx-amount pos">+$23.99</div>
      </div>
      <div class="tx-row">
        <div class="tx-icon merchant-refund"><i class="bi bi-arrow-counterclockwise"></i></div>
        <div class="tx-info">
          <div class="tx-name">Remboursement reçu</div>
          <div class="tx-date">19 mai · 13:28</div>
        </div>
        <div class="tx-amount pos">+$45.50</div>
      </div>
    </div>

  </main>
</div>

</body>
</html>