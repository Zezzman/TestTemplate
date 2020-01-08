<?php
// URI locations
$domain = config('DOMAIN');
return [
    'LINKS' => [
        // Application Link
        'PUBLIC' => '/',
        // Assets Directories
        'IMAGES' => 'assets/images/',
        'JS' => 'assets/javascript/',
        'CSS' => 'assets/css/',
        'PLUGINS' => 'assets/plugins/',
        // Storage Directory
        'STORAGE' => 'storage/',
        'EXPAND' => function (string $constant, $default = false) use ($domain) { return $domain . config('LINKS.' . $constant, $default); }
    ]
];