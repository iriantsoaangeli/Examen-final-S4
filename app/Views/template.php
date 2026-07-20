<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Templates Bootstrap 5 — Design</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Manrope:wght@600;700;800&family=Inter:wght@400;500;600&family=JetBrains+Mono:wght@500;700&display=swap" rel="stylesheet">

<!-- styles.css doit être présent à côté de ce fichier -->
<link href="/css/style.css" rel="stylesheet">
</head>
<body>

<!-- =========================================================
     WRAPPER GENERIQUE (.page-wrapper — défini dans styles.css)
     Copiable n'importe où pour cadrer n'importe quel contenu.
========================================================= -->
<div class="page-wrapper">

  <!-- =========================================================
       TEMPLATE 1 : LISTE EN RECTANGLES (champs modulables)
       Copiable seul avec Bootstrap 5 + styles.css.
       Duplique le bloc <div class="card item-card ..."> par élément.
  ========================================================= -->

  <div class="section-heading">
    <span class="bar"></span>
    <h6>Mouvements récents</h6>
  </div>

  <div class="card item-card mb-3">
    <div class="card-body d-flex align-items-center gap-3">
      <div class="item-avatar"><i class="bi bi-arrow-down-left"></i></div>
      <div class="flex-grow-1">
        <h5 class="card-title mb-1">Dépôt reçu</h5>
        <span class="item-meta">Orange Money<span class="dot"></span>Réf. #40213<span class="dot"></span>20/07/2026</span>
      </div>
      <div class="text-end me-3">
        <div class="amount">+ 45 000 Ar</div>
        <span class="badge badge-status is-positive rounded-pill mt-1">Réussi</span>
      </div>
      <button class="btn btn-detail btn-sm">Détails</button>
    </div>
  </div>

  <div class="card item-card mb-3">
    <div class="card-body d-flex align-items-center gap-3">
      <div class="item-avatar"><i class="bi bi-arrow-up-right"></i></div>
      <div class="flex-grow-1">
        <h5 class="card-title mb-1">Transfert envoyé</h5>
        <span class="item-meta">MVola<span class="dot"></span>Réf. #40198<span class="dot"></span>19/07/2026</span>
      </div>
      <div class="text-end me-3">
        <div class="amount">− 12 500 Ar</div>
        <span class="badge badge-status rounded-pill mt-1">En attente</span>
      </div>
      <button class="btn btn-detail btn-sm">Détails</button>
    </div>
  </div>

  <hr class="my-5" style="border-color: var(--gray-300);">

  <!-- =========================================================
       TEMPLATE 2 : SOLDE (un chiffre en gros)
       Copiable seul avec Bootstrap 5 + styles.css.
  ========================================================= -->

  <div class="section-heading">
    <span class="bar"></span>
    <h6>Solde</h6>
  </div>

  <div class="card balance-card text-center border-0">
    <div class="card-body py-5 position-relative">
      <p class="balance-label mb-2 text-uppercase">Solde disponible</p>
      <h1 class="display-3 balance-figure mb-2">125 000 Ar</h1>
      <span class="balance-trend"><i class="bi bi-graph-up-arrow"></i> +2,4 % depuis hier</span>
      <p class="balance-updated mt-3 mb-0">Mis à jour le 20/07/2026 à 14:32</p>
    </div>
  </div>

  <hr class="my-5" style="border-color: var(--gray-300);">

  <!-- =========================================================
       TEMPLATE 3 : FORMULAIRE (champs modulables)
       Copiable seul avec Bootstrap 5 + styles.css.
       Duplique un bloc <div class="mb-3"> par champ.
  ========================================================= -->

  <div class="section-heading">
    <span class="bar"></span>
    <h6>Nouvelle opération</h6>
  </div>

  <div class="card form-card">
    <div class="card-body p-4">
      <h5 class="card-title mb-4">Effectuer un transfert</h5>
      <form>

        <div class="mb-3">
          <label for="champTexte" class="form-label">Numéro du destinataire</label>
          <input type="text" class="form-control" id="champTexte" placeholder="034 XX XXX XX">
        </div>

        <div class="mb-3">
          <label for="champNombre" class="form-label">Montant (Ar)</label>
          <input type="number" class="form-control font-mono" id="champNombre" placeholder="0">
        </div>

        <div class="mb-3">
          <label for="champSelect" class="form-label">Type d'opération</label>
          <select class="form-select" id="champSelect">
            <option selected disabled>Choisir...</option>
            <option value="1">Transfert</option>
            <option value="2">Retrait</option>
            <option value="3">Dépôt</option>
          </select>
        </div>

        <div class="mb-3">
          <label for="champDate" class="form-label">Date</label>
          <input type="date" class="form-control" id="champDate">
        </div>

        <div class="mb-4">
          <label for="champTextarea" class="form-label">Note (optionnel)</label>
          <textarea class="form-control" id="champTextarea" rows="3" placeholder="Motif de l'opération..."></textarea>
        </div>

        <button type="submit" class="btn btn-submit">Confirmer</button>
        <button type="reset" class="btn btn-cancel ms-2">Annuler</button>

      </form>
    </div>
  </div>

</div><!-- /.page-wrapper -->

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>