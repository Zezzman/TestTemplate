<?php
namespace App\Controllers;

use App\Controller;
use App\Providers\FileProvider;
use App\Helpers\DataCleanerHelper;
/**
 * 
 */
final class StorageController extends Controller
{
    /**
     * List Background Pictures
     */
    public function ListUploads(array $extensions = null)
    {
        $path =  'uploads/';
        $path =  'pictures/';
        if (is_dir(config('PATHS.STORAGE') . $path)) {
            $files = FileProvider::scan($path, $extensions ?? []);
            foreach ($files as $file) {
                echo '<a href="' . config('LINKS.STORAGE') . 'uploads/' . $file->name() . '" style="display:block">' . $file->name() . '</a>';
            }
        } else {
            self::respond(404);
        }
    }
    /**
     * Upload files management
     * 
     * Manage all links to uploads
     */
    public function Uploads($name)
    {
        if (is_string($name) && ! empty($name)) {
            $path =  'uploads/';
            $path =  'pictures/';

            if (is_dir(config('PATHS.STORAGE') . $path)) {
                $file = FileProvider::create($path . $name);
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