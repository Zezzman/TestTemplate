<?php
error_reporting(0);
/**
 * Register Composer auto loader
 */
if (is_file(dirname(__DIR__) . '/vendor/autoload.php'))
{
    require_once(dirname(__DIR__) . '/vendor/autoload.php');
}
else
{
    echo 'Autoload Not Found';
    throw new \Exception('Autoload Not Found');
    exit();
}
/**
 * Load Application
 */
if (is_file(dirname(__DIR__) . '/src/System/Launcher.php'))
{
    require_once(dirname(__DIR__) . '/src/System/Launcher.php');
}
else
{
    echo 'Launcher Not Found';
    throw new \Exception('Launcher Not Found');
    exit();
}