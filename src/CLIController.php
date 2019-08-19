<?php
namespace App;

use App\Models\CLIRequestModel;
use App\Helpers\DataCleanerHelper;
use App\Interfaces\IRequest;
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
    public static function respond(int $code, $message = null, IRequest $request = null, Exception $exception = null)
    {
        ob_start();
        ob_clean();
        $responses = \App::Responses();

        if (isset($responses[$code])) {
            $response = $responses[$code];
            $body = [
                'response' => $code,
                'type' => $response,
            ];
            if (empty($message)) {
                $body['message'] = $response;
            } else if (is_string($message)) {
                $body['message'] = DataCleanerHelper::cleanValue($message ?? '');
            } else {
                $message = DataCleanerHelper::cleanArray((array) $message);
                $body['message'] = $message;
            }
            if (! is_null($exception) && config('DEBUG')) {
                $body['request'] = $request;
                $body['Exception'] = $exception;
                if ($body['message'] === '') {
                    $body['message'] = DataCleanerHelper::cleanValue($exception->getMessage());
                }
            }
            if (! empty($body['message'])) {
                echo config('App.NAME') . ":\n" . $body['message'] . "\n";
            }
        }
        exit();
    }
}