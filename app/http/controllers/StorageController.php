<?php
namespace App\Controllers;

use App\Controller;
use App\Providers\FileProvider;
use App\Helpers\DataCleanerHelper;
use App\ViewModels\ViewModel;

/**
 * 
 */
final class StorageController extends Controller
{
    /**
     * Storage Index
     */
    public function Index($location, array $extensions = null)
    {
        $viewModel = new ViewModel();
        if (! empty($location)) {
            $dir = array_reduce($location, function($str, $item) { return $str . '/' . $item; });
            if (is_dir(config('PATHS.STORAGE') . $dir)) {
                $this->list($viewModel, $dir, $extensions);
            } else {
                $this->show($dir);
            }
        } else {
            $this->list($viewModel, '', $extensions);
        }
        $this->view('storage', $viewModel);
    }
    /**
     * List files within directories relative to storage
     */
    private function list(ViewModel $viewModel, string $directory, array $extensions = null)
    {
        $directory = trim($directory, '/');
        if (is_dir(config('PATHS.STORAGE') . $directory)) {
            $files = FileProvider::scan($directory, $extensions ?? [], true);
            $back = DataCleanerHelper::dataMap($directory, '/', function ($result, $item) { return $result . '/' . $item; }, -1);
            $viewModel->addMessage('<a href="'. config('LINKS.STORAGE') . $back . '" style="display:block">/back/</a>', 'links');

            foreach ($files as $file) {
                if (is_string($file)) {
                    $link = '<a href="' . config('LINKS.STORAGE') . trim($file, '/') . '/" style="display:block">' . $file . '/</a>';
                } else {
                    $link = '<a href="' . config('LINKS.STORAGE') . $directory . '/' . $file->name() . '" style="display:block">' . $file->name() . '</a>';
                }
                $viewModel->addMessage($link, 'links');
            }
        } else {
            self::respond(404);
        }
    }
    /**
     * Show file
     * 
     * Manage all links to storage files
     */
    private function show(string $path)
    {
        if (is_string($path) && ! empty($path)) {
            if (is_file(config('PATHS.STORAGE') . $path)) {
                $file = FileProvider::create($path);
                if (! $file->isValid()) {
                    self::respond(404);
                }
                if ($file->read() === false) {
                    self::respond(404);
                }
            } else {
                self::respond(404);
            }
        } else {
            self::respond(404);
        }
    }
}