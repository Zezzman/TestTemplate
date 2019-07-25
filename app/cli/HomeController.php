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
        echo config('App.NAME') . " cli access point\n";
    }
}