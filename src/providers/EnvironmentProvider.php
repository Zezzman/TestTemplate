<?php
namespace App\Providers;

use App\Helpers\ArrayHelper;
/**
 * Manage environment variables
 * 
 * set/update/manage environment variables
 */
final class EnvironmentProvider
{
    private static $instance = null;
    public $configs = [];

    private function __construct() {}

    public static function instance()
    {
        if (! is_null(self::$instance)) {
            return self::$instance;
        } else {
            return self::$instance = new self();
        }
    }
    /**
     * Setup Environment
     */
    public function setup()
    {
        // Load configurations
        $this->getConfig();
        $this->getEnvironment();
    }
    /**
     * Environment configuration settings
     * 
     * @param   string      $key                key name within configuration settings
     * @param   bool        $require            when true, throw Exception when key is not found
     * 
     * @return  string|bool     return value related to the $key and return $default when value not found
     */
    public function configurations(string $key = null, $default = false)
    {
        $configs = $this->configs;

        if (is_null($key)) {
            return $configs;
        } elseif (empty($key)) {
            return null;
        }

        $constant = ArrayHelper::deepSearch($configs, strtoupper($key));
        if (is_null($constant)) {
            return $default;
        } else {
            return $constant;
        }
    }
    /**
     * Set configuration settings
     * 
     * @param   string      $key                key name within configuration settings
     * @param   bool        $value              value of the key that will be set
     * 
     * @return  bool     return true when value is set
     */
    public function set(string $key, $value)
    {
        $configs = &$this->configs;
        if (is_array($configs)) {
            $key = trim(strtoupper($key), '/');
            $keys = explode('/', $key);
            
            for ($i = 0; $i < count($keys); $i++) {
                $index = $keys[$i];
                if ($i < count($keys) - 1) {
                    if (isset($configs[$index])) {
                        if (is_array($configs[$index])) {
                            $configs = &$configs[$index];
                        } else {
                            return false;
                        }
                    } else {
                        return false;
                    }
                } else {
                    $this->setKey($configs, $index, $value);
                }
            }
            $this->configs = $configs;
            return true;
        }
        return false;
    }
    /**
     * Set array value at key
     * 
     * @param   array       $configs            array with configurations
     * @param   string      $key                key name within array
     * @param   bool        $value              value of the key that will be set
     * 
     * @return  bool     return true when value is set
     */
    private function setKey(array &$configs, string $key, $value)
    {
        if (is_array($configs)) {
            if (isset($configs[$key])) {
                if (is_array($configs[$key])) {
                    if (is_array($value)) {
                        $configs[$key] = array_merge($configs[$key], $value); // merge values
                    } else {
                        // $configs[$key] = $value; // Set value | No push
                        array_push($configs[$key], $value); // Push value
                    }
                } else {
                    $configs[$key] = $value; // Set value
                }
            } else {
                $configs[$key] = $value; // Add value
            }
            return true;
        }
        return false;
    }
    /**
     * Merge configuration settings recursively
     * 
     * @param   array      $configs             configuration settings
     * @param   array      $changes              new configurations that will be merge with the $settings array
     * 
     * @return  array     return new merged array
     */
    private function mergeRecursive(array $configs, array $changes)
    {
        if (is_array($changes) && ! empty($changes)) {
            foreach ($changes as $key => $value) {
                if (isset($configs[$key])) {
                    if (is_array($configs[$key]) && is_array($value)) {
                        $this->setKey($configs, $key, $this->mergeRecursive($configs[$key], $value));
                    } else {
                        $this->setKey($configs, $key, $value);
                    }
                } else {
                    $this->setKey($configs, $key, $value);
                }
            }
        }
        return $configs;
    }
    /**
     * Add configurations to settings
     * 
     * @param   array      $configs              new configurations that will be merge with the environment settings
     * 
     * @return  bool     return true if configurations are added
     */
    private function add(array $configs)
    {
        if (is_array($configs) && ! empty($configs)) {
            $this->configs = $this->mergeRecursive($this->configs, $configs);
            return true;
        }
        return false;
    }
    /**
     * Get configurations from configuration files
     * within config folder
     */
    private function getConfig()
    {
        $this->add($this->loadConfig('app'));
        $this->add($this->loadConfig('permissions'));
        $this->add($this->loadConfig('database'));
        $this->add($this->loadConfig('paths'));
        $this->add($this->loadConfig('links'));
    }
    /**
     * Get configurations from environment files
     * within environment folder.
     * 
     * default.env.php is loaded by default.
     * "APP_ENVIRONMENT".env.php is loaded second if it exist.
     * 
     * Set environment by setting a global apache variable
     * "APP_ENVIRONMENT" to file name of environment file
     */
    private function getEnvironment()
    {
        $env = getenv('APP_ENVIRONMENT');
        $this->add($this->loadEnvironment('default'));
        $this->add($this->loadEnvironment($env));
    }
    /**
     * Load configurations from configuration file
     * 
     * @param   string        $name              name of configuration file with extension omit
     * 
     * @return  array        return load configurations
     */
    private function loadConfig(string $name)
    {
        if (! empty($name)) {
            $name = dirname(__DIR__) . '/../configs/' . $name . '.php';
            if (file_exists($name)) {
                $config = require($name);
                if (is_array($config)) {
                    return $config;
                }
            }
        }
        return [];
    }
    /**
     * Load configurations from environment file
     * 
     * @param   string        $name              name of configuration file with extension omit
     * 
     * @return  array        return load configurations
     */
    private function loadEnvironment(string $name)
    {
        if (! empty($name)) {
            $name = dirname(__DIR__) . '/../configs/environments/' . $name . '.env.php';
            if (file_exists($name)) {
                $config = require($name);
                if (is_array($config)) {
                    return $config;
                }
            }
        }
        return [];
    }
    /**
     * DNS
     * 
     * @return  string        return DNS
     */
    private function domain()
    {
        if (isset($_SERVER['HTTP_HOST'])) {
            $serverName = $_SERVER['HTTP_HOST'];
        } elseif (isset($_SERVER['SERVER_NAME'])) {
            $serverName = $_SERVER['SERVER_NAME'];
        }
        if (! empty($serverName)) {
            return ($_SERVER['REQUEST_SCHEME'] ?? 'http') . '://' . $serverName . '/';
        }
        return '';
    }
    /**
     * The type the client use to request resources
     * 
     * @return  string        return client type of requesting a client
     */
    private function clientType()
    {
        if(php_sapi_name() === 'cli'){
            if(isset($_SERVER['TERM'])){
                return 'cli';
            } else{
                return 'cronjob';
            }
        } else {
            if (isset($_SERVER['HTTP_X_REQUESTED_WITH'])) {
                if ($_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest') {
                    return 'api';
                } else {
                    http_response_code(406);
                    throw new Exception('Not Acceptable');
                    exit();
                }
            } else {
                return 'webserver';
            }
        }
    }
}