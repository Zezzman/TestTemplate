<?php
namespace App\Models;

use App\Interfaces\IRequest;
/**
 * 
 */
abstract class RequestModel implements IRequest
{
    public $type = null;
    public $controller = null;
    public $action = null;
    public $params = [];
    public $response = null;
    public $message = null;

    /**
     * Check if request is valid
     * 
     * Request needs specific fields filled
     * to be a valid request
     * 
     * @return   boolean    returns true if request is valid
     */
    public abstract function valid();
    /**
     * Empty Request
     */
    public static function empty()
    {
        return new static();
    }
}