<?php
namespace App;

use App\Interfaces\IView;
use App\Interfaces\IController;
use App\Helpers\DataCleanerHelper;
/**
 * 
 */
class ViewData implements IView
{
    public $hasRendered = false;
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
        if (config('LAYOUT.DEFAULT') !== false) {
            $this->setLayout(config('LAYOUT.DEFAULT'));
        }
    }
    /**
     * 
     */
    public function setLayout(string $name = null)
    {
        if (is_null($name)) {
            $this->layout = null;
        } else {
            $name = trim($name, '/');
            $name = DataCleanerHelper::cleanValue($name);
            $path = "/{$name}.php";
    
            if (file_exists($path)) {
                $this->layout = $path;
            } else {
                $path = config('PATHS.EXPAND')('RESOURCES'). "layouts/{$name}.php";
                if (file_exists($path)) {
                    $this->layout = $path;
                } else {
                    throw new Exception('Layout File Not Found');
                }
            }
        }
    }
    /**
     * 
     */
    public function link()
    {
        if (! is_null($this->controller->getRequest())) {
            return config('LINKS.EXPAND')('PUBLIC') . $this->controller->getRequest()->uri;
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