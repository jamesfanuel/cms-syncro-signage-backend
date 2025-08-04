<?php
require_once __DIR__ . '/../../controllers/order/OrderController.php';
require_once __DIR__ . '/../../services/order/OrderService.php';
require_once __DIR__ . '/../../repositories/order/OrderRepository.php';
require_once __DIR__ . '/../../models/order/Order.php';

$conn = $GLOBALS['conn'];

$repository = new OrderRepository($conn);
$service = new OrderService($repository);
$controller = new OrderController($service);

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$method = $_SERVER['REQUEST_METHOD'];

// GET /api/order/get/{id}
if (preg_match('#^/api/order/get/(\d+)$#', $uri, $matches) && $method === 'GET') {
    $id = (int) $matches[1];
    $controller->findById($id);
}

// PUT /api/order/update/{id}
if (preg_match('#^/api/order/update/(\d+)$#', $uri, $matches) && $method === 'PUT') {
    $id = (int) $matches[1];
    $controller->updateOrder($id);
}

// DELETE /api/order/delete/{id}
if (preg_match('#^/api/order/delete/(\d+)$#', $uri, $matches) && $method === 'DELETE') {
    $id = (int) $matches[1];
    $controller->deleteOrder($id);
}

// GET /api/order/get
if (preg_match('#^/api/customer/(\d+)/orders$#', $uri, $matches) && $method === 'GET') {
    $customerId = (int)$matches[1];
    $controller->findOrders($customerId);
}


// POST /api/order/create
if ($uri === '/api/order/create' && $method === 'POST') {
    $controller->createOrder();
}

/////////////////////


// GET /api/order/item/get/{id}
if (preg_match('#^/api/order/item/get/(\d+)$#', $uri, $matches) && $method === 'GET') {
    $id = (int) $matches[1];
    $controller->findOrderItemById($id);
}

// PUT /api/order/item/update/{id}
if (preg_match('#^/api/order/item/update/(\d+)$#', $uri, $matches) && $method === 'PUT') {
    $id = (int) $matches[1];
    $controller->updateOrderItem($id);
}

// DELETE /api/order/item/delete/{id}
if (preg_match('#^/api/order/item/delete/(\d+)$#', $uri, $matches) && $method === 'DELETE') {
    $id = (int) $matches[1];
    $controller->deleteOrderItem($id);
}

// GET /api/order/item/get
if (preg_match('#^/api/customer/(\d+)/orders/(\d+)/item$#', $uri, $matches) && $method === 'GET') {
    $customerId = (int)$matches[1];
    $orderId = (int)$matches[2];
    $controller->findOrderItem($customerId, $orderId);
}

// POST /api/order/item/create
if ($uri === '/api/order/item/create' && $method === 'POST') {
    $controller->createOrderItem();
}