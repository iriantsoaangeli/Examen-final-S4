<?php

namespace App\Models;

use CodeIgniter\Model;

class PrefixModel extends Model
{
    protected $table = 'prefix';
    protected $primaryKey = 'value';
    protected $returnType = 'array';
    protected $allowedFields = ['value', 'operator_id'];

    public function findPrefix(String $numero) {
        if(strlen($numero) < 3) {
            return null;
        }
        return $this->where('value', substr($numero, 0, 3))->first();
    }
    public function isValidNumero(string $numero): bool
    {
        return $this->findPrefix($numero) !== null;
    }

    public function operatorIdForNumero(string $numero): ?int
    {
        $prefix = $this->findPrefix($numero);
        return $prefix ? (int) $prefix['operator_id'] : null;
    }

    public function allWithOperator(): array
    {
        return $this->select(
            'prefix.value,
             prefix.operator_id,
             operator.name AS operator,
             CASE
                WHEN provider.numero IS NULL THEN 0
                ELSE 1
             END AS has_provider',
            false
        )
            ->join('operator', 'operator.id = prefix.operator_id')
            ->join('user AS provider', 'provider.is_provider = 1 AND prefix.value = substr(provider.numero, 1, 3)', 'left')
            ->orderBy('prefix.value', 'ASC')
            ->findAll();
    }

    public function operatorsWithProviders(): array
    {
        return $this->select(
            'operator.id AS operator_id,
             operator.name AS operator_name,
             MAX(CASE
                WHEN user.is_provider = 1 THEN user.numero
                ELSE NULL
             END) AS provider_numero,
             MAX(CASE
                WHEN user.is_provider = 1 THEN user.nom
                ELSE NULL
             END) AS provider_name,
             CASE
                WHEN MAX(CASE WHEN user.is_provider = 1 THEN 1 ELSE 0 END) = 1 THEN "avec_fournisseur"
                ELSE "sans_fournisseur"
             END AS provider_status',
            false
        )
            ->join('operator', 'operator.id = prefix.operator_id')
            ->join('user', 'prefix.value = substr(user.numero, 1, 3)', 'left')
            ->groupBy('operator.id')
            ->orderBy('operator.name', 'ASC')
            ->findAll();
    }
}
