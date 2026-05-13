<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// ===== ROUTES PUBLIQUES =====
$routes->get('/', 'HomeController::index');
$routes->get('/login', 'AuthController::loginForm');
$routes->post('/login', 'AuthController::login');
$routes->get('/logout', 'AuthController::logout');

// ===== ROUTES PROTEGEES - EMPLOYE =====
$routes->group('employe', ['filter' => 'auth'], static function ($routes) {
    $routes->get('dashboard', 'EmployeController::dashboard');
    $routes->get('demande', 'EmployeController::formulaire');
    $routes->post('demande', 'EmployeController::submitDemande');
    $routes->get('mes-demandes', 'EmployeController::listDemandes');
    $routes->post('demande/(:num)/cancel', 'EmployeController::cancelDemande/$1');
});

// ===== ROUTES PROTEGEES - RH =====
$routes->group('rh', ['filter' => 'auth'], static function ($routes) {
    $routes->get('dashboard', 'RHController::dashboard');
    $routes->get('demandes', 'RHController::index');
    $routes->get('demandes/(:num)', 'RHController::detail/$1');
    $routes->post('demandes/(:num)/approuver', 'RHController::approuver/$1');
    $routes->post('demandes/(:num)/refuser', 'RHController::refuser/$1');
    $routes->get('historique', 'RHController::historique');
});

// ===== ROUTES PROTEGEES - ADMIN =====
$routes->group('admin', ['filter' => 'auth'], static function ($routes) {
    $routes->get('dashboard', 'AdminController::dashboard');
    $routes->get('employes', 'AdminController::listEmployes');
    $routes->get('employes/create', 'AdminController::formEmploye');
    $routes->post('employes', 'AdminController::createEmploye');
    $routes->get('employes/(:num)/edit', 'AdminController::formEmploye/$1');
    $routes->post('employes/(:num)', 'AdminController::updateEmploye/$1');
    $routes->post('employes/(:num)/disable', 'AdminController::disableEmploye/$1');
    
    $routes->get('departements', 'AdminController::listDepartements');
    $routes->get('departements/create', 'AdminController::formDepartement');
    $routes->post('departements', 'AdminController::createDepartement');
    $routes->get('departements/(:num)/edit', 'AdminController::formDepartement/$1');
    $routes->post('departements/(:num)', 'AdminController::updateDepartement/$1');
    
    $routes->get('types', 'AdminController::listTypes');
    $routes->get('types/create', 'AdminController::formType');
    $routes->post('types', 'AdminController::createType');
    $routes->get('types/(:num)/edit', 'AdminController::formType/$1');
    $routes->post('types/(:num)', 'AdminController::updateType/$1');
    
    $routes->get('soldes', 'AdminController::listSoldes');
    $routes->post('soldes/init', 'AdminController::initSoldes');
});
