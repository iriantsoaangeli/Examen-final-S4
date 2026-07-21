<?php

namespace App\Models;

use CodeIgniter\Model;

class TrancheModel extends Model
{
    protected $table = 'tranche';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $allowedFields = ['inf', 'sup', 'frais','promotion', 'id_type'];

    public function findByTypeAndMontant(int $typeId, float $montant): ?array
    {
        return $this->where('id_type', $typeId)
            ->where('inf <=', $montant)
            ->groupStart()
            ->where('sup >=', $montant)
            ->orWhere('sup', null)
            ->groupEnd()
            ->orderBy('inf', 'ASC')
            ->first();
    }

    public function getPromotion() {
        return $this->find('promotion');
    }
}
