<?php
namespace App\Models;

use App\Models\RequestModel;
/**
 * 
 */
class HttpRequestModel extends RequestModel
{
    public $uri = '';
    public $requestPattern = '';
    public $route = null;
    public $method = '';
    public $redirect = null;

    /**
     *  Initiate a request
     * 
     * @param   string  $uri        request query
     */
    public function __construct(string $requestPattern = '')
    {
        $this->requestPattern = $requestPattern;
    }
    /**
     * Check if request is valid
     * 
     * Request needs specific fields filled
     * to be a valid request
     * 
     * @return   boolean    returns true if request is valid
     */
    public function valid()
    {
        if (empty($this->uri)
        || ! is_null($this->response)
        || ! is_array($this->route)
        || empty($this->route)
        || empty($this->controller)
        || empty($this->action)
        || empty($this->type)) {
            return false;
        } else {
            return true;
        }
    }
    /**
     * Set Http Response code
     * 
     * When request is handled the response will be set
     * 
     * @param   int     $code       http response code
     */
    public function respond($code)
    {
        $this->redirect = null;
        $this->response = $code;

        return $this;
    }
    /**
     * 
     */
    public function requireMethod(string $method)
    {
        if ($this->method !== '' && $method !== ''
        && $this->method !== $method) {
            return false;
        }
        return true;
    }
    /**
     * 
     */
    public function matchRequest(self $request)
    {
        if (! isset($request->route)) {
            return false;
        }
        return $this->matchRoutes($request->route);
    }
    /**
     * 
     */
    public function matchRoutes(array $route)
    {
        $route1 = $this->route;
        $route2 = $route;
        reset($route1);
        reset($route2);

        if (count($route1) === count($route2) || array_key_exists('append', $route2)) {
            foreach ($route2 as $key => $value) {
                $index = trim($value, '{}');
                if ($key !== 'append' && $index === $value) {
                    if (isset($route1[$key])) {
                        if ($value !== $route1[$key]) {
                            return false;
                        }
                    } else {
                        return false;
                    }
                }
            }
            return true;
        }
        return false;
    }
}