<?php
require_once __DIR__ . '/../../controllers/order/CampaignController.php';
require_once __DIR__ . '/../../services/order/CampaignService.php';
require_once __DIR__ . '/../../repositories/order/CampaignRepository.php';
require_once __DIR__ . '/../../models/order/Campaign.php';

$conn = $GLOBALS['conn'];

$repository = new CampaignRepository($conn);
$service = new CampaignService($repository);
$controller = new CampaignController($service);

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$method = $_SERVER['REQUEST_METHOD'];

// GET /api/campaign/get/{id}
if (preg_match('#^/api/campaign/get/(\d+)$#', $uri, $matches) && $method === 'GET') {
    $id = (int) $matches[1];
    $controller->findById($id);
}

// PUT /api/campaign/update/{id}
if (preg_match('#^/api/campaign/update/(\d+)$#', $uri, $matches) && $method === 'PUT') {
    $id = (int) $matches[1];
    $controller->update($id);
}

// DELETE /api/campaign/delete/{id}
if (preg_match('#^/api/campaign/delete/(\d+)$#', $uri, $matches) && $method === 'DELETE') {
    $id = (int) $matches[1];
    $controller->delete($id);
}

// GET /api/campaign/get
if (preg_match('#^/api/customer/(\d+)/campaigns$#', $uri, $matches) && $method === 'GET') {
    $customerId = (int)$matches[1];
    $controller->find($customerId);
}


// POST /api/campaign/create
if ($uri === '/api/campaign/create' && $method === 'POST') {
    $controller->create();
}
