<?php
require_once __DIR__ . '/../controllers/CustomerController.php';
require_once __DIR__ . '/../services/CustomerService.php';
require_once __DIR__ . '/../repositories/CustomerRepository.php';
require_once __DIR__ . '/../models/Customer.php';

$conn = $GLOBALS['conn'];

$repository = new CustomerRepository($conn);
$service = new CustomerService($repository);
$controller = new CustomerController($service);

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$method = $_SERVER['REQUEST_METHOD'];

// GET /api/customer/get/{id}
if (preg_match('#^/api/customer/get/(\d+)$#', $uri, $matches) && $method === 'GET') {
    $id = (int) $matches[1];
    $controller->findById($id);
}

// PUT /api/customer/update/{id}
if (preg_match('#^/api/customer/update/(\d+)$#', $uri, $matches) && $method === 'PUT') {
    $id = (int) $matches[1];
    $controller->update($id);
}

// DELETE /api/customer/delete/{id}
if (preg_match('#^/api/customer/delete/(\d+)$#', $uri, $matches) && $method === 'DELETE') {
    $id = (int) $matches[1];
    $controller->delete($id);
}

// GET /api/customer/get
if ($uri === '/api/customer/get' && $method === 'GET') {
    $controller->find();
}

// POST /api/customer/create
if ($uri === '/api/customer/create' && $method === 'POST') {
    $controller->create();
}
