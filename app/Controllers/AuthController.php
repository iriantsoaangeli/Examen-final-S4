<?php

namespace App\Controllers;

use App\Models\UserModel;

class AuthController extends BaseController
{
    public function login(): string
    {
        return view('/login/login');
    }

    public function authenticate($numero)
    {
        $model = new UserModel();
        if ($this->regex($numero)) {

            if ($model->exists($numero) || $model->createUser($numero)) {
                $user = $model->findByNumero($numero);
                session()->set('numero', $user['numero']);
                session()->set('isLoggedIn', true);
            }
            return redirect()->to('/operations/depot');
        } else {
            return redirect()->back()->with('error', 'Nummero invalide');
        }
    }

    public function regex($numero)
    {
        return true;
    }
}
