<?php

return [
    'default' => env('FILESYSTEM_DISK', 'local'),
    'disks' => [
        'local' => [
            'driver' => 'local',
            'root' => storage_path('app'),
            'throw' => false,
        ],
        'public' => [
            'driver' => 'local',
            'root' => storage_path('app/public'),
            'url' => env('APP_URL') . '/storage',
            'visibility' => 'public',
            'throw' => false,
        ],
        's3' => [
            'driver' => 's3',
            'key' => env('S3_ACCESS_KEY'),
            'secret' => env('S3_SECRET_KEY'),
            'region' => env('S3_REGION', 'ru-1a'),
            'bucket' => env('S3_BILLING_BUCKET', '...'),
            'endpoint' => env('S3_HOST') . (env('S3_PORT') ? ':' . env('S3_PORT') : ''),
            'use_path_style_endpoint' => env('S3_PATHSTYLE', false),
            'throw' => false,
            'http' => [
                'verify' => env('UM_CANCEL_INNER_SSL', true)  // Добавлена поддержка отключения SSL
            ]
        ],
    ],
    'links' => [
        public_path('storage') => storage_path('app/public'),
    ],
];
