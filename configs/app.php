<?php
return [
    'APP' => [
        'NAME' => 'TestTemplate',
        'ENVIRONMENT' => getenv('APP_ENVIRONMENT'),
    ],
    'DEBUG' => false,
    'CLIENT_TYPE' => $this->clientType(),
    'DOMAIN' => $this->domain(),
    'HOST' => gethostname(),
    'PATHS' => [
        'ROOT' => dirname(__DIR__) . '/',
        'DOCUMENT_ROOT' => $_SERVER['DOCUMENT_ROOT'] . '/',
    ],
    'TIMEZONE' => 'Australia/Brisbane'
];