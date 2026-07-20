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
      <p class="text-muted mb-4" style="font-size:14px;">Entrez votre numéro de téléphone pour accéder à votre solde et vos transactions.</p>

      <?php if (session()->getFlashdata('error')) : ?>
        <div class="alert alert-danger py-2 px-3" style="font-size:13.5px; border-radius:8px;">
          <?= esc(session()->getFlashdata('error')) ?>
        </div>
      <?php endif; ?>

      <form class="form-ledger" id="loginForm" action="<?= base_url('auth') ?>" method="post" novalidate>

        <div class="mb-2">
          <label for="numero">Numéro de téléphone</label>
          <div class="position-relative">
            <input
              type="tel"
              class="form-control"
              id="numero"
              name="numero"
              placeholder="0341234567"
              inputmode="numeric"
              maxlength="10"
              autocomplete="tel"
              value="<?= esc(old('numero')) ?>"
              required
            >
          </div>
          <div id="numeroFeedback" class="mt-1" style="font-size:12.5px; color:var(--muted);">
            10 chiffres, en commençant par 0 (ex : Orange 032…, Telma 034… / 038…).
          </div>
        </div>

        <div class="mb-4"></div>

        <button type="submit" class="btn-full" id="submitBtn">Se connecter</button>

      </form>
    </div>
  </div>

</div>

<script src="/script/regex.js"></script>
<script>
  const numeroInput = document.getElementById('numero');
  const feedback = document.getElementById('numeroFeedback');
  const form = document.getElementById('loginForm');

  function updateFeedback() {
    const numero = numeroInput.value.trim();

    if (numero === '') {
      feedback.textContent = '10 chiffres, en commençant par 0 (ex : Orange 032…, Telma 034… / 038…).';
      feedback.style.color = 'var(--muted)';
      return true;
    }

    if (!checkNum(numero)) {
      feedback.textContent = 'Le numéro doit contenir exactement 10 chiffres et commencer par 0.';
      feedback.style.color = 'var(--neg)';
      return false;
    }

    if (!checkPrefix(getPrefix(numero))) {
      feedback.textContent = 'Opérateur non reconnu (préfixes valides : 032, 034, 038).';
      feedback.style.color = 'var(--neg)';
      return false;
    }

    feedback.textContent = 'Numéro valide.';
    feedback.style.color = 'var(--accent-dark)';
    return true;
  }

  numeroInput.addEventListener('input', () => {
    numeroInput.value = numeroInput.value.replace(/[^0-9]/g, '').slice(0, 10);
    updateFeedback();
  });

  form.addEventListener('submit', (e) => {
    if (!updateFeedback()) {
      e.preventDefault();
      numeroInput.focus();
    }
  });
</script>

</body>
</html>