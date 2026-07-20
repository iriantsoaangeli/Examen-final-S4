<?php

namespace App\Controllers\operations;

use App\Controllers\BaseController;
use App\Models\MvtDetailsModel;
use App\Models\MvtModel;
use App\Models\PrefixModel;
use App\Models\TrancheModel;
use App\Models\TypeMvtModel;
use App\Models\UserModel;
use RuntimeException;

class Operation extends BaseController
{
    private UserModel $userModel;
    private MvtModel $mvtModel;
    private PrefixModel $prefixModel;
    private TrancheModel $trancheModel;
    private TypeMvtModel $typeMvtModel;
    private MvtDetailsModel $mvtDetailsModel;

    private ?string $providerNumero = null;

    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->mvtModel = new MvtModel();
        $this->prefixModel = new PrefixModel();
        $this->trancheModel = new TrancheModel();
        $this->typeMvtModel = new TypeMvtModel();
        $this->mvtDetailsModel = new MvtDetailsModel();
    }

    private function getProviderNumero(): string
    {
        if ($this->providerNumero === null) {
            $this->providerNumero ??= $this->userModel->getProviderNumero();
        }
        return $this->providerNumero;
    }

    public function depot()
    {
        if (strtolower($this->request->getMethod()) !== 'post') {
            return view('operations/depot', [
                'activePage' => 'depot',
            ]);
        }
        return $this->executerOperation('depot');
    }

    public function retrait()
    {
        if (strtolower($this->request->getMethod()) !== 'post') {
            return view('operations/retrait', [
                'activePage' => 'retrait',
            ]);
        }
        return $this->executerOperation('retrait');
    }

    public function transfert()
    {
        if (strtolower($this->request->getMethod()) !== 'post') {
            return view('operations/transfert', [
                'activePage' => 'transfert',
            ]);
        }
        return $this->executerOperation('transfert');
    }

    public function recu()
    {
        $receipt = session()->getFlashdata('receipt');

        return view('operations/recu', [
            'activePage' => 'receipt',
            'receipt' => $receipt,
        ]);
    }

    public function historique(string $numero)
    {
        return $this->response->setJSON($this->mvtModel->getMouvementsByUser($numero));
    }

    private function executerOperation(string $type)
    {
        try {
            $montant = (float) $this->request->getPost('montant');
            if ($montant <= 0) {
                throw new RuntimeException('Le montant doit être supérieur à 0.');
            }

            $typeId = $this->resoudreTypeId($type);
            $tranche = $this->resoudreTranche($typeId, $montant);
            [$sender, $receiver] = $this->resoudreParticipants($type);

            $debit = $type === 'depot' ? 0 : $montant + (float) $tranche['frais'];

            if ($debit > 0 && (float) $sender['solde'] < $debit) {
                throw new RuntimeException('Solde insuffisant.');
            }

            $instant = date('Y-m-d H:i:s');
            $mvtId = $this->enregistrerMouvement($type, $typeId, $tranche, $sender, $receiver, $montant, $debit, $instant);
        } catch (RuntimeException $e) {
            return redirect()->back()->withInput()->with('error', $e->getMessage());
        }

        session()->setFlashdata('receipt', [
            'id_mvt' => $mvtId,
            'type' => $type,
            'montant' => $montant,
            'frais' => (float) $tranche['frais'],
            'sender' => $sender['numero'],
            'receiver' => $receiver['numero'],
            'description' => (string) ($this->request->getPost('description') ?? ''),
            'instant' => $instant,
        ]);

        return redirect()->to('recu');
    }

    private function resoudreTypeId(string $type): int
    {
        $typeId = $this->typeMvtModel->getIdByLibelle($type);
        if ($typeId === null) {
            throw new RuntimeException("Le type d'opération {$type} n'existe pas.");
        }
        return $typeId;
    }

    private function resoudreTranche(int $typeId, float $montant): array
    {
        $tranche = $this->trancheModel->findByTypeAndMontant($typeId, $montant);
        if ($tranche === null) {
            throw new RuntimeException('Aucune tranche de frais ne correspond à ce montant.');
        }
        return $tranche;
    }

    private function resoudreParticipants(string $type): array
    {
        $provider = fn() => $this->userModel->find($this->getProviderNumero());

        $client = fn(string $champ) => $this->getClientByNumero(
            (string) $this->request->getPost($champ),
            $this->userModel
        );

        $sender = $type === 'depot' ? $provider() : $client('numero_sender');
        $receiver = $type === 'retrait' ? $provider() : $client('numero_receiver');

        if ($sender === null || $receiver === null) {
            throw new RuntimeException('Compte introuvable pour cette opération.');
        }

        if ($sender['numero'] === $receiver['numero']) {
            throw new RuntimeException('Le compte source et le compte destinataire doivent être différents.');
        }

        return [$sender, $receiver];
    }

    private function enregistrerMouvement(
        string $type,
        int $typeId,
        array $tranche,
        array $sender,
        array $receiver,
        float $montant,
        float $debit,
        string $instant
    ): int {
        $db = $this->mvtModel->db;
        $db->transStart();

        if ($debit > 0) {
            $this->incrementerSolde($db, $sender['numero'], -$debit);
        }

        if ($type !== 'retrait') {
            $this->incrementerSolde($db, $receiver['numero'], $montant);
        }

        $mvtId = $this->mvtModel->insert([
            'montant' => $montant,
            'frais' => (float) $tranche['frais'],
            'id_type' => $typeId,
            'num_sender' => $sender['numero'],
            'num_receiver' => $receiver['numero'],
            'description' => (string) ($this->request->getPost('description') ?? ''),
            'instant' => $instant,
        ], true);

        $this->mvtDetailsModel->insert([
            'id_mvt' => $mvtId,
            'id_tranche' => (int) $tranche['id'],
        ]);

        $db->transComplete();

        if (!$db->transStatus()) {
            throw new RuntimeException("L'opération n'a pas pu être enregistrée.");
        }

        return $mvtId;
    }

    private function getClientByNumero(string $numero, UserModel $userModel): ?array
    {
        $numero = trim($numero);

        if ($numero === '' || !preg_match('/^[0-9]{10}$/', $numero)) {
            return null;
        }

        if (!$this->prefixModel->isValidNumero($numero)) {
            return null;
        }

        $user = $userModel->findByNumero($numero);

        if ($user === null || (int) $user['is_provider'] === 1) {
            return null;
        }

        return $user;
    }

    private function incrementerSolde($db, string $numero, float $montant): void
    {
        $db->table('user')
            ->set('solde', 'solde + ' . $montant, false)
            ->where('numero', $numero)
            ->update();
    }

    /**
     * Gère l'affichage de l'interface et le traitement du transfert multiple (Montant divisé).
     */
    public function transfertMultiple()
    {
        if (strtolower($this->request->getMethod()) !== 'post') {
            return view('operations/transfert-multiple', [
                'activePage' => 'transfert-multiple',
            ]);
        }

        $db = $this->mvtModel->db;

        try {
            $senderNumero = (string) $this->request->getPost('numero_sender');
            $receivers = $this->request->getPost('numero_receiver'); // Tableau issu des checkboxes
            $montantGlobal = (float) $this->request->getPost('montant');
            $description = (string) ($this->request->getPost('description') ?? 'Transfert multiple');

            if (empty($receivers) || !is_array($receivers)) {
                throw new RuntimeException('Veuillez sélectionner au moins un destinataire valide.');
            }

            if ($montantGlobal <= 0) {
                throw new RuntimeException('Le montant global doit être supérieur à 0.');
            }

            $nombreDestinataires = count($receivers);

            // --- CORRECTION : Division du montant entre les destinataires ---
            $montantUnitaire = $montantGlobal / $nombreDestinataires;

            // Résolution des types et des frais basés sur le montant divisé
            $typeId = $this->resoudreTypeId('transfert');
            $tranche = $this->resoudreTranche($typeId, $montantUnitaire);
            $fraisUnitaire = (float) $tranche['frais'];

            // Validation de l'émetteur
            $sender = $this->getClientByNumero($senderNumero, $this->userModel);
            if ($sender === null) {
                throw new RuntimeException('Compte émetteur introuvable ou invalide.');
            }

            // Calcul du débit total (Montant global + cumul des frais individuels)
            $fraisTotal = $fraisUnitaire * $nombreDestinataires;
            $debitTotal = $montantGlobal + $fraisTotal;

            if ((float) $sender['solde'] < $debitTotal) {
                throw new RuntimeException("Solde insuffisant pour diviser {$montantGlobal} Ar entre {$nombreDestinataires} personnes (Besoin de: {$debitTotal} Ar avec frais).");
            }

            $instant = date('Y-m-d H:i:s');

            // Démarrage de la transaction SQL
            $db->transStart();

            // 1. Débiter l'émetteur du package complet (Global + Frais)
            $this->incrementerSolde($db, $sender['numero'], -$debitTotal);

            $lastMvtId = null;

            // 2. Boucle de distribution
            foreach ($receivers as $receiverNumero) {
                $receiver = $this->getClientByNumero($receiverNumero, $this->userModel);
                if ($receiver === null) {
                    throw new RuntimeException("Le compte destinataire {$receiverNumero} est introuvable.");
                }

                if ($sender['numero'] === $receiver['numero']) {
                    throw new RuntimeException('Vous ne pouvez pas inclure votre propre numéro dans les destinataires.');
                }

                // Créditer le montant divisé
                $this->incrementerSolde($db, $receiver['numero'], $montantUnitaire);

                // Insérer le mouvement individuel
                $mvtId = $this->mvtModel->insert([
                    'montant' => $montantUnitaire,
                    'frais' => $fraisUnitaire,
                    'id_type' => $typeId,
                    'num_sender' => $sender['numero'],
                    'num_receiver' => $receiver['numero'],
                    'description' => $description,
                    'instant' => $instant,
                ], true);

                // Détails de la tranche
                $this->mvtDetailsModel->insert([
                    'id_mvt' => $mvtId,
                    'id_tranche' => (int) $tranche['id'],
                ]);

                // 3. Commission inter-opérateur calculée sur le montant divisé
                $this->calculerEtEnregistrerCommission($db, $mvtId, $sender['numero'], $receiver['numero'], $montantUnitaire, $instant);

                $lastMvtId = $mvtId;
            }

            $db->transComplete();

            if (!$db->transStatus()) {
                throw new RuntimeException("L'opération de transfert multiple a échoué.");
            }

        } catch (RuntimeException $e) {
            return redirect()->back()->withInput()->with('error', $e->getMessage());
        }

        // Flashdata pour le reçu avec le récapitulatif
        session()->setFlashdata('receipt', [
            'id_mvt' => $lastMvtId,
            'type' => 'transfert_multiple',
            'montant' => $montantGlobal, // Affiche la somme totale distribuée
            'frais' => $fraisTotal,
            'sender' => $sender['numero'],
            'receiver' => implode(', ', $receivers),
            'description' => "Enveloppe divisée en {$nombreDestinataires} parts de " . number_format($montantUnitaire, 2, '.', ' ') . " Ar",
            'instant' => $instant,
        ]);

        return redirect()->to('recu');
    }

    private function calculerEtEnregistrerCommission($db, int $mvtId, string $senderNum, string $receiverNum, float $montant, string $instant): void
    {
        $prefixSender = substr($senderNum, 0, 3);
        $prefixReceiver = substr($receiverNum, 0, 3);

        // Si les préfixes (et donc les opérateurs) sont identiques, pas de commission inter-opérateur
        if ($prefixSender === $prefixReceiver) {
            return;
        }

        // Récupération de l'ID opérateur émetteur
        $opSender = $db->table('prefix')->select('operator_id')->where('value', $prefixSender)->get()->getRowArray();
        // Récupération de l'ID opérateur destinataire
        $opReceiver = $db->table('prefix')->select('operator_id')->where('value', $prefixReceiver)->get()->getRowArray();

        if ($opSender && $opReceiver) {
            $idOp1 = (int) $opSender['operator_id'];
            $idOp2 = (int) $opReceiver['operator_id'];

            // Recherche du taux de commission configuré
            $com = $db->table('comission')
                ->select('commission_rate')
                ->where('id_op1', $idOp1)
                ->where('id_op2', $idOp2)
                ->get()
                ->getRowArray();

            if ($com) {
                $taux = (float) $com['commission_rate'];
                $montantCommission = $montant * $taux;

                // Enregistrement du gain de commission
                $db->table('mvt_commission')->insert([
                    'id_mvt' => $mvtId,
                    'montant' => $montantCommission,
                    'instant' => $instant
                ]);
            }
        }
    }

}