<?php
namespace App;

use App\ViewData;
use App\Interfaces\IController;
use App\Interfaces\IViewModel;
use App\Factories\ViewFactory;
use App\Helpers\DataCleanerHelper;
use Exception;
/**
 * View
 * 
 * Holds and manages the viewData, model and layout
 */
class View
{
    public $hasRendered = false;
    private $controller = null;
    private $viewData = [];
    private $layout = null;
    private $currentView = null;
    private $content = '';
    private $append = '';

    /**
     * 
     */
    private function __construct(IController $controller, string $name, IViewModel $model = null, array $bag = [])
    {
        $this->controller = $controller;
        $viewData = ViewFactory::createView($name, $model, $bag);
        if ($viewData->valid()) {
            $this->currentView = $viewData;
            $this->viewData[$name] = $viewData;
            if (config('LAYOUT.DEFAULT', false) !== false) {
                $this->layout(config('LAYOUT.DEFAULT'));
            }
        }
    }
    /**
     * 
     */
    public static function create(IController $controller, string $name, IViewModel $model = null, array $bag = [])
    {
        $view = new self($controller, $name, $model, $bag);

        if ($view->viewData($name)->valid()) {
            return $view;
        } else {
            return null;
        }
    }
    /**
     * 
     */
    public function viewData(string $name)
    {
        return $this->viewData[$name] ?? null;
    }
    /**
     * 
     */
    public function appendView(string $name, IViewModel $model = null, array $bag = [])
    {
        if (! isset($this->viewData[$name])) {
            $viewData = ViewFactory::createView($name, $model, $bag);
            if ($viewData->valid()) {
                $this->currentView = $viewData;
                $this->viewData[$name] = $viewData;
                return true;
            }
        }
        return false;
    }
    /**
     * 
     */
    public function append(string $content)
    {
        $this->append .= $content;
    }
    /**
     * 
     */
    public function layout(string $name = null)
    {
        $this->layout = ViewFactory::layoutPath($name);
    }
    private function header(string $name, array $bag = null)
    {
        return $this->loadFile(ViewFactory::headerPath($name), $bag);
    }
    private function footer(string $name, array $bag = null)
    {
        return $this->loadFile(ViewFactory::footerPath($name), $bag);
    }
    /**
     * 
     */
    private function section(string $name, array $bag = null)
    {
        return $this->loadFile(ViewFactory::sectionPath($name), $bag);
    }
    /**
     * 
     */
    private function loadFile(string $path, array $bag = null)
    {
        // file local pre-defined variables
        $controller = $this->controller ?? null;
        $layout = $this->layout ?? null;
        $viewData = $this->currentView ?? null;
        $model = $viewData->model ?? null;
        if (is_null($bag)) {
            if (! is_null($viewData)
            && ! is_null($viewData->bag)) {
                $bag = &$this->currentView->bag;
            } else {
                $bag = [];
            }
        }

        if (! empty($path)) {
            if (file_exists($path)) {
                return include($path);
            }
        }
        return false;
    }
    /**
     * 
     */
    public function hasRendered()
    {
        return $this->hasRendered;
    }
    /**
     * 
     */
    public function render()
    {
        if (! is_array($this->viewData)
        || empty($this->viewData)
        || $this->hasRendered == true) {
            return false;
        }

        $hasView = false;
        $body = '';
        // buffer view
        ob_start();
        foreach ($this->viewData as $view) {
            if ($this->loadFile($view->path)) {
                $body .= ob_get_clean();
                $hasView = true;
            } else {
                ob_clean();
            }
        }
        // buffer layout
        if (is_null($this->layout) || ! $hasView) {
            $this->hasRendered = true;
            // render view
            echo $body . $this->append;
            ob_flush();
        } else {
            // include body within layout
            $this->content = $body;
            $layout = $this->loadFile($this->layout ?? '');
            $content = ob_get_clean();
            if ($layout) {
                $this->hasRendered = true;
                // render view
                echo $content . $this->append;
            } else {
                ob_clean();
            }
        }
    }
}