<?php

namespace App\Controllers\operations;

use App\Controllers\BaseController;
use App\Models\MvtDetailsModel;
use App\Models\MvtModel;
use App\Models\PrefixModel;
use App\Models\TrancheModel;
use App\Models\TypeMvtModel;
use App\Models\UserModel;

class Operation extends BaseController
{
    private const PROVIDER_NUMERO = '0340000000';

    public function depot()
    {
        if (strtolower($this->request->getMethod()) !== 'post') {
            return view('operations/depot');
        }

        return $this->executerOperation('depot');
    }

    public function retrait()
    {
        if (strtolower($this->request->getMethod()) !== 'post') {
            return view('operations/retrait');
        }

        return $this->executerOperation('retrait');
    }

    public function transfert()
    {
        if (strtolower($this->request->getMethod()) !== 'post') {
            return view('operations/transfert');
        }

        return $this->executerOperation('transfert');
    }

    public function historique(string $numero)
    {
        $mouvements = (new MvtModel())->getMouvementsByUser($numero);

        return $this->response->setJSON($mouvements);
    }

    public function gains()
    {
        return $this->response->setJSON((new MvtModel())->getGainFrais());
    }

    public function comptes()
    {
        return $this->response->setJSON((new UserModel())->getClients());
    }

    private function executerOperation(string $type)
    {
        $montant = (float) $this->request->getPost('montant');
        $description = (string) ($this->request->getPost('description') ?? '');

        if ($montant <= 0) {
            return $this->erreur('Le montant doit être supérieur à 0.');
        }

        $userModel = new UserModel();
        $typeModel = new TypeMvtModel();
        $trancheModel = new TrancheModel();
        $mvtModel = new MvtModel();
        $mvtDetailsModel = new MvtDetailsModel();
        $db = db_connect();

        $typeId = $typeModel->getIdByLibelle($type);
        if ($typeId === null) {
            return $this->erreur("Le type d'opération {$type} n'existe pas.");
        }

        $tranche = $trancheModel->findByTypeAndMontant($typeId, $montant);
        if ($tranche === null) {
            return $this->erreur('Aucune tranche de frais ne correspond à ce montant.');
        }

        $frais = (float) $tranche['frais'];
        $sender = null;
        $receiver = null;

        if ($type === 'depot') {
            $receiver = $this->getClientByNumero((string) $this->request->getPost('numero_receiver'), $userModel);
            $sender = $userModel->find(self::PROVIDER_NUMERO);
        } elseif ($type === 'retrait') {
            $sender = $this->getClientByNumero((string) $this->request->getPost('numero_sender'), $userModel);
            $receiver = $userModel->find(self::PROVIDER_NUMERO);
        } else {
            $sender = $this->getClientByNumero((string) $this->request->getPost('numero_sender'), $userModel);
            $receiver = $this->getClientByNumero((string) $this->request->getPost('numero_receiver'), $userModel);
        }

        if ($sender === null || $receiver === null) {
            return $this->erreur('Compte introuvable pour cette opération.');
        }

        if ($sender['numero'] === $receiver['numero']) {
            return $this->erreur('Le compte source et le compte destinataire doivent être différents.');
        }

        $debit = $type === 'depot' ? 0 : $montant + $frais;
        if ($debit > 0 && (float) $sender['solde'] < $debit) {
            return $this->erreur('Solde insuffisant.');
        }

        $db->transStart();

        if ($debit > 0) {
            $this->incrementerSolde($sender['numero'], -$debit);
        }

        if ($type !== 'retrait') {
            $this->incrementerSolde($receiver['numero'], $montant);
        }

        $mvtId = $mvtModel->insert([
            'montant' => $montant,
            'frais' => $frais,
            'id_type' => $typeId,
            'num_sender' => $sender['numero'],
            'num_receiver' => $receiver['numero'],
            'description' => $description,
            'instant' => date('Y-m-d H:i:s'),
        ], true);

        $mvtDetailsModel->insert([
            'id_mvt' => $mvtId,
            'id_tranche' => (int) $tranche['id'],
        ]);

        $db->transComplete();

        if (! $db->transStatus()) {
            return $this->erreur("L'opération n'a pas pu être enregistrée.", 500);
        }

        return $this->response->setJSON([
            'success' => true,
            'id_mvt' => $mvtId,
            'type' => $type,
            'montant' => $montant,
            'frais' => $frais,
        ]);
    }

    private function getClientByNumero(string $numero, UserModel $userModel): ?array
    {
        $numero = trim($numero);

        if ($numero === '' || ! preg_match('/^[0-9]{10}$/', $numero)) {
            return null;
        }

        if (! (new PrefixModel())->isValidNumero($numero)) {
            return null;
        }

        $user = $userModel->findByNumero($numero);

        if ($user === null || (int) $user['is_provider'] === 1) {
            return null;
        }

        return $user;
    }

    private function incrementerSolde(string $numero, float $montant): void
    {
        db_connect()->table('user')
            ->set('solde', 'solde + ' . $montant, false)
            ->where('numero', $numero)
            ->update();
    }

    private function erreur(string $message, int $status = 400)
    {
        return $this->response
            ->setStatusCode($status)
            ->setJSON([
                'success' => false,
                'message' => $message,
            ]);
    }
}
