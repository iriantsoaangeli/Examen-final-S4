<?php

use CodeIgniter\Router\RouteCollection;
use App\Controllers\AuthController;

/**
 * @var RouteCollection $routes
 */

$routes->get('login', 'AuthController::login');
$routes->post('auth', 'AuthController::authenticate');
$routes->get('logout', 'AuthController::logout');
$routes->get('/', 'AuthController::login');

$routes->get('dashboard', 'Home::dashboard');
$routes->get('dashboard.html', 'Home::dashboard');
$routes->get('transactions', 'Home::transactions');
$routes->get('transactions.html', 'Home::transactions');
$routes->get('recu', 'operations\Operation::recu');
$routes->get('receipt', 'operations\Operation::recu');
$routes->get('receipt.html', 'operations\Operation::recu');

$routes->get('api/prefix', 'APIController::getPrefix');

$routes->get('operations/depot', 'operations\Operation::depot');
$routes->post('operations/depot', 'operations\Operation::depot');
$routes->get('operations/retrait', 'operations\Operation::retrait');
$routes->post('operations/retrait', 'operations\Operation::retrait');

$routes->group('operations', ['filter' => 'opfilter'], function ($routes) {
    $routes->get('transfert', 'operations\Operation::transfert');
    $routes->post('transfert', 'operations\Operation::transfert');
    $routes->get('historique/(:segment)', 'operations\Operation::historique/$1');
    $routes->get('situation/gains', 'operations\Operation::gains');
    $routes->get('situation/comptes', 'operations\Operation::comptes');
});

$routes->get('operations/transfert-multiple', 'operations\Operation::transfertMultiple');
$routes->post('operations/transfert-multiple', 'operations\Operation::transfertMultiple');

$routes->get('rapports', 'rapports\Commission::index');
$routes->get('rapports/comptes', 'rapports\Commission::comptes');
