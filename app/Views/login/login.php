<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Connexion — Encaisse</title>
<link href="bootstrap/css/bootstrap.css" rel="stylesheet">
<link href="bootstrap/css/icons.css" rel="stylesheet">
<link href="/css/custom.css" rel="stylesheet">
</head>
<body class="app-body">

<div class="login-shell">

  <!-- Panneau visuel -->
  <div class="login-visual d-none d-lg-flex">
    <div class="d-flex align-items-center gap-2">
      <div class="brand-mark">E</div>
      <span class="font-display fs-4 fw-semibold">Encaisse</span>
    </div>

    <div class="quote">
      « Tenir ses comptes à jour, c'est déjà se donner
      une longueur d'avance sur demain. »
      <div class="quote-attr">— Le registre Encaisse</div>
    </div>

    <div class="d-flex gap-4">
      <div>
        <div class="font-display fs-3 fw-semibold">128k+</div>
        <div class="font-mono" style="font-size:12px; color:rgba(241,243,236,0.55); text-transform:uppercase; letter-spacing:1px;">Comptes actifs</div>
      </div>
      <div>
        <div class="font-display fs-3 fw-semibold">4.9/5</div>
        <div class="font-mono" style="font-size:12px; color:rgba(241,243,236,0.55); text-transform:uppercase; letter-spacing:1px;">Satisfaction</div>
      </div>
    </div>
  </div>

  <!-- Formulaire -->
  <div class="login-form-side">
    <div class="login-box">

      <div class="brand-row d-lg-none">
        <div class="brand-mark" style="background:var(--accent); color:#fff;">E</div>
        <span class="font-display fs-4 fw-semibold">Encaisse</span>
      </div>

      <div class="page-eyebrow">Bienvenue</div>
      <h1 class="page-title" style="margin-bottom:8px;">Connexion à votre caisse</h1>
      <p class="text-muted mb-4" style="font-size:14px;">Entrez vos identifiants pour accéder à votre solde et vos transactions.</p>

      <form class="form-ledger">
        <div class="mb-3">
          <label for="email">Adresse e-mail</label>
          <input type="email" class="form-control" id="email" placeholder="vous@exemple.com">
        </div>

        <div class="mb-2">
          <label for="password">Mot de passe</label>
          <div class="position-relative">
            <input type="password" class="form-control" id="password" placeholder="••••••••">
          </div>
        </div>

        <div class="d-flex justify-content-between align-items-center mb-4" style="font-size:13px;">
          <div class="form-check">
            <input class="form-check-input" type="checkbox" id="remember">
            <label class="form-check-label text-muted" for="remember">Se souvenir de moi</label>
          </div>
          <a href="#" class="fw-medium" style="color:var(--accent-dark);">Mot de passe oublié ?</a>
        </div>

        <button type="submit" class="btn-full">Se connecter</button>

        <div class="divider-or">ou</div>

        <button type="button" class="btn-outline-ledger mb-2">
          <i class="bi bi-google"></i> Continuer avec Google
        </button>
        <button type="button" class="btn-outline-ledger">
          <i class="bi bi-apple"></i> Continuer avec Apple
        </button>
      </form>

      <p class="text-center text-muted mt-4" style="font-size:13.5px;">
        Pas encore de compte ? <a href="#" class="fw-semibold" style="color:var(--ink);">Créer un compte</a>
      </p>
    </div>
  </div>

</div>

</body>
</html>