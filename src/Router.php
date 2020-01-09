<?php
namespace App;

use App\Helpers\HTTPHelper;
use App\Providers\AuthProvider;
use App\Providers\HttpRequestProvider;
use App\Providers\CLIRequestProvider;
use App\Models\HttpRequestModel;
use App\Models\CLIRequestModel;
/**
 * Process client requests into request object
 * 
 * @author  Francois Le Roux <francoisleroux97@gmail.com>
 */
class Router
{
    public $request = null;
    private $requestType = null;
    private $requestMethod = null;
    /**
     * 
     */
    public function __construct()
    {
        $this->requestMethod = getenv('REQUEST_METHOD');
        $this->requestType = config('CLIENT_TYPE');

        if ($this->requestType === 'webserver') {
            $this->webRoutes();
        } elseif ($this->requestType === 'api') {
            $this->apiRoutes();
        }  elseif ($this->requestType === 'cronjob') {
            $this->cronjobRoutes();
        } elseif ($this->requestType === 'cli') {
            $this->cliRoutes();
        }
    }
    /**
     * 
     */
    public function type()
    {
        return $this->requestType;
    }
    /**
     * 
     */
    public function method()
    {
        return $this->requestMethod;
    }
    /**
     * 
     */
    private function webRoutes()
    {
        // Client request
        $uri = HTTPHelper::URI();
        $provider = new HttpRequestProvider($uri);
        // Load available request
        $web = config('CLOSURES.PATH')('ROUTES') . 'web.php';
        if (file_exists($web)) {
            require($web);
        } else {
            throw new Exception('Web Route File Not Found');
        }
        // Find matching request
        $this->request = $provider->matchRequests();
    }
    /**
     * 
     */
    private function apiRoutes()
    {
        $uri = HTTPHelper::URI();
        $provider = new HttpRequestProvider($uri);
        $api = config('CLOSURES.PATH')('ROUTES') . 'api.php';
        if (file_exists($api)) {
            require($api);
        } else {
            throw new Exception('API Route File Not Found');
        }
        if ($this->requestMethod === 'OPTIONS') {
            header('Access-Control-Allow-Origin: https://localhost');
            header("Access-Control-Max-Age: 3600");
        }
        $this->request = $provider->matchRequests();
    }
    /**
     * 
     */
    private function cliRoutes()
    {
        $commands = config('APP.ARGV');
        $provider = new CLIRequestProvider($commands);
        $cli = config('CLOSURES.PATH')('ROUTES') . 'cli.php';
        if (file_exists($cli)) {
            require($cli);
        } else {
            throw new Exception('CLI Route File Not Found');
        }
        $this->request = $provider->matchRequests($commands);
    }
    /**
     * 
     */
    private function cronjobRoutes()
    {

    }
    private function isAuth()
    {
        return AuthProvider::isAuthorized();
    }
    private function Referer()
    {
        return SessionProvider::get('refererURI');
    }
    private function RefererCode()
    {
        return SessionProvider::get('refererCode');
    }
}