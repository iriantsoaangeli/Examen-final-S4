<?php

namespace App\Controllers;

use App\Models\PrefixModel;

class APIController extends BaseController
{
    /**
     * GET /api/prefix
     * Retourne la liste des préfixes valides et leur opérateur, telle
     * qu'elle est stockée en base (table prefix + operator).
     * Consommé en AJAX par public/script/regex.js (getPrefix / checkPrefix).
     */
    public function getPrefix()
    {
        $prefixes = (new PrefixModel())->allWithOperator();

        return $this->response->setJSON($prefixes);
    }
}
