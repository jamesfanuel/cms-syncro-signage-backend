<?php
// cors.php
header("Access-Control-Allow-Origin: *"); // atau spesifik: http://192.168.210.86:5173
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
