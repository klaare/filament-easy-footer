<?php

return [
    'app_name' => env('APP_NAME', 'Filament Footer'),
    'github' => [
        'enabled' => false,
        'repository' => env('GITHUB_REPOSITORY', ''),
        'token' => env('GITHUB_TOKEN', ''),
        'cache_ttl' => env('GITHUB_CACHE_TTL', 3600),
        'show_logo' => true,
        'show_url' => true,
    ],
];
