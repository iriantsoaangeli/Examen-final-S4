<?php
$numero = (string) (session()->get('numero') ?? '');
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Transfert Multiple — CASH</title>
<link href="<?= base_url('bootstrap/css/bootstrap.css') ?>" rel="stylesheet">
<link href="<?= base_url('bootstrap/css/icons.css') ?>" rel="stylesheet">
<link href="<?= base_url('css/custom.css') ?>" rel="stylesheet">
</head>
<body class="app-body">

<div class="app-shell">
	<?= $this->include('partials/sidebar') ?>

	<main class="main">
		<div class="page-eyebrow">Opération</div>
		<h1 class="page-title">Effectuer un transfert multiple</h1>

		<?php if (session('error')) : ?>
			<div class="alert-ledger error">
				<i class="bi bi-exclamation-triangle"></i>
				<span><?= esc(session('error')) ?></span>
			</div>
		<?php endif; ?>

		<div class="card-ledger p-4">
			<div class="op-header">
				<div class="icon-badge" style="background:#E9EEFB; color:#2A4FD6;"><i class="bi bi-diagram-3"></i></div>
				<div>
					<div class="op-title">Transfert multiple</div>
					<div class="op-sub">Envoyez le même montant à plusieurs destinataires en une fois.</div>
				</div>
			</div>
			<form action="<?= base_url('operations/transfert-multiple') ?>" method="post" class="row g-3" id="transferMultipleForm">
				<div class="col-12 col-md-6">
					<label class="form-label">Votre numéro</label>
					<input type="text" class="form-control" name="numero_sender" value="<?= esc($numero) ?>" readonly>
				</div>
				
				<div class="col-12 col-md-6">
					<label class="form-label">Ajouter un destinataire</label>
					<div class="input-group">
						<input type="text" class="form-control" id="numero_input"  maxlength="10" placeholder="Ex: 0341234567 ou 0691234567">
						<button class="btn btn-outline-success" type="button" id="btn_add_numero">
							<i class="bi bi-plus-lg"></i>
						</button>
					</div>
					<small class="text-danger d-none" id="numero_error">Numéro ou préfixe invalide.</small>
				</div>

				<!-- Fieldset dynamique pour lister les numéros validés en multiselect -->
				<div class="col-12">
					<fieldset class="card-ledger p-3 d-none" id="destinataires_fieldset">
						<legend class="float-none w-auto px-2">Liste des destinataires</legend>
						<div id="destinataires_container" class="d-flex flex-column gap-2">
							<!-- Les lignes de numéro avec checkbox + bouton (-) seront insérées ici par JS -->
						</div>
					</fieldset>
				</div>

				<div class="col-12 col-md-6">
					<label class="form-label">Montant par destinataire</label>
					<input type="number" class="form-control" name="montant" min="1" step="0.01" required>
				</div>
				<div class="col-12 col-md-6">
					<label class="form-label">Description</label>
					<input type="text" class="form-control" name="description" placeholder="Transfert multiple">
				</div>

				<div class="col-12 d-flex gap-2 mt-4">
					<button type="submit" class="btn-ledger primary" id="btn_submit" disabled>Envoyer les transferts</button>
					<a href="<?= base_url('dashboard') ?>" class="btn btn-secondary">Annuler</a>
				</div>
			</form>
		</div>
	</main>
</div>

<script src="<?= base_url('script/multi.js') ?>"></script>
</body>
</html>