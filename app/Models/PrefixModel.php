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
}
