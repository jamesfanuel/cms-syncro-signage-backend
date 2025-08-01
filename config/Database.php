<?php
function getDatabaseConnection()
{
    $host = "localhost";
    $db   = "syncro_signage_db";
    $user = "root";
    $pass = "rahasia123";

    $mysqli = new mysqli($host, $user, $pass, $db);

    if ($mysqli->connect_error) {
        die("Connection failed: " . $mysqli->connect_error);
    }

    return $mysqli;
}
