<?php

namespace App\Controllers\operations;

use App\Controllers\BaseController;
use App\Models\CommissionModel;
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
    private CommissionModel $commissionModel;

    private ?string $providerNumero = null;

    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->mvtModel = new MvtModel();
        $this->prefixModel = new PrefixModel();
        $this->trancheModel = new TrancheModel();
        $this->typeMvtModel = new TypeMvtModel();
        $this->mvtDetailsModel = new MvtDetailsModel();
        $this->commissionModel = new CommissionModel();
    }

    private function getProviderNumero(): string
    {
        if ($this->providerNumero === null) {
            $this->providerNumero = $this->userModel->getProviderNumero();
        }

        return $this->providerNumero;
    }

    public function depot()
    {
        if (!$this->isPostRequest()) {
            return $this->renderOperationPage('operations/depot', 'depot');
        }

        return $this->executerOperation('depot');
    }

    public function retrait()
    {
        if (!$this->isPostRequest()) {
            return $this->renderOperationPage('operations/retrait', 'retrait');
        }

        return $this->executerOperation('retrait');
    }

    public function transfert()
    {
        if (!$this->isPostRequest()) {
            return $this->renderOperationPage('operations/transfert', 'transfert');
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

    private function isPostRequest(): bool
    {
        return strtolower($this->request->getMethod()) === 'post';
    }

    private function renderOperationPage(string $view, string $activePage)
    {
        return view($view, [
            'activePage' => $activePage,
        ]);
    }

    private function redirectBackWithError(string $message)
    {
        return redirect()->back()->withInput()->with('error', $message);
    }

    private function executerOperation(string $type)
    {
        try {
            $operation = $this->preparerOperation($type);
            $mvtId = $this->enregistrerMouvement($operation);
        } catch (RuntimeException $e) {
            return $this->redirectBackWithError($e->getMessage());
        }

        session()->setFlashdata('receipt', $this->buildReceiptData($operation, $mvtId));

        return redirect()->to('recu');
    }

    private function preparerOperation(string $type): array
    {
        $montant = $this->getPostAmount('montant');
        $typeId = $this->resoudreTypeId($type);
        $tranche = $this->resoudreTranche($typeId, $montant);
        [$sender, $receiver] = $this->resoudreParticipants($type);

        $debit = $this->calculerDebit($type, $montant, $tranche);
        $this->verifierSolde($sender, $debit);

        return [
            'type' => $type,
            'typeId' => $typeId,
            'tranche' => $tranche,
            'sender' => $sender,
            'receiver' => $receiver,
            'montant' => $montant,
            'debit' => $debit,
            'commission' => $type === 'transfert'
                ? $this->calculerCommissionInterOperateur($sender['numero'], $receiver['numero'], $montant)
                : 0.0,
            'instant' => date('Y-m-d H:i:s'),
            'description' => $this->getPostString('description'),
        ];
    }

    private function getPostString(string $key, string $default = ''): string
    {
        $value = $this->request->getPost($key);

        if ($value === null || $value === '') {
            return $default;
        }

        return (string) $value;
    }

    private function getPostAmount(string $key): float
    {
        $montant = (float) $this->request->getPost($key);

        if ($montant <= 0) {
            throw new RuntimeException('Le montant doit être supérieur à 0.');
        }

        return $montant;
    }

    private function calculerDebit(string $type, float $montant, array $tranche): float
    {
        if ($type === 'depot') {
            return 0.0;
        }

        return $montant + (float) $tranche['frais'];
    }

    private function verifierSolde(array $sender, float $debit): void
    {
        if ($debit > 0 && (float) $sender['solde'] < $debit) {
            throw new RuntimeException('Solde insuffisant.');
        }
    }

    private function buildReceiptData(array $operation, int $mvtId): array
    {
        return [
            'id_mvt' => $mvtId,
            'type' => $operation['type'],
            'montant' => $operation['montant'],
            'frais' => (float) $operation['tranche']['frais'],
            'sender' => $operation['sender']['numero'],
            'receiver' => $operation['receiver']['numero'],
            'description' => $operation['description'],
            'commission' => $operation['commission'],
            'instant' => $operation['instant'],
        ];
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
        $provider = $this->getProviderByNumero();
        $sender = $type === 'depot'
            ? $provider
            : $this->findClientByNumero($this->getPostString('numero_sender'));

        $receiver = $type === 'retrait'
            ? $provider
            : $this->findClientByNumero($this->getPostString('numero_receiver'));

        if ($sender === null || $receiver === null) {
            throw new RuntimeException('Compte introuvable pour cette opération.');
        }

        if ($sender['numero'] === $receiver['numero']) {
            throw new RuntimeException('Le compte source et le compte destinataire doivent être différents.');
        }

        return [$sender, $receiver];
    }

    private function getProviderByNumero(): ?array
    {
        return $this->userModel->find($this->getProviderNumero());
    }

    private function enregistrerMouvement(array $operation): int
    {
        $db = $this->mvtModel->db;
        $db->transStart();

        try {
            if ($operation['debit'] > 0) {
                $this->incrementerSolde($db, $operation['sender']['numero'], -$operation['debit']);
            }

            if ($operation['type'] !== 'retrait') {
                $this->incrementerSolde($db, $operation['receiver']['numero'], $operation['montant']);
            }

            $mvtId = $this->mvtModel->insert([
                'montant' => $operation['montant'],
                'frais' => (float) $operation['tranche']['frais'],
                'id_type' => $operation['typeId'],
                'num_sender' => $operation['sender']['numero'],
                'num_receiver' => $operation['receiver']['numero'],
                'description' => $operation['description'],
                'instant' => $operation['instant'],
            ], true);

            if (!$mvtId) {
                throw new RuntimeException("L'opération n'a pas pu être enregistrée.");
            }

            $this->mvtDetailsModel->insert([
                'id_mvt' => $mvtId,
                'id_tranche' => (int) $operation['tranche']['id'],
            ]);

            $db->transComplete();

            if (!$db->transStatus()) {
                throw new RuntimeException("L'opération n'a pas pu être enregistrée.");
            }

            return (int) $mvtId;
        } catch (RuntimeException $e) {
            $db->transRollback();
            throw $e;
        }
    }

    private function findClientByNumero(string $numero): ?array
    {
        $numero = trim($numero);

        if ($numero === '' || !preg_match('/^[0-9]{10}$/', $numero)) {
            return null;
        }

        if (!$this->prefixModel->isValidNumero($numero)) {
            return null;
        }

        $user = $this->userModel->findByNumero($numero);

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
        if (!$this->isPostRequest()) {
            return $this->renderOperationPage('operations/transfert-multiple', 'transfert-multiple');
        }

        try {
            $transfert = $this->preparerTransfertMultiple();
            $lastMvtId = $this->executerTransfertMultiple($transfert);
        } catch (RuntimeException $e) {
            return $this->redirectBackWithError($e->getMessage());
        }

        session()->setFlashdata('receipt', $this->buildTransfertMultipleReceipt($transfert, $lastMvtId));

        return redirect()->to('recu');
    }

    private function preparerTransfertMultiple(): array
    {
        $receivers = $this->request->getPost('numero_receiver');
        if (empty($receivers) || !is_array($receivers)) {
            throw new RuntimeException('Veuillez sélectionner au moins un destinataire valide.');
        }
        $promotion = $this->trancheModel . getPromotion();
        $montantGlobal = $this->getPostAmount('montant');
        $senderNumero = $this->getPostString('numero_sender');
        $senderPrefix = $this->prefixModel->findPrefix($senderNumero);
        $description = $this->getPostString('description', 'Transfert multiple');
        $nombreDestinataires = count($receivers);
        $montantUnitaire = $montantGlobal / $nombreDestinataires;
        $typeId = $this->resoudreTypeId('transfert');
        $tranche = $this->resoudreTranche($typeId, $montantUnitaire);
        $receiversPrefix = [];
        $fraisUnitaire = (float) $tranche['frais'];
        foreach ($receivers as $rec) {
            $receiversPrefix = $this->prefixModel->findPrefix($rec);
        }
        foreach ($receiversPrefix as $pre) {
            if ($senderPrefix === $pre) {
                $fraisUnitaire = (float) $tranche['frais'] * (int) $promotion / 100;
            }
        }
        $sender = $this->findClientByNumero($senderNumero);

        if ($sender === null) {
            throw new RuntimeException('Compte émetteur introuvable ou invalide.');
        }

        $fraisTotal = $fraisUnitaire * $nombreDestinataires;
        $debitTotal = $montantGlobal + $fraisTotal;

        if ((float) $sender['solde'] < $debitTotal) {
            throw new RuntimeException("Solde insuffisant pour diviser {$montantGlobal} Ar entre {$nombreDestinataires} personnes (Besoin de: {$debitTotal} Ar avec frais).");
        }

        return [
            'sender' => $sender,
            'receivers' => $receivers,
            'montantGlobal' => $montantGlobal,
            'montantUnitaire' => $montantUnitaire,
            'fraisUnitaire' => $fraisUnitaire,
            'fraisTotal' => $fraisTotal,
            'debitTotal' => $debitTotal,
            'typeId' => $typeId,
            'tranche' => $tranche,
            'description' => $description,
            'instant' => date('Y-m-d H:i:s'),
        ];
    }

    private function executerTransfertMultiple(array $transfert): int
    {
        $db = $this->mvtModel->db;
        $db->transStart();

        try {
            $this->incrementerSolde($db, $transfert['sender']['numero'], -$transfert['debitTotal']);

            $lastMvtId = 0;

            foreach ($transfert['receivers'] as $receiverNumero) {
                $lastMvtId = $this->traiterDestinataireTransfertMultiple($db, $transfert, (string) $receiverNumero);
            }

            $db->transComplete();

            if (!$db->transStatus()) {
                throw new RuntimeException("L'opération de transfert multiple a échoué.");
            }

            return $lastMvtId;
        } catch (RuntimeException $e) {
            $db->transRollback();
            throw $e;
        }
    }

    private function traiterDestinataireTransfertMultiple($db, array $transfert, string $receiverNumero): int
    {
        $receiver = $this->findClientByNumero($receiverNumero);
        if ($receiver === null) {
            throw new RuntimeException("Le compte destinataire {$receiverNumero} est introuvable.");
        }

        if ($transfert['sender']['numero'] === $receiver['numero']) {
            throw new RuntimeException('Vous ne pouvez pas inclure votre propre numéro dans les destinataires.');
        }

        $this->incrementerSolde($db, $receiver['numero'], $transfert['montantUnitaire']);

        $mvtId = $this->mvtModel->insert([
            'montant' => $transfert['montantUnitaire'],
            'frais' => $transfert['fraisUnitaire'],
            'id_type' => $transfert['typeId'],
            'num_sender' => $transfert['sender']['numero'],
            'num_receiver' => $receiver['numero'],
            'description' => $transfert['description'],
            'instant' => $transfert['instant'],
        ], true);

        if (!$mvtId) {
            throw new RuntimeException("L'opération de transfert multiple a échoué.");
        }

        $this->mvtDetailsModel->insert([
            'id_mvt' => $mvtId,
            'id_tranche' => (int) $transfert['tranche']['id'],
        ]);

        $this->enregistrerCommissionInterOperateur(
            $db,
            $mvtId,
            $transfert['sender']['numero'],
            $receiver['numero'],
            $transfert['montantUnitaire'],
            $transfert['instant']
        );

        return $mvtId;
    }

    private function buildTransfertMultipleReceipt(array $transfert, int $lastMvtId): array
    {
        return [
            'id_mvt' => $lastMvtId,
            'type' => 'transfert_multiple',
            'montant' => $transfert['montantGlobal'],
            'frais' => $transfert['fraisTotal'],
            'sender' => $transfert['sender']['numero'],
            'receiver' => implode(', ', $transfert['receivers']),
            'description' => 'Enveloppe divisée en ' . count($transfert['receivers']) . ' parts de ' . number_format($transfert['montantUnitaire'], 2, '.', ' ') . ' Ar',
            'instant' => $transfert['instant'],
        ];
    }

    private function enregistrerCommissionInterOperateur($db, int $mvtId, string $senderNum, string $receiverNum, float $montant, string $instant): void
    {
        $commission = $this->calculerCommissionInterOperateur($senderNum, $receiverNum, $montant);
        if ($commission <= 0) {
            return;
        }

        $this->commissionModel->enregistrerCommission($mvtId, $commission, $instant);
    }

    private function calculerCommissionInterOperateur(string $senderNum, string $receiverNum, float $montant): float
    {
        $prefixSender = substr($senderNum, 0, 3);
        $prefixReceiver = substr($receiverNum, 0, 3);

        if ($prefixSender === $prefixReceiver) {
            return 0.0;
        }

        $idOp1 = $this->getOperatorIdByPrefix($this->mvtModel->db, $prefixSender);
        $idOp2 = $this->getOperatorIdByPrefix($this->mvtModel->db, $prefixReceiver);

        if ($idOp1 === null || $idOp2 === null) {
            return 0.0;
        }

        $taux = $this->getCommissionRate($idOp1, $idOp2);
        if ($taux === null) {
            return 0.0;
        }

        return $montant * $taux;
    }

    private function getOperatorIdByPrefix($db, string $prefix): ?int
    {
        $row = $db->table('prefix')->select('operator_id')->where('value', $prefix)->get()->getRowArray();

        if (!$row) {
            return null;
        }

        return (int) $row['operator_id'];
    }

    private function getCommissionRate(int $idOp1, int $idOp2): ?float
    {
        return $this->commissionModel->findRate($idOp1, $idOp2);
    }

}