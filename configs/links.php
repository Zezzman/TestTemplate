<?php
$root = config('DOMAIN');
return [
    'LINKS' => [
        'PUBLIC' => $root,
        'IMAGES' => $root . 'assets/images/',
        'JS' => $root . 'assets/javascript/',
        'CSS' => $root . 'assets/css/',
        'PLUGINS' => $root . 'assets/plugins/',
        'STORAGE' => $root . 'storage/',
    ]
];