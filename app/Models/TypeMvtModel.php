<?php

namespace App\Models;

use CodeIgniter\Model;

class TypeMvtModel extends Model
{
    protected $table = 'type_mvt';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $allowedFields = ['libelle'];

    public function getIdByLibelle(string $libelle): ?int
    {
        $type = $this->where('libelle', strtolower($libelle))->first();
        return $type === null ? null : (int) $type['id'];
    }
}
