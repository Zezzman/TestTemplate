<?php
namespace App\Factories;

use App\Interfaces\IRequest;
use App\Interfaces\IViewModel;
use App\Interfaces\IView;
use App\Interfaces\IController;
use App\Helpers\DataCleanerHelper;
use App\ViewData;
/**
 * 
 */
class ViewFactory
{
    /**
     * 
     */
    public function createView(IController $controller, string $name, IViewModel $model = null)
    {
        $view = new ViewData($name, $controller);
        $view->path = $this->secureViewPath($name);
        if (! is_null($model)) {
            $this->setModel($view, $model);
        }
        return $view;
    }
    /**
     * 
     */
    private function setModel(IView $view, IViewModel $model)
    {
        if (is_null($view->model)) {
            $view->model = $model;
        }
    }
    /**
     * 
     */
    private function secureViewPath(string $name)
    {
        $name = trim($name, '/');
        $name = DataCleanerHelper::cleanValue($name);
        $path = "/{$name}.php";

        if (file_exists($path)) {
            return $path;
        } else {
            $path = config('PATHS.APP') . "{$name}.php";
            if (file_exists($path)) {
                return $path;
            } else {
                throw new \Exception('Missing View file : ' . $path);
                exit();
            }
        }
    }
}