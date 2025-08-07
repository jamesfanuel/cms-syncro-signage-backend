<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/helpers/response.php';

$conn = getDatabaseConnection();
$GLOBALS['conn'] = $conn;

$prefixes = [
    'auth'     => '/api/auth',
    'product'  => '/api/product',
    'client'   => '/api/client',
    'customer' => '/api/customer',
    'outlet'   => '/api/outlet',
    'formation'=> '/api/formation',
    'campaign' => '/api/campaign',
    'order'    => '/api/order',
    'playlist' => '/api/playlist',
    'license' => '/api/license',
];

// Map file ke folder khusus jika diperlukan
$folderMap = [
    'product'   => 'master',
    'client'    => 'master',
    'outlet'    => 'master',
    'formation' => 'master',
    'campaign'  => 'order',
    'order'     => 'order',
    'playlist'  => 'order',
];

foreach ($prefixes as $file => $prefix) {
    $folder = $folderMap[$file] ?? '.';
    $routeFile = __DIR__ . "/routes/{$folder}/{$file}.php";

    if (file_exists($routeFile)) {
        require_once $routeFile;
    } else {
        error_log("Route file not found: $routeFile");
    }
}
