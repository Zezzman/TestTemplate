<?php
namespace App\CLI;

use App\CLIController;
use App\ViewModels\ViewModel;
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