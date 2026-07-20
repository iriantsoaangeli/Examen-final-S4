<?php

namespace App\Models;

use CodeIgniter\Model;

class MvtDetailsModel extends Model
{
    protected $table = 'mvt_details';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $allowedFields = ['id_mvt', 'id_tranche'];
}
