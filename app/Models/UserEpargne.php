<?php
namespace App\Models;
use CodeIgniter\Model;
class UserEpargne extends Model
{
    protected $table = 'client_epargne';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $allowedFields = ['numero', 'pourcentage'];
    
    public function getPercentage($numero){
        return $this->where('numero',$numero)->first();
    }

    public function setPourcentage($numero,$pourcentage){
         
    }
}
