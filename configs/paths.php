<?php
// Directory paths relative to ROOT
$root = requireConfig('PATHS.ROOT');
return [
    'PATHS' => [
        'PUBLIC' => 'public/',
        'RESOURCES' => 'resources/',
        'APP' => 'app/',
        'SRC' => 'src/',
        'ROUTES' => 'routes/',
        'STORAGE' => 'storage/',
        'VENDOR' => 'vendor/',
    ],
    'CLOSURES' => [
        'PATH' => function (string $constant, $default = false) use ($root) { return $root . config('PATHS.' . $constant, $default); }
    ]
];