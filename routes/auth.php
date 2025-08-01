<?php
require_once __DIR__ . '/../controllers/AuthController.php';

$conn = $GLOBALS['conn']; // ambil koneksi dari global
$controller = new AuthController($conn);

// Routingnya
$uri = $_SERVER['REQUEST_URI'];
$method = $_SERVER['REQUEST_METHOD'];

if ($uri === '/api/auth/login' && $method === 'POST') {
    $controller->login();
}

if ($uri === '/api/auth/register' && $method === 'POST') {
    $controller->register();
}

if ($uri === '/api/auth/get' && $method === 'GET') {
    $controller->find();
}

if (preg_match('#^/api/auth/update/(\d+)$#', $uri, $matches) && $method === 'PUT') {
    $id = (int) $matches[1];
    $controller->update($id);
}

if (preg_match('#^/api/auth/delete/(\d+)$#', $uri, $matches) && $method === 'DELETE') {
    $id = (int) $matches[1];
    $controller->delete($id);
}