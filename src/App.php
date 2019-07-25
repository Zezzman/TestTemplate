<?php
use App\Interfaces\IRequest;
use App\Controller;
use App\APIController;
use App\Providers\EnvironmentProvider;
use App\Exceptions\RespondingException;
/**
 * Handles App
 * 
 * Initiates Application
 * and executes Controller
 * 
 * @author  Francois Le Roux <francoisleroux97@gmail.com>
 */
final class App
{
    private static $instance = null;
    public $environment = null;
    /**
     * Private the constructor to stop instantiations
     */
    private function __construct(){}
    /**
     * Configure App pre-launch
     * 
     * @param   string      $applicationName        app name
     * 
     * @return  self     return self instance
     */
    public static function setup()
    {
        if (is_null(self::$instance)) {
            $instance = &self::$instance;
            $instance = new self();

            // Load configurations
            $instance->environment = EnvironmentProvider::instance();
            $instance->environment->setup();
            
            // Set debug output
            if (config('DEBUG')) {
                error_reporting(E_ALL);
                ini_set('display_errors', E_ALL);
            }
            
            // Set timezone
            date_default_timezone_set(config('TIMEZONE', 'Australia/Brisbane'));
            
            // Set class Instance
            self::$instance = $instance;
        }

        return self::$instance;
    }
    /**
     * Instance of the App
     * 
     * @return  self     return self instance
     */
    public static function instance()
    {
        return self::$instance;
    }
    /**
     * Run app with request provided to from client
     * to access resources
     * 
     * Process request into web or api controller and construct
     * controller and method requested from client request
     * 
     * @param   IRequest     $request           request sent from the client
     */
    public function run(IRequest $request)
    {
        $type = config('CLIENT_TYPE');
        if ($type === 'webserver') {
            /**
             * Handle request for Web Servers
             */
            if ($this->webController($request) === false) {
                // route to 404 error view
                Controller::respond(404, 'Invalid Request', $request);
            }
            return true;
        } elseif ($type === 'api') {
            /**
             * Handle request for APIs
             */
            if ($this->apiController($request) === false) {
                // return 404 error response
                APIController::respond(404, "Invalid Request", $request);
            }
            return true;
        } elseif ($type === 'cli') {
            /**
             * Handle request for Commands
             */
            if ($this->cliController($request) === false) {
                // return 404 error response
                // CLIController::respond(404, "Invalid Request", $request);
            }
            return true;
        } elseif ($type === 'cronjob') {
            /**
             * Handle request for Cronjobs
             */
            return true;
        }
        http_response_code(404);
        exit();
    }
    /**
     * Call controller and method requested from client request
     * 
     * @param   IRequest     $request           request sent from the client
     * 
     * @return  bool     return true if controller and method is successfully called
     */
    private function webController(IRequest $request = null)
    {
        if (! is_null($request)) {
            $controller = $request->controller;
            $action = $request->action;
            $params = $request->params;

            if (! is_null($request->response)) {
                Controller::respond($request->response, $request->message, $request);
            }
            if (! is_null($controller) && ! is_null($action) && ! is_null($params)) {
                $path = "App\\Controllers\\{$controller}Controller";
                try {
                    $controller = $this->executeController($request, $path, $action, $params);
                    http_response_code(200);
                } catch (RespondingException $e) {
                    Controller::respond($e->respondCode(), '', $request, $e);
                } catch (PDOException $e) {
                    Controller::respond(503, '', $request, $e);
                } catch (Exception $e) {
                    Controller::respond(500, '', $request, $e);
                }
                return true;
            }
        }
        return false;
    }
    /**
     * Call controller and method requested from client request
     * 
     * @param   IRequest     $request           request sent from the client
     * 
     * @return  bool     return true if controller and method is successfully called
     */
    private function apiController(IRequest $request = null)
    {
        if (! is_null($request)) {
            $controller = $request->controller;
            $action = $request->action;
            $params = $request->params;

            if (! is_null($request->response)) {
                APIController::respond($request->response, $request->message, $request);
            }
            if (! is_null($controller) && ! is_null($action) && ! is_null($params)) {
                $path = "App\\API\\{$controller}Controller";
                try {
                    $controller = $this->executeController($request, $path, $action, $params);
                    if (is_null($controller->getContent())) {
                        APIController::respond(204, '', $request);
                    } else {
                        http_response_code(200);
                    }
                } catch (RespondingException $e) {
                    APIController::respond($e->respondCode(), '', $request, $e);
                } catch (PDOException $e) {
                    APIController::respond(503, '', $request, $e);
                } catch (Exception $e) {
                    APIController::respond(500, '', $request, $e);
                }
                return true;
            }
        }
        return false;
    }
    /**
     * Call controller and method requested from client request
     * 
     * @param   IRequest     $request           request sent from the client
     * 
     * @return  bool     return true if controller and method is successfully called
     */
    private function cliController(IRequest $request = null)
    {
        if (! is_null($request)) {
            $controller = $request->controller;
            $action = $request->action;
            $params = $request->params;

            if (! is_null($request->response)) {
                // APIController::respond($request->response, $request->message, $request);
            }
            if (! is_null($controller) && ! is_null($action) && ! is_null($params)) {
                $path = "App\\CLI\\{$controller}Controller";
                try {
                    $controller = $this->executeController($request, $path, $action, $params);
                } catch (Exception $e) {
                    // APIController::respond(500, '', $request, $e);
                }
                return true;
            }
        }
        return false;
    }
    /**
     * Call controller and method requested
     */
    private function executeController(IRequest $request,
        string $controllerPath, string $action, array $params)
    {
        if (class_exists($controllerPath, true)) {
            $class = new $controllerPath($request);
            if (method_exists($class, $action)) {
                $params = array_values($params);
                if (is_array($params) && count($params) > 0) {
                    $method = $class->$action(...$params);
                } else {
                    $method = $class->$action();
                }
                return $class;
            } else {
                throw new Exception("Error finding Controller Method: $controllerPath::$action()");
            }
        } else {
            throw new Exception("Error finding Controller: $controllerPath");
        }
    }
    public static function Responses()
    {
        return [
            200 => 'OK',
            204 => 'NoContent',
            401 => 'Unauthorized',
            403 => 'Forbidden',
            404 => 'NotFound',
            405 => 'MethodNotAllowed',
            415 => 'InvalidMediaType',
            500 => 'InternalError',
            503 => 'ServiceUnavailable',
        ];
    }
}

/**
 * Get Configuration
 * 
 * @param   string      $constant           constant name within configuration settings
 * @param   mix         $default            default value returned when key does not exist
 * 
 * @return  string|bool     return value related to the $key or $default when value is not found
 */
function config(string $constant, $default = false)
{
    if (! is_null(App::instance())) {
        return EnvironmentProvider::instance()->configurations($constant, $default);
    }
    throw new Exception('App not instantiated');
}
/**
 * Set Configuration
 * 
 * @param   string      $constant           constant name within configuration settings
 * @param   bool        $value              value of the constant that will be set
 * 
 * @return  bool     return true when value is set
 */
function setConfig(string $constant, $value)
{
    if (! is_null(App::instance())) {
        return EnvironmentProvider::instance()->set($constant, $value);
    }
    throw new Exception('App not instantiated');
}