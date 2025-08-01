<?php
require_once __DIR__ . '/../../controllers/master/ClientController.php';
require_once __DIR__ . '/../../services/master/ClientService.php';
require_once __DIR__ . '/../../repositories/master/ClientRepository.php';
require_once __DIR__ . '/../../models/master/Client.php';

$conn = $GLOBALS['conn'];

$repository = new ClientRepository($conn);
$service = new ClientService($repository);
$controller = new ClientController($service);

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$method = $_SERVER['REQUEST_METHOD'];

// GET /api/client/get/{id}
if (preg_match('#^/api/client/get/(\d+)$#', $uri, $matches) && $method === 'GET') {
    $id = (int) $matches[1];
    $controller->findById($id);
}

// PUT /api/client/update/{id}
if (preg_match('#^/api/client/update/(\d+)$#', $uri, $matches) && $method === 'PUT') {
    $id = (int) $matches[1];
    $controller->update($id);
}

// DELETE /api/client/delete/{id}
if (preg_match('#^/api/client/delete/(\d+)$#', $uri, $matches) && $method === 'DELETE') {
    $id = (int) $matches[1];
    $controller->delete($id);
}

// GET /api/client/get
if (preg_match('#^/api/customer/(\d+)/clients$#', $uri, $matches) && $method === 'GET') {
    $customerId = (int)$matches[1];
    $controller->find($customerId);
}


// POST /api/client/create
if ($uri === '/api/client/create' && $method === 'POST') {
    $controller->create();
}
