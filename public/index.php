<?php
/**
 * Bootstrap Application
 */
require_once(dirname(__DIR__) . '/bootstrap/app.php');

/**
 * Create Application
 */
$app = App::setup();
setConfig('APP', ['ARGV' => ($argv ?? [])]);

/**
 * Routes
 */
$router = new App\Router();
$request = $router->request;

/**
 * Run Application
 */
$app->run($request);

/**
 * Close application
 */
exit();