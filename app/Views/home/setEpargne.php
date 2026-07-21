<?php
$numero = (string) (session()->get('numero') ?? '');
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Dépôt — CASH</title>
<link href="<?= base_url('bootstrap/css/bootstrap.css') ?>" rel="stylesheet">
<link href="<?= base_url('bootstrap/css/icons.css') ?>" rel="stylesheet">
<link href="<?= base_url('css/custom.css') ?>" rel="stylesheet">
</head>
<body class="app-body">

<div class="app-shell">
	<?= $this->include('partials/sidebar') ?>

	<main class="main">
        		<h1 class="page-title">Effectuer un dépôt</h1>

		<?php if (session('error')) : ?>
			<div class="alert-ledger error">
				<i class="bi bi-exclamation-triangle"></i>
				<span><?= esc(session('error')) ?></span>
			</div>
		<?php endif; ?>

		<div class="card-ledger p-4">
			<div class="op-header">
				<div class="icon-badge" style="background:#E7F1EA; color:var(--accent-dark);"><i class="bi bi-plus-lg"></i></div>
				<div>
					<div class="op-title">Nouveau dépôt</div>
					<div class="op-sub">Créditez votre compte instantanément.</div>
				</div>
			</div>
			<form action="<?= base_url('epargne/set') ?>" method="post" class="row g-3">
				<div class="col-12 col-md-6">
					<label class="form-label">Pourcentage</label>
					<input type="number" class="form-control" name="pourcentage" min="1" step="1" max="100" required>
				</div>
				<div class="col-12">
					<button type="submit" class="btn-ledger primary">Valider le pourcentage</button>
				</div>
			</form>
		</div>
	</main>
</div>

</body>
</html>
