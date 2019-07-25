<?php
namespace App\Providers;

use App\Helpers\DataCleanerHelper;
use App\Helpers\HTTPHelper;
use App\Traits\Feedback;
/**
 * File Manager
 * 
 * Upload/Download/Manage Client uploaded files
 */
final class FileProvider
{
    use Feedback;
    
    const MAX_SIZE = 4000000;
    const EXTENSIONS = ['jpeg','jpg','png', 'pdf'];
    const TYPES = ['image/jpeg', 'image/jpg', 'image/png', 'application/pdf'];
    
    protected $allowUpload = true;
    protected $path = null;
    protected $info = [];

    public function __construct(string $path)
    {
        if (empty($path)) {
            return;
        }
        $this->path = DataCleanerHelper::cleanValue($path);
        if (!$this->Valid()) {
            $this->path = '';
        } else {
            $this->info = pathinfo($this->FullPath());
        }
    }
    /**
     * Creates file instance
     * 
     * @param   string      $file_name              file path
     * 
     * @return  self    file instance
     */
    public static function create(string $path)
    {
        $file = new self($path);
        return $file;
    }
    /**
     * Check if file exist
     * 
     * @return  bool        true if file exist
     */
    public function isValid()
    {
        return self::checkFile($this->fullPath());
    }
    /**
     * File extension
     * 
     * @return  string      file extension
     */
    public function extension()
    {
        if ($this->info && isset($this->info['extension'])) {
            return $this->info['extension'];
        } else {
            return false;
        }
    }
    /**
     * File name
     * 
     * @return  string      file name
     */
    public function name()
    {
        if ($this->info && isset($this->info['basename'])) {
            return $this->info['basename'];
        } else {
            return '';
        }
    }
    /**
     * File path
     * 
     * File path from relative to storage folder
     * 
     * @return  string      file path
     */
    public function path()
    {
        return $this->path;
    }
    /**
     * Full file path
     * 
     * @return  string      file path
     */
    public function fullPath()
    {
        if (! empty($this->path)) {
            return config('PATHS.STORAGE') . $this->path;
        }
        return '';
    }
    /**
     * Link to file
     * 
     * @return  string      url of the file
     */
    public function link()
    {
        if (empty($this->Path())) {
            return '';
        }
        return config('LINKS.STORAGE') . 'uploads/' . $this->Path();
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
        if (! self::$allowUpload || ! HTTPHelper::isFile($fileIndex) || ($_FILES[$fileIndex]['error'] !== 0)) {
            return false;
        }

        // Get file
        $file = self::create($_FILES[$fileIndex]['name'] ?? '');
        $name = $file->name();
        $ext = $file->extension();

        // check if upload directory is a directory and is writable
        if (! is_dir(config('PATHS.STORAGE'))) {
            $file->feedback('Upload directory does not exist', 1, 'FileFolder');
            return $file;
        }
        if (! is_writable(config('PATHS.STORAGE'))) {
            $file->feedback('Upload directory is not writable', 1, 'FileFolder');
            return $file;
        }
        
        // check if folder is valid
        if ($folder) {
            $folder = str_replace('/', '', $folder);
            $folder = DataCleanerHelper::cleanValue($folder);
            if (file_exists(config('PATHS.STORAGE') . $folder) && is_dir(config('PATHS.STORAGE') . $folder)) {
                $folder = $folder . '/';
            } else {
                $file->feedback('Folder does not exist ' . config('PATHS.STORAGE') . $folder, 1, 'FileFolder');
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

        if ( !$overwrite && self::checkFile(config('PATHS.STORAGE') . $file_name)) {
            $file->feedback('File name already exist', 0, 'FileName');
        }
        if ($file->hasFeedbackWithType(1)) {
            $file->feedback('Something went wrong with the upload', 0, 'FileUpload');
        }
        if ($file->hasFeedback()) {
            return $file;
        }

        if (move_uploaded_file($tmp_name, config('PATHS.STORAGE') . $file_name)) {
            $file = new self($file_name);
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
    protected static function checkExtension(string $ext, array $allowExtensions = [])
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
    protected static function checkType(string $type)
    {
        if (in_array($type, self::TYPES)) {
            return true;
        }
        return false;
    }
    /**
     * Check if file exist in storage folder
     * 
     * @param   string      $file                       file path
     * 
     * @return  bool      true if file exist
     */
    protected static function checkFile(string $file)
    {
        if (empty($file)) {
            return false;
        }
        if (file_exists($file) && is_file($file)) {
            return true;
        }
        return false;
    }
}