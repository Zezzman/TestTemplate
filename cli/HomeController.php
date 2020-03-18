<?php
namespace App\CLI;

use System\CLIController;
use System\ViewModels\ViewModel;
/**
 * 
 */
final class HomeController extends CLIController
{
    public function Index()
    {
        CLIController::respond(200, 'cli access point');
    }
}