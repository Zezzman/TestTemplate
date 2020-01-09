<?php
namespace App\Factories;

use App\ViewData;
use App\Interfaces\IViewModel;
use App\Helpers\DataCleanerHelper;
use Exception;
/**
 * 
 */
class ViewFactory
{
    /**
     * 
     */
    public static function createView(string $name, IViewModel $model = null, array $bag = null)
    {
        return new ViewData($name, self::secureViewPath($name), $model, $bag);
    }
    /**
     * 
     */
    private static function secureViewPath(string $name)
    {
        $name = DataCleanerHelper::cleanValue($name);
        $path = config('CLOSURES.PATH')('APP') . "views/{$name}.php";
        if (file_exists($path)) {
            return $path;
        } else {
            $altPath = requireConfig('PATHS.ROOT') . "{$name}.php";
            if (file_exists($altPath)) {
                return $altPath;
            } else {
                throw new Exception('Missing View file : ' . $name);
                exit();
            }
        }
    }
}