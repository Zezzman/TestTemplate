<?php
namespace App;

use App\Models\CLIRequestModel;
use App\Helpers\DataCleanerHelper;
use Exception;
/**
 * Base class for cli controller classes
 * 
 * Api class is used to inherit the base api controller features.
 * 
 * @author  Francois Le Roux <francoisleroux97@gmail.com>
 */
class CLIController
{
    protected $request = null;

    public function getRequest()
    {
        return $this->request ?? CLIRequestModel::empty();
    }

    /**
     * Setup Api request and input
     * 
     * Store request and input from php://input into their respected variables
     * 
     * @param   CLIRequestModel    $request        request sent from client for resource access
     */
    public function __construct(CLIRequestModel $request = null)
    {
        $this->request = $request;
    }
    /**
     * 
     */
    public function respond(int $code, string $message = null, Exception $exception = null)
    {
        ob_start();
        ob_clean();
        $responses = \App::Responses();

        if (isset($responses[$code])) {
            $response = $responses[$code];
            http_response_code($code);
            
            $body = [
                'response' => $code,
                'type' => $response,
                'message' => DataCleanerHelper::cleanValue($message ?? '')
            ];
            if (! is_null($exception) && config('DEBUG')) {
                $body['request'] = $this->request;
                $body['Exception'] = $exception;
                if ($body['message'] === '') {
                    $body['message'] = DataCleanerHelper::cleanValue($exception->getMessage());
                }
            }
            echo json_encode($body);
        }

        exit();
    }
    public static function respondView(int $code, string $message = null, HttpRequestModel $request = null, Exception $exception = null)
    {
        $controller = new self($request);
        $controller->respond($code, $message, $exception);
    }
}