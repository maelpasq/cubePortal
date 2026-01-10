<?php
// Configuration & Database Connection
// This file should be excluded from version control (gitignore)

$host = 'mysql-pamal-studio.alwaysdata.net';
$dbname = 'pamal-studio_cube_portal';
$username = '392241';
$password = 'pa19mal09Studio2024';
$charset = 'utf8mb4';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=$charset", $username, $password);
    // Enable exceptions for errors
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    // Show error only if necessary or log it. 
    // die("Connection failed: " . $e->getMessage());
    die("Erreur de connexion base de données.");
}