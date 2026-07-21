<?php
namespace App\Models;
use CodeIgniter\Model;
use DateTime;
class EpargneModel extends Model
{
    protected $table = 'mvt_epargne';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $allowedFields = ['instant', 'montant','numero'];
  
    public function getSolde($numero){
    return $this->select("SUM(montant)")->where('numero',$numero);
    }  

    public function transfert($numero,$montant){
        $this->insert(['numero'=>$numero,'montant'=>$montant ,'instant'=> 'DATE()']) ;
    }
}
