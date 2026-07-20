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

	public function transactions()
	{
		$numero = (string) (session()->get('numero') ?? '');

		if ($numero === '') {
			return redirect()->to('/login');
		}

		$mvtModel = new MvtModel();
		$result = $mvtModel->getPaginatedForUser($numero, 10);

		$groupes = [];
		foreach ($result['transactions'] as $transaction) {
			$isSender = $transaction['num_sender'] === $numero;
			$isDeposit = $transaction['type_libelle'] === 'depot';
			$isWithdraw = $transaction['type_libelle'] === 'retrait';
			$counterpartyNom = $isSender
				? ($transaction['receiver_nom'] ?? $transaction['num_receiver'])
				: ($transaction['sender_nom'] ?? $transaction['num_sender']);

			if ($isDeposit) {
				$label = $isSender ? 'Dépôt envoyé' : 'Dépôt reçu';
				$icon = 'bi-cash-coin';
				$merchantClass = 'merchant-sale';
			} elseif ($isWithdraw) {
				$label = $isSender ? 'Retrait effectué' : 'Retrait reçu';
				$icon = 'bi-arrow-down-left';
				$merchantClass = 'merchant-refund';
			} else {
				$label = $isSender ? 'Transfert envoyé' : 'Transfert reçu';
				$icon = 'bi-arrow-left-right';
				$merchantClass = 'merchant-transfer';
			}

			$amount = (float) $transaction['montant'];
			$displayAmount = $isSender ? -($amount + (float) $transaction['frais']) : $amount;

			$instant = $transaction['instant'];
			$timestamp = strtotime($instant);
			$dateKey = date('Y-m-d', $timestamp);

			if ($dateKey === date('Y-m-d')) {
				$dateLabel = "Aujourd'hui";
			} elseif ($dateKey === date('Y-m-d', strtotime('-1 day'))) {
				$dateLabel = 'Hier';
			} else {
				$dateLabel = ucfirst(date('d ', $timestamp) . $this->moisFr((int) date('n', $timestamp)) . date(' Y', $timestamp));
			}

			$groupes[$dateKey]['label'] ??= $dateLabel;
			$groupes[$dateKey]['items'][] = [
				'label' => $label,
				'icon' => $icon,
				'merchantClass' => $merchantClass,
				'counterparty' => $counterpartyNom,
				'heure' => date('H:i', $timestamp),
				'amount' => $displayAmount,
				'isPositive' => $displayAmount >= 0,
			];
		}
		krsort($groupes);

		return view('history/historique', [
			'activePage' => 'transactions',
			'groupes' => $groupes,
			'pager' => $result['pager'],
		]);
	}

	private function moisFr(int $numeroMois): string
	{
		$mois = [
			1 => 'janvier', 2 => 'février', 3 => 'mars', 4 => 'avril',
			5 => 'mai', 6 => 'juin', 7 => 'juillet', 8 => 'août',
			9 => 'septembre', 10 => 'octobre', 11 => 'novembre', 12 => 'décembre',
		];

		return $mois[$numeroMois] ?? '';
	}

}
