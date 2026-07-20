<?php

namespace App\Controllers;

use App\Models\PrefixModel;
use App\Models\UserModel;

class AuthController extends BaseController
{
    public function login(): string
    {
        return view('/login/login');
    }

    public function authenticate()
    {
        $numero = trim((string) $this->request->getPost('numero'));

        if (! $this->regex($numero)) {
            return redirect()->back()->withInput()->with('error', 'Numéro invalide. Il doit contenir 10 chiffres et commencer par 0.');
        }

        $model = new UserModel();

        if ($model->exists($numero) || $model->createUser($numero)) {
            $user = $model->findByNumero($numero);
            session()->set('numero', $user['numero']);
            session()->set('isLoggedIn', true);

            return redirect()->to('/dashboard');
        }

        return redirect()->back()->withInput()->with('error', "La connexion a échoué. Veuillez réessayer.");
    }

    public function logout()
    {
        session()->destroy();

        return redirect()->to('/login');
    }

    /**
     * Valide le numéro : 10 chiffres commençant par 0, et préfixe
     * (3 premiers chiffres) rattaché à un opérateur connu en base.
     */
    public function regex(string $numero): bool
    {
        if (! preg_match('/^0[0-9]{9}$/', $numero)) {
            return false;
        }

        return (new PrefixModel())->isValidNumero($numero);
    }
}
