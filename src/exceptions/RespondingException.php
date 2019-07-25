<?php
namespace App\Exceptions;

use Exception;
/**
 * Exception to send http respond codes with exceptions
 */
class RespondingException extends Exception
{
    private $respondCode = null;

    public function __construct(int $respondCode, string $message, int $code = 0, Exception $previous = null)
    {
        $this->respondCode = $respondCode;
        parent::__construct($message, $code, $previous);
    }
    
    public function respondCode()
    {
        return $this->respondCode;
    }
}