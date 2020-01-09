<?php
namespace App;

use App\Interfaces\IView;
use App\Interfaces\IViewModel;
use App\Helpers\DataCleanerHelper;
/**
 * 
 */
class ViewData implements IView
{
    public $name = null;
    public $path = null;
    public $model = null;
    public $bag = [];

    /**
     * 
     */
    public function __construct(string $name, string $path, IViewModel $model = null, array $bag = [])
    {
        $this->name = $name;
        $this->path = $path;
        $this->model = $model;
        $this->bag = $bag;
    }
    /**
     * 
     */
    public function link()
    {
        if (! is_null($this->controller->getRequest())) {
            return config('CLOSURES.LINK')('PUBLIC') . $this->controller->getRequest()->uri;
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