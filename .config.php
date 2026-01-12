<?php
// Hidden config file for DB credentials.
return [
    'debug' => false,
    'db' => [
        'dsn' => 'mysql:host=nom_de_l_host;port=port;dbname=nom_de_la_db;charset=utf8mb4',
        'user' => 'identifiant',
        'pass' => 'mot-de-passe',
        'options' => [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ],
    ],
];
