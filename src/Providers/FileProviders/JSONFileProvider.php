<?php
namespace App\Providers\FileProviders;

use App\Providers\FileProvider;
use App\Models\FileModel;
use App\Helpers\DataCleanerHelper;
use App\Helpers\HTTPHelper;
/**
 * File Manager
 * 
 * Upload/Download/Manage Client uploaded files
 * 
 * Allow paths are relative to storage directory
 */
class JSONFileProvider extends FileProvider
{
    const MAX_SIZE = 4000000;
    const EXTENSIONS = ['json'];
    const MIME = ['text/plain'];
}