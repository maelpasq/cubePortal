<?php
// Config - Database Connection
$config = require __DIR__ . '/.env.php';

$host = $config['db_host'];
$dbname = $config['db_name'];
$username = $config['db_user'];
$password = $config['db_pass'];
$charset = $config['db_charset'] ?? 'utf8mb4';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=$charset", $username, $password);
    // Enable exceptions for errors
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    // En production, ne pas afficher l'erreur brute
    die("Erreur de connexion à la base de données : " . $e->getMessage());
}
