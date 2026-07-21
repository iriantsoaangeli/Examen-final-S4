<?php
namespace App\Models;
use CodeIgniter\Model;
class UserModel extends Model
{
    protected $table = 'user';
    protected $primaryKey = 'numero';
    protected $returnType = 'array';
    protected $allowedFields = ['numero', 'nom', 'solde', 'is_provider'];

    public function getProviderNumero(): array
    {
        return $this->where('is_provider', 1)->findAll();
    }
    public function findByNumero(string $numero): ?array
    {
        return $this->where('numero', $numero)->first();
    }

    public function getClients(): array
    {
        return $this->where('is_provider', 0)
            ->orderBy('numero', 'ASC')
            ->findAll();
    }

    public function exists($numero)
    {
        return $this->where('numero', $numero)->first() !== null;
    }

    public function createUser($numero)
    {
        $this->insert(['numero' => $numero, 'solde' => 0]);
        return $this->exists($numero);
    }

}
