<?php
namespace App\Providers;

use App\Providers\AuthProvider;
use App\Helpers\HTTPHelper;
use App\Factories\RequestFactory;
use App\Models\HttpRequestModel;
/**
 * Manage client request
 * 
 * Create/setup client request to from url
 * and manage request state.
 */
final class HttpRequestProvider
{
    private $request = null;
    private $currentRequests = null;
    private $designatedRequests = [];

    public function __construct(string $uri)
    {
        $this->request = RequestFactory::simpleHttpRequest($uri, getenv('REQUEST_METHOD'), config('CLIENT_TYPE'));
    }
    public function getRequest()
    {
        return $this->request;
    }
    public function matchRequests()
    {
        $selectedRequest = RequestFactory::emptyHttpRequest();

        foreach ($this->designatedRequests as $key => $request) {
            if ($this->request->matchRequest($request)) {
                if ($this->request->requireMethod($request->method)) {
                    if (! $selectedRequest->valid()) {
                        $request->uri = $this->request->uri;
                        $selectedRequest = $request;
                        break;
                    }
                }
            }
        }
        
        $this->request = $selectedRequest;
        return $this->request;
    }
    /**
     * 
     */
    private function createRequest(string $method, string $requestString, string $actionString = '')
    {
        $this->currentRequests = RequestFactory::httpRequest($requestString, $actionString, $method, config('CLIENT_TYPE'), $this->request->route);
        return $this->currentRequests;
    }
    /**
     * 
     */
    public function request(string $match, string $actionString)
    {
        $this->createRequest(getenv('REQUEST_METHOD'), $match, $actionString);
        if (! is_null($this->currentRequests)) {
            $this->designatedRequests[] = $this->currentRequests;
        }
        return $this;
    }
    /**
     * 
     */
    public function get(string $match, string $actionString)
    {
        $this->createRequest('GET', $match, $actionString);
        if (! is_null($this->currentRequests)) {
            $this->designatedRequests[] = $this->currentRequests;
        }
        return $this;
    }
    /**
     * 
     */
    public function post(string $match, string $actionString)
    {
        $this->createRequest('POST', $match, $actionString);
        if (! is_null($this->currentRequests)) {
            $this->designatedRequests[] = $this->currentRequests;
        }
        return $this;
    }
    /**
     * Get request parameters
     */
    private function getParams($params)
    {
        $requestParams = $this->currentRequests->params;
        $params = (array) $params;
        foreach ($params as $key => $value) {
            if (is_string($value)) {
                $index = trim($value, '{}');
                if ($value !== $index) {
                    $content = null;
                    if (is_numeric($key)) {
                        unset($params[$key]);
                        if (isset($requestParams[$index])) {
                            $content = $requestParams[$index];
                        } elseif (HTTPHelper::isGet($index)) {
                            $content = HTTPHelper::get($index);
                        }
                    } else {
                        if (isset($requestParams[$index])) {
                            $content = $requestParams[$index];
                        } elseif (HTTPHelper::isGet($index)) {
                            $content = HTTPHelper::get($index);
                        }
                        $index = $key;
                    }
                    $params[$index] = $content;
                }
            }
        }
        return $params;
    }
    /**
     * 
     */
    public function requireMethod($method)
    {
        if (! is_null($this->currentRequests) && $this->currentRequests->valid()) {
            $methods = (array) $method;
            foreach ($methods as $key => $value) {
                $methods[$key] = trim($value);
            }
            if (! is_null($this->currentRequests)
            && ! is_null($this->currentRequests->method)
            && ! (in_array($this->currentRequests->method, $methods)
            || $this->currentRequests->method === 'OPTIONS')) {
                $this->currentRequests->respond(405);
                $this->currentRequests->message = 'Request Method not allowed';
            }
        }
        
        return $this;
    }
    /**
     * 
     */
    public function auth()
    {
        // must be authorized to enter
        if (! is_null($this->currentRequests)
        && $this->currentRequests->valid()
        && ! AuthProvider::isAuthorized()) {
            $this->currentRequests->respond(401);
            $this->currentRequests->message = 'Request require authorization';
            return $this;
        }
        return $this;
    }
    /**
     * 
     */
    public function guest()
    {
        // must not be authorized to enter
        if (! is_null($this->currentRequests)
        && $this->currentRequests->valid()
        && AuthProvider::isAuthorized()) {
            $this->currentRequests->respond(403);
            $this->currentRequests->message = 'Request only allowed as guest';
            return $this;
        }
        return $this;
    }
    /**
     * 
     */
    public function extension($ext)
    {
        $ext = (array) $ext;
        if (! is_null($this->currentRequests) && $this->currentRequests->valid()) {
            if (is_array($ext)) {
                foreach ($ext as $file => $type) {
                    $extension = '';
                    if (is_numeric($file)) {
                        if (isset($this->currentRequests->params['ext'])) {
                            $extension = $this->currentRequests->params['ext'];
                        } elseif (isset($this->currentRequests->params['file'])) {
                            $file = $this->currentRequests->params['file'];
                            $pos = strpos(strrev($file), '.');
                            $extension = substr($file, -$pos);
                        }
                        if (! in_array($extension, $ext)) {
                            $this->currentRequests->respond(415);
                            $this->currentRequests->message = 'Invalid File Extension';
                            return $this;
                        }
                    } elseif (is_string($file)) {
                        if (isset($this->currentRequests->params[$file])) {
                            $file = $this->currentRequests->params[$file];
                            $pos = strpos(strrev($file), '.');
                            $extension = substr($file, -$pos);
                        }
                        if (is_array($type)) {
                            if (! in_array($extension, $type)) {
                                $this->currentRequests->respond(415);
                                $this->currentRequests->message = 'Invalid File Extension';
                                return $this;
                            }
                        } elseif (is_string($type)) {
                            if ($extension !== $type) {
                                $this->currentRequests->respond(415);
                                $this->currentRequests->message = 'Invalid File Extension';
                                return $this;
                            }
                        }
                    }
                }
            }
        }
        return $this;
    }
    /**
     * Add to request parameters
     * 
     */
    public function params($params)
    {
        // add params to request
        if (! empty($params) 
        && ! is_null($this->currentRequests) 
        && $this->currentRequests->valid()) {
            $this->currentRequests->params = array_merge($this->currentRequests->params, $this->getParams($params));
        }
        return $this;
    }
    /**
     * Check request header
     */
    public function header($headers)
    {
        $serverHeaders = $_SERVER;
        $headers = (array) $headers;
        if (! empty($headers)) {
            foreach ($headers as $key => $header) {
                if (is_numeric($key) && is_string($header)) {
                    if (! array_key_exists($header, $serverHeaders)) {
                        $this->currentRequests->respond(404); // TODO: check correct http response
                        $this->currentRequests->message = 'Headers do not match';
                    }
                } elseif (is_string($key) && is_string($header)) {
                    if (! array_key_exists($key, $serverHeaders)
                    || $serverHeaders[$key] !== $header) {
                        $this->currentRequests->respond(404); // TODO: check correct http response
                        $this->currentRequests->message = 'Headers do not match';
                    }
                } else {
                    $this->currentRequests->respond(404); // TODO: check correct http response
                    $this->currentRequests->message = 'Headers do not match';
                }
            }
        }
        return $this;
    }
    /**
     * Set request parameters
     * 
     */
    public function setParams($params)
    {
        // add params to request
        if (! empty($params) 
        && ! is_null($this->currentRequests) 
        && $this->currentRequests->valid()) {
            $this->currentRequests->params = $this->getParams($params);
        }
        return $this;
    }
    /**
     * 
     */
    public function redirect(string $uri, bool $clearParams = true)
    {
        if (! is_null($this->currentRequests) 
        && ! $this->currentRequests->valid() && is_null($this->currentRequests->redirect)) {
            $this->currentRequests->redirect = $uri;
            if ($clearParams === true) {
                $this->currentRequests->params = [];
            }
        }
        return $this;
    }
    /**
     * 
     */
    public function changeAction(string $actionString)
    {
        if (! is_null($this->currentRequests)) {
            RequestFactory::controllerAction($this->currentRequests, $actionString);
        }
        return $this;
    }
    /**
     * Close request on $state true
     */
    public function close($state)
    {
        if (! empty($state)) {
            // detach for request because state is true
            $this->currentRequests = RequestFactory::emptyHttpRequest();
        }
        return $this;
    }
    /**
     * 
     */
    public function respond(int $code)
    {
        if (! is_null($this->currentRequests)) {
            $this->currentRequests->respond($code);
        }
        return $this;
    }
    /**
     * Close request if request isn't valid
     */
    public function isValid()
    {
        if (is_null($this->currentRequests) || ! ($this->currentRequests->valid())) {
            $this->currentRequests = RequestFactory::emptyHttpRequest();
        }
        return $this;
    }
}