<?php
// URI locations
$root = config('DOMAIN');
return [
    'LINKS' => [
        // Application Domain Root
        'PUBLIC' => $root,
        'IMAGES' => $root . 'assets/images/',
        'JS' => $root . 'assets/javascript/',
        'CSS' => $root . 'assets/css/',
        'PLUGINS' => $root . 'assets/plugins/',
        'STORAGE' => $root . 'storage/',
    ]
];