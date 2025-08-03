<?php
require_once __DIR__ . '/../../controllers/master/FormationController.php';
require_once __DIR__ . '/../../services/master/FormationService.php';
require_once __DIR__ . '/../../repositories/master/FormationRepository.php';
require_once __DIR__ . '/../../models/master/Formation.php';

$conn = $GLOBALS['conn'];

$repository = new FormationRepository($conn);
$service = new FormationService($repository);
$controller = new FormationController($service);

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$method = $_SERVER['REQUEST_METHOD'];

// GET /api/formation/get/{id}
if (preg_match('#^/api/formation/get/(\d+)$#', $uri, $matches) && $method === 'GET') {
    $id = (int) $matches[1];
    $controller->findById($id);
}

// PUT /api/formation/update/{id}
if (preg_match('#^/api/formation/update/(\d+)$#', $uri, $matches) && $method === 'PUT') {
    $id = (int) $matches[1];
    $controller->update($id);
}

// DELETE /api/formation/delete/{id}
if (preg_match('#^/api/formation/delete/(\d+)$#', $uri, $matches) && $method === 'DELETE') {
    $id = (int) $matches[1];
    $controller->delete($id);
}

// GET /api/formation/get
if (preg_match('#^/api/customer/(\d+)/formations$#', $uri, $matches) && $method === 'GET') {
    $customerId = (int)$matches[1];
    $controller->find($customerId);
}


// POST /api/formation/create
if ($uri === '/api/formation/create' && $method === 'POST') {
    $controller->create();
}
