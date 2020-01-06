<?php
namespace App;

use App\Interfaces\IController;
use App\Interfaces\IRequest;
use App\Interfaces\IViewModel;
use App\Providers\SessionProvider;
use App\Helpers\HTTPHelper;
use App\Helpers\DataCleanerHelper;
use App\Exceptions\RespondingException;
use App\Models\HttpRequestModel;
use App\ViewModels\ViewModel;
use App\ViewModels\ExceptionViewModel;
use App\View;
use Exception;
/**
 * Base class for web controller classes
 * 
 * Controller class is used to inherit the base controller features.
 * 
 * @author  Francois Le Roux <francoisleroux97@gmail.com>
 */
class Controller implements IController
{
    private $request = null;
    private $exception = null;
    protected $view = null;
    protected $pauseRender = false;
    
    /**
     * Setup Controller request
     * 
     * @param   IRequest        $request    request sent from client for resource access
     */
    public function __construct(IRequest $request = null)
    {
        $this->request = $request;
    }
    public function getRequest()
    {
        return $this->request ?? HttpRequestModel::empty();
    }
    /**
     * Create view
     * 
     * @param   string          $name       view name (file_name with extension omit)
     * @param   IViewModel      $model      model for view that holds information from controller to view
     * 
     * @return  View    return new created view
     */
    public function view(string $name = '', IViewModel $model = null, array $bag = [])
    {
        if (is_null($this->view)) {
            if (! empty($name)) {
                try {
                    $this->view = View::create($this, $name, $model, $bag, $this->pauseRender);
                } catch (RespondingException $e) {
                    $this->error($e->respondCode(), $e);
                } catch (PDOException $e) {
                    $this->error(503, $e);
                } catch (Exception $e) {
                    $this->error(500, $e);
                }
            }
        }

        return $this->view;
    }
    /**
     * Create Error View
     * 
     * @param   int             $code           header response code
     * @param   Exception       $exception      exceptions caught from try and catch
     */
    public function error(int $code, Exception $exception = null)
    {
        self::respond($code, '', $this->request, $exception);
    }
    /**
     * Continue View rendering
     */
    public function continueRender()
    {
        $this->pauseRender = false;
        $this->render();
    }
    /**
     * Pause View rendering
     */
    public function pauseRender()
    {
        $this->pauseRender = true;
    }
    /**
     * Redirect view
     * 
     * @param   string          $uri        view to redirect to
     */
    public function redirect(string $uri, int $responseCode = null)
    {
        // redirect to new $uri || create view here
        HTTPHelper::redirect($uri, null, $responseCode);
    }

    /**
     * 
     */
    public static function respond(int $code, string $message = null, IRequest $request = null, Exception $exception = null)
    {
        ob_start();
        ob_clean();
        $responses = \Launcher::Responses();

        if (! is_null($request)) {
            $redirect = $request->redirect;
            if (! is_null($redirect)) {
                SessionProvider::set('refererURI', $request->uri);
                $responseCode = http_response_code();
                SessionProvider::set('refererCode', $responseCode);
                HTTPHelper::redirect($redirect, $request->params, ($responseCode !== 200 ? $responseCode : null));
            }
        }
        if (isset($responses[$code])) {
            $response = $responses[$code];
            http_response_code($code);

            $respond = new static();
            $respond->request = $request;
            $respond->exception = $exception;
            
            $viewModel = new ExceptionViewModel();
            $viewModel->responseTitle = $response;
            $viewModel->responseCode = $code;
            $viewModel->exception = $exception;
            if (! empty($message)) {
                $viewModel->AddMessage(DataCleanerHelper::cleanValue($message));
            }

            $name = config('PATHS.RESOURCES') . 'responses/' . $code;
            try {
                if (is_file($name . '.php')) {
                    $respond->view = View::create($respond, $name, $viewModel);
                } else {
                    $respond->view = View::create($respond, config('PATHS.RESOURCES') . 'responses/index', $viewModel);
                }
            } catch (Exception $e) {
                if (config('DEBUG')) {
                    echo "({$name}) : ". $e->getMessage();
                } else {
                    echo 'Something went wrong';
                }
                exit();
            }
        }
        exit();
    }
}