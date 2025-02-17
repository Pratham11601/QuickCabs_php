<?php
$host = 'localhost';
$dbname = 'sync_quickcab_db';
$username = 'root';
$password = 'Sync@116';

$conn = new mysqli($host, $username, $password, $dbname);


if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>