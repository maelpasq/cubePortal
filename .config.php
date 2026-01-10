<?php
// Hidden config file for DB credentials.
return [
    'db' => [
        'dsn' => 'mysql:host=mysql-pamal-studio.alwaysdata.net;dbname=pamal-studio_cube_portal;charset=utf8mb4',
        'user' => '392241',
        'pass' => 'pa19mal09Studio2024!',
        'options' => [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ],
    ],
];
