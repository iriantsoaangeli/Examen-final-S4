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
        $provider = fn () => $this->userModel->find($this->getProviderNumero());
        
        $client = fn (string $champ) => $this->getClientByNumero(
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

}