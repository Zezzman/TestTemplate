<?php
namespace App\Controllers;

use App\Controller;
use App\ViewModels\ViewModel;
/**
 * 
 */
final class HomeController extends Controller
{
    public function Index()
    {
        return $this->view('home');
    }
    public function Document()
    {
        $viewModel = new ViewModel();
        return $this->view('document', $viewModel);
    }
}