<?php

return [
    'db' => [
        'dsn' => 'mysql:host=localhost;dbname=cube_portal;charset=utf8mb4',
        'user' => 'cube_portal_user',
        'password' => 'change_me_now',
    ],
    'security' => [
        'app_key' => 'replace_with_long_random_string',
        'initial_admin_email' => 'admin@cube-portal.local',
        'initial_admin_password' => 'ChangeMe123!',
        'initial_admin_name' => 'Admin Cube',
    ],
    'app' => [
        'base_url' => '/',
    ],
];
