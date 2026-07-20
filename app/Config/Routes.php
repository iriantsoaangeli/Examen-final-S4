<?php

use CodeIgniter\Router\RouteCollection;
use App\Controllers\AuthController;

/**
 * @var RouteCollection $routes
 */

$routes->get('login', 'AuthController::login');
$routes->post('auth', 'AuthController::authenticate');
$routes->get('logout', 'AuthController::logout');

$routes->get('api/prefix', 'APIController::getPrefix');

$routes->get('operations/depot', 'operations\Operation::depot');
$routes->post('operations/depot', 'operations\Operation::depot');
$routes->get('operations/retrait', 'operations\Operation::retrait');
$routes->post('operations/retrait', 'operations\Operation::retrait');
$routes->get('operations/transfert', 'operations\Operation::transfert');
$routes->post('operations/transfert', 'operations\Operation::transfert');
$routes->get('operations/historique/(:segment)', 'operations\Operation::historique/$1');
$routes->get('operations/situation/gains', 'operations\Operation::gains');
$routes->get('operations/situation/comptes', 'operations\Operation::comptes');
