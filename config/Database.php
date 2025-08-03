<?php
function getDatabaseConnection()
{
    $host = "localhost";
    $db   = "syncro_signage_db";
    $user = "root";
    $pass = "K4t4Kunc1!";

    $mysqli = new mysqli($host, $user, $pass, $db);

    if ($mysqli->connect_error) {
        die("Connection failed: " . $mysqli->connect_error);
    }

    return $mysqli;
}
