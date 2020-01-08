<?php
namespace App\ViewModels;

use App\ViewModels\ViewModel;
/**
 * 
 */
class ExceptionViewModel extends ViewModel
{
    public $responseTitle = null;
    public $responseCode = null;
    public $exception = null;

    public function Exception()
    {
        return (config('DEBUG') && ! is_null($this->exception)) ? var_dump($this->exception) : '';
    }
}