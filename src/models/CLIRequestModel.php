<?php
namespace App\Models;

use App\Models\RequestModel;
/**
 * 
 */
class CLIRequestModel extends RequestModel
{
    public $commands = [];
    public $flags = [];
    public $response = null;

    /**
     *  Initiate a request
     * 
     * @param   string  $uri        request query
     */
    public function __construct(array $commands = [], array $inputs = [], array $flags = [])
    {
        $this->commands = $commands;
        $this->params = $inputs;
        $this->flags = $flags;
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
        if (empty($this->commands)) {
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
        $this->response = $code;

        return $this;
    }
    /**
     * 
     */
    public function matchRequest(self $request)
    {
        if ($request->valid()) {
            foreach ($request->commands as $command) {
                if (! array_key_exists($command, $this->commands)) {
                    return false;
                } else {
                    $command = $this->commands[$command];
                    if (! empty($request->params)) {
                        foreach ($request->params as $key => $param) {
                            if (! isset($command['inputs']) 
                            || ! array_key_exists($key, $command['inputs'])) {
                                return false;
                            }
                        }
                    }
                    if (! empty($request->flags)) {
                        foreach ($request->flags as $flag) {
                            if (! isset($command['flags']) 
                            || ! array_key_exists($flag, $command['flags'])) {
                                return false;
                            }
                        }
                    }
                }
            }
            return true;
        } elseif ($request->commands == [] && $this->commands == []) {
            return true;
        }
        return false;
    }
}