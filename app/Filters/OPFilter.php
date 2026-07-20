<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class OPFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        // If the session 'isLoggedIn' is missing or false, redirect to login page
        if (!session()->get('isOP')) {
            return redirect()->to('operations/transfert-multiple');
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Do nothing here
    }
}
