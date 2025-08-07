<?php
require_once __DIR__ . '/../controllers/LicenseController.php';
require_once __DIR__ . '/../services/LicenseService.php';
require_once __DIR__ . '/../repositories/LicenseRepository.php';
require_once __DIR__ . '/../models/License.php';

$conn = $GLOBALS['conn'];

$repository = new LicenseRepository($conn);
$service = new LicenseService($repository);
$controller = new LicenseController($service);

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$method = $_SERVER['REQUEST_METHOD'];

// GET /api/license/get/{id}
if (preg_match('#^/api/license/get/(\d+)$#', $uri, $matches) && $method === 'GET') {
    $id = (int) $matches[1];
    $controller->findById($id);
}

// PUT /api/license/update/{id}
if (preg_match('#^/api/license/update/(\d+)$#', $uri, $matches) && $method === 'PUT') {
    $id = (int) $matches[1];
    $controller->update($id);
}

// DELETE /api/license/delete/{id}
if (preg_match('#^/api/license/delete/(\d+)$#', $uri, $matches) && $method === 'DELETE') {
    $id = (int) $matches[1];
    $controller->delete($id);
}

// GET /api/license/get
if ($uri === '/api/license/get' && $method === 'GET') {
    $controller->find();
}

// POST /api/license/create
if ($uri === '/api/license/register' && $method === 'POST') {
    $controller->create();
}

// GET /api/license/validate/{code}
if ($uri === '/api/license/validate' && $method === 'POST') {
    $controller->validate();
}