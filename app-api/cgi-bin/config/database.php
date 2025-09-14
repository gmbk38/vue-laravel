<?php

return [
    'default' => 'master',
    'connections' => [
        'mysql' => [
            'driver' => 'mysql',
            'charset' => env('MYSQL_CHARSET', 'utf8mb4'),  // Чтоб laravel не возвращал ??? вместо кириллицы
            'collation' => env('MYSQL_COLLATION', 'utf8mb4_unicode_ci'),  // Чтоб laravel не возвращал ??? вместо кириллицы
            'host' => env('MYSQL_HOST', '127.0.0.1'),
            'port' => env('MYSQL_PORT', 3306),
            'database' => env('MYSQL_DBNAME', '...'),
            'username' => env('MYSQL_USER', '...'),
            'password' => env('MYSQL_PASSWORD', ''),
        ]
        // Конфиг для PostgreSQL
        // 'pgsql' => [
        //     'driver' => 'pgsql',
        //     'host' => env('POSTGRES_HOST', '...'),
        //     'port' => env('POSTGRES_PORT', 5432),
        //     'database' => env('POSTGRES_DBNAME', '...'),
        //     'username' => env('POSTGRES_USER', '...'),
        //     'password' => env('POSTGRES_PASSWORD', ''),
        //     'charset' => env('POSTGRES_CHARSET', 'utf8'),
        //     'prefix' => '',
        //     'schema' => 'public',
        //     'sslmode' => 'prefer',
        // ],
    ],
];
