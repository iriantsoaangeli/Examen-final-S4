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
  <div class="login-box">

    <div class="brand-row">
      <div class="brand-mark" style="background:var(--accent); color:#fff;">E</div>
      <span class="font-display fs-4 fw-semibold">Encaisse</span>
    </div>

    <div class="page-eyebrow">Connexion</div>
    <h1 class="page-title">Votre numéro de téléphone</h1>
    <p class="lead">Il sert d'identifiant. Aucun mot de passe n'est nécessaire.</p>

    <?php if (session()->getFlashdata('error')) : ?>
      <div class="alert alert-danger py-2 px-3 mb-3" style="font-size:13.5px; border-radius:8px; text-align:left;">
        <?= esc(session()->getFlashdata('error')) ?>
      </div>
    <?php endif; ?>

    <form id="loginForm" action="<?= base_url('auth') ?>" method="post" novalidate>

      <div class="numero-field">
        <label for="numero">Numéro de téléphone</label>
        <div class="numero-input-group">
          <span class="prefix-tag"><i class="bi bi-telephone"></i></span>
          <input
            type="tel"
            id="numero"
            name="numero"
            placeholder="0341234567"
            inputmode="numeric"
            maxlength="10"
            autocomplete="tel"
            autofocus
            value="<?= esc(old('numero')) ?>"
            required
          >
        </div>
        <div id="numeroHint" class="numero-hint">10 chiffres, en commençant par 0.</div>
      </div>

      <button type="submit" class="btn-full" id="submitBtn">Se connecter</button>
    </form>

    <div class="login-footnote">
      <i class="bi bi-shield-check"></i> Orange et Telma pris en charge
    </div>

  </div>
</div>

<script src="/script/regex.js"></script>
<script>
  const numeroInput = document.getElementById('numero');
  const hint = document.getElementById('numeroHint');
  const form = document.getElementById('loginForm');
  const submitBtn = document.getElementById('submitBtn');

  const DEFAULT_HINT = '10 chiffres, en commençant par 0.';
  let lastCheckValid = false;

  function setHint(text, color) {
    hint.textContent = text;
    hint.style.color = color;
  }

  async function updateHint() {
    const numero = numeroInput.value.trim();

    if (numero === '') {
      setHint(DEFAULT_HINT, 'var(--muted)');
      lastCheckValid = false;
      return;
    }

    if (!checkNum(numero)) {
      setHint('Le numéro doit contenir exactement 10 chiffres.', 'var(--neg)');
      lastCheckValid = false;
      return;
    }

    setHint('Vérification…', 'var(--muted)');

    try {
      const prefixInfo = await getPrefix(numero);

      if (!prefixInfo) {
        setHint('Opérateur non reconnu.', 'var(--neg)');
        lastCheckValid = false;
        return;
      }

      setHint(`Opérateur détecté : ${prefixInfo.operator}`, 'var(--accent-dark)');
      lastCheckValid = true;
    } catch (err) {
      setHint('Vérification impossible pour le moment.', 'var(--neg)');
      lastCheckValid = false;
    }
  }

  numeroInput.addEventListener('input', () => {
    numeroInput.value = numeroInput.value.replace(/[^0-9]/g, '').slice(0, 10);
    updateHint();
  });

  form.addEventListener('submit', async (e) => {
    e.preventDefault();

    submitBtn.disabled = true;
    await updateHint();

    if (!lastCheckValid) {
      submitBtn.disabled = false;
      numeroInput.focus();
      return;
    }

    form.submit();
  });
</script>

</body>
</html>
