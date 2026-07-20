<?php
$numero = (string) (session()->get('numero') ?? '');
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Retrait — CASH</title>
<link href="<?= base_url('bootstrap/css/bootstrap.css') ?>" rel="stylesheet">
<link href="<?= base_url('bootstrap/css/icons.css') ?>" rel="stylesheet">
<link href="<?= base_url('css/custom.css') ?>" rel="stylesheet">
</head>
<body class="app-body">

<div class="app-shell">
	<?= $this->include('partials/sidebar') ?>

	<main class="main">
		<div class="page-eyebrow">Opération</div>
		<h1 class="page-title">Effectuer un retrait</h1>

		<?php if (session('error')) : ?>
			<div class="alert-ledger error">
				<i class="bi bi-exclamation-triangle"></i>
				<span><?= esc(session('error')) ?></span>
			</div>
		<?php endif; ?>

		<div class="card-ledger p-4">
			<form action="<?= base_url('operations/retrait') ?>" method="post" class="row g-3">
				<div class="col-12 col-md-6">
					<label class="form-label">Votre numéro</label>
					<input type="text" class="form-control" name="numero_sender" value="<?= esc($numero) ?>" readonly>
				</div>
				<div class="col-12 col-md-6">
					<label class="form-label">Montant</label>
					<input type="number" class="form-control" name="montant" min="1" step="0.01" required>
				</div>
				<div class="col-12">
					<label class="form-label">Description</label>
					<input type="text" class="form-control" name="description" placeholder="Retrait en espèces">
				</div>
				<div class="col-12">
					<button type="submit" class="btn-ledger primary">Valider le retrait</button>
				</div>
			</form>
		</div>
	</main>
</div>

</body>
</html>
