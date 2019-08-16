<?php
return [
    'APP' => [
        'NAME' => 'Application Name',
        'ENVIRONMENT' => getenv('APP_ENVIRONMENT'),
    ],
    // Method client used to connect to server
    'CLIENT_TYPE' => $this->clientType(),
    // Domain of the server
    'DOMAIN' => $this->domain(),
    // Server name
    'HOST' => gethostname(),
    // Directory paths
    'PATHS' => [
        'ROOT' => dirname(__DIR__) . '/',
        'DOCUMENT_ROOT' => $_SERVER['DOCUMENT_ROOT'] . '/',
    ],
    // Timezone of application
    'TIMEZONE' => 'Australia/Brisbane',
    // Redirect guests when on restricted page
    'GUESTS_REDIRECT' => false,
    // Show if page has restricted access or show page not found
    'VISIBLE_RESTRICTIONS' => true,
    // Show debug output
    'DEBUG' => false,
];