<?php

namespace App\Controllers;

use App\Models\MvtModel;
use App\Models\UserModel;

class Home extends BaseController
{
	public function dashboard()
	{
		$numero = (string) (session()->get('numero') ?? '');

		if ($numero === '') {
			return redirect()->to('/login');
		}

		$userModel = new UserModel();
		$mvtModel = new MvtModel();
		$currentUser = $userModel->findByNumero($numero);

		if ($currentUser === null) {
			return redirect()->to('/login');
		}

		$recentTransactions = array_map(
			function (array $transaction) use ($numero): array {
				$isSender = $transaction['num_sender'] === $numero;
				$isDeposit = $transaction['type_libelle'] === 'depot';
				$isWithdraw = $transaction['type_libelle'] === 'retrait';
				$counterpartyNom = $isSender ? ($transaction['receiver_nom'] ?? $transaction['num_receiver']) : ($transaction['sender_nom'] ?? $transaction['num_sender']);

				if ($isDeposit) {
					$label = $isSender ? 'Dépôt envoyé' : 'Dépôt reçu';
				} elseif ($isWithdraw) {
					$label = $isSender ? 'Retrait effectué' : 'Retrait reçu';
				} else {
					$label = $isSender ? 'Transfert envoyé' : 'Transfert reçu';
				}

				$amount = (float) $transaction['montant'];
				$displayAmount = $isSender ? -($amount + (float) $transaction['frais']) : $amount;

				return [
					'label' => $label,
					'counterparty' => $counterpartyNom,
					'instant' => $transaction['instant'],
					'amount' => $displayAmount,
					'isPositive' => $displayAmount >= 0,
				];
			},
			$mvtModel->getRecentForUser($numero, 3)
		);

		return view('home/dashboard', [
			'activePage' => 'dashboard',
			'currentUser' => $currentUser,
			'recentTransactions' => $recentTransactions,
		]);
	}

	public function transactions(): string
	{
		return view('history/historique', [
			'activePage' => 'transactions',
		]);
	}

}
