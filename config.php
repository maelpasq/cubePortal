<?php
return [
    'debug' => false,
    'db' => [
        // Valeurs par défaut “vides” (pas de secret)
        'dsn' => '',
        'user' => '',
        'pass' => '',
        'options' => [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ],
    ],
];