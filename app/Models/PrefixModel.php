<?php

namespace App\Models;

use CodeIgniter\Model;

class PrefixModel extends Model
{
    protected $table = 'prefix';
    protected $primaryKey = 'value';
    protected $returnType = 'array';
    protected $allowedFields = ['value', 'operator_id'];

    public function isValidNumero(string $numero): bool
    {
        return $this->where('value', substr($numero, 0, 3))->first() !== null;
    }

    /**
     * Retourne tous les préfixes avec le nom de l'opérateur associé.
     * Utilisé par APIController::getPrefix pour l'AJAX du login.
     */
    public function allWithOperator(): array
    {
        return $this->select('prefix.value, prefix.operator_id, operator.name AS operator')
            ->join('operator', 'operator.id = prefix.operator_id')
            ->orderBy('prefix.value', 'ASC')
            ->findAll();
    }
}
