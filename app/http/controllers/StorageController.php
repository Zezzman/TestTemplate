<?php
namespace App\Controllers;

use App\Controller;
use App\Helpers\FileHelper;
use App\Helpers\DataCleanerHelper;
/**
 * 
 */
final class StorageController extends Controller
{
    /**
     * Upload files management
     * 
     * Manage all links to uploads
     */
    public function Uploads($name)
    {
        if (is_string($name) && ! empty($name)) {
            $path = config('PATHS.STORAGE') . 'uploads/' . DataCleanerHelper::cleanValue(trim($name, '/'));
            if (FileHelper::readFile($path, 'image/png') === false) {
                self::respond(404);
            }
        }
    }
}