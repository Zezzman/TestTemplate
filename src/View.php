<?php
namespace App;

use App\Interfaces\IController;
use App\Interfaces\IViewModel;
use App\Factories\ViewFactory;
/**
 * View
 * 
 * Holds and manages the viewData, model and layout
 */
class View
{
    private $viewData = null;

    /**
     * 
     */
    public function __construct(IController $controller, string $name, IViewModel $model = null, array $bag = [])
    {
        $service = new ViewFactory();
        $this->viewData = $service->createView($controller, $name, $model);
        $this->viewData->bag = $bag;
    }
    /**
     * 
     */
    public static function create(IController $controller, string $name, IViewModel $model = null, array $bag = [], bool $pauseRender = false)
    {
        $view = new self($controller, $name, $model);

        if ($view->viewData()->valid()) {
            if ($pauseRender === false) {
                $view->render();
            }
            return $view;
        } else {
            return null;
        }
    }
    /**
     * 
     */
    public function ViewData()
    {
        return $this->viewData;
    }
    /**
     * 
     */
    private function layout(string $layout = null)
    {
        $this->viewData->setLayout($layout);
    }
    private function header(string $name, array $bag = null)
    {
        $path = config('PATHS.RESOURCES') . "headers/{$name}.php";
        return $this->loadFile($path, $bag);
    }
    private function footer(string $name, array $bag = null)
    {
        $path = config('PATHS.RESOURCES') . "footers/{$name}.php";
        return $this->loadFile($path, $bag);
    }
    /**
     * 
     */
    private function section(string $name, array $bag = null)
    {
        $path = config('PATHS.RESOURCES') . "sections/{$name}.php";
        return $this->loadFile($path, $bag);
    }
    /**
     * 
     */
    private function loadFile(string $path, array $bag = null)
    {
        // file local pre-defined variables
        $viewData = $this->viewData ?? null;
        $layout = $this->viewData->layout ?? null;
        $model = $this->viewData->model ?? null;
        if (is_null($bag)) {
            if (! is_null($viewData)
            && ! is_null($viewData->bag)) {
                $bag = &$this->viewData->bag;
            } else {
                $bag = [];
            }
        }

        if (! empty($path) && file_exists($path)) {
            return include($path);
        } else {
            return false;
        }
    }
    /**
     * 
     */
    private function body()
    {
        return $this->viewData->body ?? '';
    }
    /**
     * 
     */
    public function rendered()
    {
        return $this->viewData->rendered;
    }
    /**
     * 
     */
    public function render()
    {
        if (is_null($this->viewData)
        || ! $this->viewData->valid()
        || $this->viewData->rendered == true) {
            return false;
        }

        // render viewData
        ob_start();
        $view = $this->loadFile($this->viewData->path ?? '');
        $body = ob_get_clean();
        if ($view !== false) {
            // render layout
            if (! is_null($this->viewData->layout)) {
                // include body within layout
                $this->viewData->body = $body;
                $layout = $this->loadFile($this->viewData->layout ?? '', []);
                $content = ob_get_clean();
                if ($layout) {
                    $this->viewData->rendered = true;
                    // print view
                    echo $content . $this->viewData->append;
                } else {
                    ob_clean();
                }
            } else {
                $this->viewData->rendered = true;
                // print view
                echo $body;
                ob_flush();
            }
        }
        ob_start();
        ob_clean();
    }
}