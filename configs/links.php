<?php
// URI locations
$domain = requireConfig('DOMAIN');
return [
    'LINKS' => [
        // Application Link
        'PUBLIC' => $domain,
        // Assets Directories
        'IMAGES' => 'assets/images/',
        'JS' => 'assets/javascript/',
        'CSS' => 'assets/css/',
        'PLUGINS' => 'assets/plugins/',
        // Storage Directory
        'STORAGE' => 'storage/',
    ]
];