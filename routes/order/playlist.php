<?php
require_once __DIR__ . '/../../controllers/order/PlaylistController.php';
require_once __DIR__ . '/../../services/order/PlaylistService.php';
require_once __DIR__ . '/../../repositories/order/PlaylistRepository.php';
require_once __DIR__ . '/../../models/order/Playlist.php';

$conn = $GLOBALS['conn'];

$repository = new PlaylistRepository($conn);
$service = new PlaylistService($repository);
$controller = new PlaylistController($service);

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$method = $_SERVER['REQUEST_METHOD'];


// POST /api/order/create
if ($uri === '/api/playlist/create' && $method === 'POST') {
    $controller->create();
}