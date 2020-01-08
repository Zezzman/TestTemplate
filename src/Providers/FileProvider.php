<?php
namespace App\Providers;

use App\Helpers\DataCleanerHelper;
use App\Helpers\HTTPHelper;
use App\Models\FileModel;
/**
 * File Manager
 * 
 * Upload/Download/Manage Client uploaded files
 * 
 * Allow paths are relative to storage directory
 */
final class FileProvider
{
    const MAX_SIZE = 4000000;
    const EXTENSIONS = ['jpeg','jpg','png', 'pdf'];
    const TYPES = ['image/jpeg', 'image/jpg', 'image/png', 'application/pdf'];
    
    private function __construct(){}

    /**
     * Creates file instance
     * 
     * @param   string      $path              file path
     * 
     * @return  self    file instance
     */
    public static function create(string $path)
    {
        if (empty($path)) {
            return;
        }
        $path = DataCleanerHelper::cleanValue($path);
        $root = config('PATHS.ROOT');
        if (self::checkFile($root . $path)) {
            return new FileModel($path, pathinfo($root . $path));
        }
    }
    /**
     * Creates file instance that is within storage
     * 
     * @param   string      $path              file path
     * 
     * @return  self    file instance
     */
    public static function storageFile(string $path)
    {
        if (empty($path)) {
            return;
        }
        $storage = substr(config('PATHS.STORAGE'), strlen(config('PATHS.ROOT')));
        return self::create($storage . $path);
    }
    /**
     * Scan folder for files
     * 
     * @param   string      $dir                        directory of files
     * @param   array       $allowExtensions            all allowed extensions
     * 
     * @return  array    An array of file instances
     */
    public static function scan(string $dir, array $allowExtensions = [])
    {
        $files = [];
        $root = config('PATHS.ROOT');
        $dir = trim($dir, '/') . DIRECTORY_SEPARATOR;
        if (is_dir($root . $dir)) {
            $names = scandir($root . $dir);
            foreach ($names as $name) {
                if (! preg_match('/(^|\s)[\.]/', $name)) {
                    if (is_file($root . $dir . $name)) {
                        $file = self::create($dir . $name);
                        if ($file->isValid()) {
                            if (self::checkExtension($file->extension(), $allowExtensions)) {
                                $files[] = $file;
                            }
                        }
                    }
                }
            }
        }
        
        return $files;
    }
    /**
     * Scan folders for files
     * 
     * @param   array       $folders                    directories of files
     * @param   array       $allowExtensions            all allowed extensions
     * 
     * @return  array    An array of file instances for each folder
     */
    public static function scanFolders(array $folders, array $allowExtensions = [])
    {
        $files = [];
        $root = config('PATHS.ROOT');
        foreach ($folders as $folder) {
            $folder = trim($folder, '/') . DIRECTORY_SEPARATOR;
            $dir = $root . $folder;
            if (is_dir($dir)) {
                $files[$folder] = [];
                $names = scandir($dir);
                foreach ($names as $name) {
                    if (! preg_match('/(^|\s)[\.]/', $name)) {
                        if (is_file($dir . $name)) {
                            $file = self::create($folder . $name);
                            if ($file->isValid()) {
                                if (self::checkExtension($file->extension(), $allowExtensions)) {
                                    $files[$folder][] = $file;
                                }
                            }
                        }
                    }
                }
            }
        }
        return $files;
    }
    /**
     * List file within directory
     * 
     * @param   string      $dir                        directory of files
     * @param   bool        $includeFolders             include folder names
     * 
     * @return  array    An array of file names
     */
    public static function listFiles(string $dir, bool $includeFolders = false)
    {
        $files = [];
        $dir = trim($dir, '/');
        $path = config('PATHS.ROOT') . $dir . DIRECTORY_SEPARATOR;
        if (! empty($dir) && is_dir($path)) {
            $names = scandir($path);
            foreach ($names as $name) {
                if (! preg_match('/(^|\s)[\.]/', $name)) {
                    if ($includeFolders
                    || (is_file($path . $name)
                    && file_exists($path . $name))) {
                        $files[] = DIRECTORY_SEPARATOR . $dir . DIRECTORY_SEPARATOR . $name;
                    }
                }
            }
        }
        return $files;
    }
    /**
     * Uploads File
     * 
     * Manage uploads to storage folder through form file post uploading
     * 
     * @param   string      $fileIndex                  name used within $_FILES when uploading file using form
     * @param   string      $folder                     folder within storage
     * @param   bool        $overwrite                  overwrite file that has similar name
     * @param   string      $customName                 if customName is not null, this custom name will be used but if it is a empty string it will generate a custom name
     * @param   array       $allowExtensions            all allowed extensions
     * @param   int         $maxSize                    max file size
     * 
     * @return  self        newly created file instance
     */
    public static function upload($fileIndex, string $folder = '', $overwrite = false, string $customName = null, array $allowExtensions = [], $maxSize = 0)
    {
        if (! config('PERMISSIONS.ALLOW_UPLOADS') || ! HTTPHelper::isFile($fileIndex) || ($_FILES[$fileIndex]['error'] !== 0)) {
            return false;
        }
        $storagePath = config('PATHS.STORAGE');

        // Get file
        $file = self::create($_FILES[$fileIndex]['name'] ?? '');
        $name = $file->name();
        $ext = $file->extension();

        // check if upload directory is a directory and is writable
        if (! is_dir($storagePath)) {
            $file->feedback('Upload directory does not exist', 1, 'FileFolder');
            return $file;
        }
        if (! is_writable($storagePath)) {
            $file->feedback('Upload directory is not writable', 1, 'FileFolder');
            return $file;
        }
        
        // check if folder is valid
        if ($folder) {
            $folder = str_replace('/', '', $folder);
            $folder = DataCleanerHelper::cleanValue($folder);
            if (file_exists($storagePath . $folder) && is_dir($storagePath . $folder)) {
                $folder = $folder . '/';
            } else {
                $file->feedback('Folder does not exist ' . $storagePath . $folder, 1, 'FileFolder');
                return $file;
            }
        } else {
            $folder = '';
        }

        $errors = [];
        $type = $_FILES[$fileIndex]['type'];
        $size = $_FILES[$fileIndex]['size'];
        $tmp_name = $_FILES[$fileIndex]['tmp_name'];

        // Check file extension
        if (! self::checkExtension($ext, $allowExtensions)) {
            $file->feedback('File extension not allowed', 0, 'FileExtension');
        }

        // Check file type
        if (! self::checkType($type)) {
            $file->feedback('File type not allowed', 0, 'FileType');
        }

        // Check file size
        if (! empty($maxSize)) {
            if ($size > $maxSize) {
                $file->feedback('File size too large', 0, 'FileSize');
            }
        } elseif ($size > self::MAX_SIZE) {
            $file->feedback('File size too large', 0, 'FileSize');
        }

        // Create a new file name
        if (! is_null($customName)) {
            if (is_string($customName) && ! empty($customName)) {
                $customName = str_replace('/', '', $customName);
                $customName = str_replace('.', '', $customName);
                $customName = str_replace(',', '', $customName);
                $customName = str_replace(' ', '', $customName);
                $customName = DataCleanerHelper::cleanValue($customName);
                if (empty ($customName)) {
                    $file->feedback('Empty file name created', 1, 'FileName');
                }
                $file_name = $folder . $customName . '.' . $ext;
            } else {
                $tmp = str_replace(array('.',' '), array('',''), microtime());
                if (empty ($tmp)) {
                    $file->feedback('Empty file name created', 1, 'FileName');
                }
                $file_name = $folder . $tmp . '.' . $ext;
            }
        } else {
            $name = str_replace(' ', '_', DataCleanerHelper::cleanValue($name));
            $file_name = $folder . $name . '.' . $ext;
        }

        if ( !$overwrite && self::checkFile($storagePath . $file_name)) {
            $file->feedback('File name already exist', 0, 'FileName');
        }
        if ($file->hasFeedbackWithType(1)) {
            $file->feedback('Something went wrong with the upload', 0, 'FileUpload');
        }
        if ($file->hasFeedback()) {
            return $file;
        }

        if (move_uploaded_file($tmp_name, $storagePath . $file_name)) {
            $file = self::storageFile($file_name);
            if ($file->isValid()) {
                return $file;
            } else {
                $file->feedback('Could not find file', 0, 'FileUpload');
                return $file;
            }
        } else {
            $file->feedback('Failed to create file', 0, 'FileUpload');
        }
        return $file;
    }
    /**
     * Check if file extension is allowed
     * 
     * @param   string      $ext                        file extension
     * @param   array       $allowExtensions            all allowed extensions
     * 
     * @return  bool      true if extension is allowed
     */
    public static function checkExtension(string $ext, array $allowExtensions = [])
    {
        if (! empty($allowExtensions)) {
            if (in_array($ext, $allowExtensions)) {
                return true;
            } else {
                return false;
            }
        } elseif (in_array($ext, self::EXTENSIONS)) {
            return true;
        }
        return false;
    }
    /**
     * Check if file type is allowed
     * 
     * @param   string      $type                       file type
     * 
     * @return  bool      true if type is allowed
     */
    public static function checkType(string $type)
    {
        if (in_array($type, self::TYPES)) {
            return true;
        }
        return false;
    }
    /**
     * Check if file path exist
     * 
     * @param   string      $path               file path
     * 
     * @return  bool      true if file exist
     */
    public static function checkFile(string $path)
    {
        if (empty($path)) {
            return false;
        }
        if (file_exists($path) && is_file($path)) {
            return true;
        }
        return false;
    }
}