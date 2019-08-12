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
        require(config('PATHS.ROUTES') . 'web.php');
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
        require(config('PATHS.ROUTES') . 'api.php');
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
        $commands = config('CLI.ARGV');
        $provider = new CLIRequestProvider($commands);
        require(config('PATHS.ROUTES') . 'cli.php');
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