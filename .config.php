<?php
// Hidden config file for DB credentials.
return [
    'db' => [
        'dsn' => 'mysql:host=localhost;dbname=cube_portal;charset=utf8mb4',
        'user' => 'cube_user',
        'pass' => 'change_me',
        'options' => [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ],
    ],
];
