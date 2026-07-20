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
}