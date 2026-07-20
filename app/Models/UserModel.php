<?php
namespace App\Models;
use CodeIgniter\Model;
class UserModel extends Model
{
    protected $table = 'user';
    protected $primaryKey = 'numero';
    protected $returnType = 'array';
    protected $allowedFields = ['numero', 'nom', 'solde', 'is_provider'];

    private $num_provider;

    public function getProviderNumero(): string
    {
        if ($this->num_provider === null) {
            $provider = $this->where('is_provider', 1)->first();
            $this->num_provider = $provider['numero'] ?? '';
        }
        return $this->num_provider;
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
