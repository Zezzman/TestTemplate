<?php
namespace App\Interfaces;

use Exception;
/**
 * 
 */
interface IController
{
    function getRequest();

    static function respond(int $code, string $message = null, IRequest $request = null, Exception $exception = null);
}