<?php
namespace App\Models;
use CodeIgniter\Model;

class MvtModel extends Model
{
    protected $table = 'mouvement';
    protected $primaryKey = 'id';
    protected $allowedFields = ['id_type', 'num_sender', 'num_receiver', 'montant', 'date_mvt'];

    public function getMouvementsByUser($numero)
    {
        return $this->where('num_sender', $numero)
                    ->orWhere('num_receiver', $numero)
                    ->findAll();
    }
}