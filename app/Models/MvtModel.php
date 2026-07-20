<?php
namespace App\Models;
use CodeIgniter\Model;

class MvtModel extends Model
{
    protected $table = 'mvt';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $allowedFields = [
        'montant',
        'frais',
        'id_type',
        'num_sender',
        'num_receiver',
        'description',
        'instant',
    ];

    public function getMouvementsByUser(string $numero): array
    {
        return $this->where('num_sender', $numero)
            ->orWhere('num_receiver', $numero)
            ->orderBy('instant', 'DESC')
            ->findAll();
    }

    public function getGainFrais(): array
    {
        return $this->select('type_mvt.libelle, SUM(mvt.frais) AS total_frais, COUNT(mvt.id) AS nombre_operations')
            ->join('type_mvt', 'type_mvt.id = mvt.id_type')
            ->whereIn('type_mvt.libelle', ['retrait', 'transfert'])
            ->groupBy('type_mvt.id')
            ->orderBy('type_mvt.libelle', 'ASC')
            ->findAll();
    }
}
