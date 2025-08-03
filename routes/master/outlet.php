<?php
require_once __DIR__ . '/../../controllers/master/OutletController.php';
require_once __DIR__ . '/../../services/master/OutletService.php';
require_once __DIR__ . '/../../repositories/master/OutletRepository.php';
require_once __DIR__ . '/../../models/master/Outlet.php';

$conn = $GLOBALS['conn'];

$repository = new OutletRepository($conn);
$service = new OutletService($repository);
$controller = new OutletController($service);

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$method = $_SERVER['REQUEST_METHOD'];

// GET /api/outlet/get/{id}
if (preg_match('#^/api/outlet/get/(\d+)$#', $uri, $matches) && $method === 'GET') {
    $id = (int) $matches[1];
    $controller->findById($id);
}

// PUT /api/outlet/update/{id}
if (preg_match('#^/api/outlet/update/(\d+)$#', $uri, $matches) && $method === 'PUT') {
    $id = (int) $matches[1];
    $controller->update($id);
}

// DELETE /api/outlet/delete/{id}
if (preg_match('#^/api/outlet/delete/(\d+)$#', $uri, $matches) && $method === 'DELETE') {
    $id = (int) $matches[1];
    $controller->delete($id);
}

// GET /api/outlet/get
if (preg_match('#^/api/customer/(\d+)/outlets$#', $uri, $matches) && $method === 'GET') {
    $customerId = (int)$matches[1];
    $controller->find($customerId);
}


// POST /api/outlet/create
if ($uri === '/api/outlet/create' && $method === 'POST') {
    $controller->create();
}
