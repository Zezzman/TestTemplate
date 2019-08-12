<?php
$root = config('PATHS.ROOT');
return [
    'PATHS' => [
        'PUBLIC' => $root . 'public/',
        'RESOURCES' => $root . 'resources/',
        'APP' => $root . 'app/',
        'SRC' => $root . 'src/',
        'ROUTES' => $root . 'routes/',
        'STORAGE' => $root . 'storage/',
        'VENDOR' => $root . 'vendor/',
    ],
];