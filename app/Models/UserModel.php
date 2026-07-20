<?php
namespace App\Models;
use CodeIgniter\Model;
class UserModel extends Model
{
    protected $table = 'user';
    protected $primaryKey = 'numero';
    protected $allowedFields = ['numero', 'solde'];

    private $numero_auto;

    public function getNumeroAuto()
    {
        return $this->numero_auto;
    }

    public function exists($numero)
    {
        return $this->where('numero', $numero)->first() !== null;
    }

    public function createUser($numero)
    {

        $this->insert(['numero' => $numero, 'solde' => 0]);
        
        //Retourne faux si ca marche pas 
        return $this->exists($numero);
    }

}