<?php
// filepath: /C:/laragon/www/short/config.php

// Database configuration
$host = 'localhost';
$username = 'root';
$password = '';
$database = 'slink';

// Create a connection
$conn = mysqli_connect($host, $username, $password, $database);

// Check connection
if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}
