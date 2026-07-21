<?php

namespace App\Controllers;


class EpargneController extends BaseController
{
    /**
     * GET /api/prefix
     * Retourne la liste des préfixes valides et leur opérateur, telle
     * qu'elle est stockée en base (table prefix + operator).
     * Consommé en AJAX par public/script/regex.js (getPrefix / checkPrefix).
     */

    
    public function getForm(){
        return view('home/setEpargne') ;
    }

    public function setPourcentage(){

    }
}
