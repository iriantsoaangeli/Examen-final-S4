<?php

namespace App\Controllers\rapports;

use App\Controllers\BaseController;
use App\Models\CommissionModel;
use App\Models\PrefixModel;
use App\Models\UserModel;

class Commission extends BaseController
{
    private UserModel $userModel;
    private PrefixModel $prefixModel;
    private CommissionModel $commissionModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->prefixModel = new PrefixModel();
        $this->commissionModel = new CommissionModel();
    }

    public function index()
    {
        return view('rapports/commission', [
            'activePage' => 'rapports',
            'commissionsParOperateur' => $this->commissionModel->getCommissionsParOperateur(),
            'montantsAEnvoyer' => $this->commissionModel->getMontantsAEnvoyerParOperateur(),
        ]);
    }

    public function bareme()
    {
        return view('rapports/bareme', [
            'activePage' => 'rapports',
            'configurationCommissions' => $this->commissionModel->getConfigurationCommissions(),
        ]);
    }

    public function operateurs()
    {
        return view('rapports/operateurs', [
            'activePage' => 'rapports',
            'operateursProviders' => $this->prefixModel->operatorsWithProviders(),
        ]);
    }

    public function comptes()
    {
        return $this->response->setJSON($this->userModel->getClients());
    }
}
