<?php
// Directory paths relative to ROOT
$root = requireConfig('PATHS.ROOT');
return [
    'PATHS' => [
        'PUBLIC' => $root . 'public/',
        'RESOURCES' => 'resources/',
        'APP' => '',
        'SRC' => 'src/',
        'ROUTES' => 'routes/',
        'STORAGE' => 'storage/',
        'VENDOR' => 'vendor/',
    ]
];