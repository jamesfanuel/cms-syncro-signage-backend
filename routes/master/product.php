<?php
require_once __DIR__ . '/../../controllers/master/ProductController.php';
require_once __DIR__ . '/../../services/master/ProductService.php';
require_once __DIR__ . '/../../repositories/master/ProductRepository.php';
require_once __DIR__ . '/../../models/master/Product.php';

$conn = $GLOBALS['conn'];

$repository = new ProductRepository($conn);
$service = new ProductService($repository);
$controller = new ProductController($service);

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$method = $_SERVER['REQUEST_METHOD'];

// ================= PRODUCT CATEGORY ==================

// // GET /api/product/category/get
// if ($uri === '/api/product/category/get' && $method === 'GET') {
//     $controller->findCategories();
// }

// GET /api/product/category/get/{id}
if (preg_match('#^/api/product/category/get/(\d+)$#', $uri, $matches) && $method === 'GET') {
    $controller->findCategoryById((int) $matches[1]);
}

// POST /api/product/category/create
if ($uri === '/api/product/category/create' && $method === 'POST') {
    $controller->createCategory();
}

// PUT /api/product/category/update/{id}
if (preg_match('#^/api/product/category/update/(\d+)$#', $uri, $matches) && $method === 'PUT') {
    $controller->updateCategory((int) $matches[1]);
}

// DELETE /api/product/category/delete/{id}
if (preg_match('#^/api/product/category/delete/(\d+)$#', $uri, $matches) && $method === 'DELETE') {
    $controller->deleteCategory((int) $matches[1]);
}

if (preg_match('#^/api/customer/(\d+)/categories$#', $uri, $matches) && $method === 'GET') {
    $customerId = (int)$matches[1];
    $controller->findCategories($customerId);
}

// ================= PRODUCT ==================

// GET /api/product/item/get
if ($uri === '/api/product/item/get' && $method === 'GET') {
    $controller->findProducts();
}

// GET /api/product/item/get/{id}
if (preg_match('#^/api/product/item/get/(\d+)$#', $uri, $matches) && $method === 'GET') {
    $controller->findProductById((int) $matches[1]);
}

// POST /api/product/item/create
if ($uri === '/api/product/item/create' && $method === 'POST') {
    $controller->createProduct();
}

// PUT /api/product/item/update/{id}
if (preg_match('#^/api/product/item/update/(\d+)$#', $uri, $matches) && $method === 'PUT') {
    $controller->updateProduct((int) $matches[1]);
}

// DELETE /api/product/item/delete/{id}
if (preg_match('#^/api/product/item/delete/(\d+)$#', $uri, $matches) && $method === 'DELETE') {
    $controller->deleteProduct((int) $matches[1]);
}

if (preg_match('#^/api/customer/(\d+)/products$#', $uri, $matches) && $method === 'GET') {
    $customerId = (int)$matches[1];
    $controller->findProducts($customerId);
}

// ================= PRODUCT VERSION ==================

// GET /api/product/version/get/{product_id}
if (preg_match('#^/api/product/version/get/(\d+)$#', $uri, $matches) && $method === 'GET') {
    $controller->findVersions((int) $matches[1]);
}

// POST /api/product/version/create
if ($uri === '/api/product/version/create' && $method === 'POST') {
    $controller->createVersion();
}

// PUT /api/product/version/update/{id}
if (preg_match('#^/api/product/version/update/(\d+)$#', $uri, $matches) && $method === 'PUT') {
    $controller->updateVersion((int) $matches[1]);
}

// DELETE /api/product/version/delete/{id}
if (preg_match('#^/api/product/version/delete/(\d+)$#', $uri, $matches) && $method === 'DELETE') {
    $controller->deleteVersion((int) $matches[1]);
}

if (preg_match('#^/api/customer/(\d+)/versions$#', $uri, $matches) && $method === 'GET') {
    $customerId = (int)$matches[1];
    $controller->findVersions($customerId);
}

if (preg_match('#^/api/product/version/(\d+)/upload$#', $uri, $matches) && $method === 'POST') {
    $versionId = (int)$matches[1];
    $controller->uploadVersion($versionId);
}