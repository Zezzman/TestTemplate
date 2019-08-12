<?php
namespace App;

use App\Interfaces\IView;
use App\Interfaces\IController;
/**
 * 
 */
class ViewData implements IView
{
    public $rendered = false;
    public $name = null;
    public $path = null;
    public $layout = null;
    public $model = null;
    public $controller = null;
    public $bag = [];
    public $append = '';
    public $body = '';

    /**
     * 
     */
    public function __construct(string $name, IController $controller)
    {
        $this->name = $name;
        $this->controller = $controller;
    }
    /**
     * 
     */
    public function setLayout(string $name)
    {
        $path = config('PATHS.RESOURCES') . "layouts/{$name}.php";
        if (file_exists($path) && is_file($path)) {
            $this->layout = $path;
        }
    }
    /**
     * 
     */
    public function link()
    {
        if (! is_null($this->controller->getRequest())) {
            return config('LINKS.PUBLIC') . $this->controller->getRequest()->uri;
        }
        return '';
    }
    /**
     * 
     */
    public function valid()
    {
        if (! is_null($this->name) && file_exists($this->path)) {
            return true;
        } else {
            return false;
        }
    }
}