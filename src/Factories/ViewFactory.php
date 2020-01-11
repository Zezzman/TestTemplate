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
        return new ViewData($name, self::viewPath($name), $model, $bag);
    }

    /**
     * 
     */
    public static function viewPath(string $name)
    {
        return self::securePath($name, 'views/');
    }
    /**
     * 
     */
    public static function layoutPath(string $name)
    {
        return self::securePath($name, config('PATHS.RESOURCES') . 'layouts/');
    }
    /**
     * 
     */
    public static function headerPath(string $name)
    {
        return self::securePath($name, config('PATHS.RESOURCES') . 'headers/');
    }
    /**
     * 
     */
    public static function footerPath(string $name)
    {
        return self::securePath($name, config('PATHS.RESOURCES') . 'footers/');
    }
    /**
     * 
     */
    public static function sectionPath(string $name)
    {
        return self::securePath($name, config('PATHS.RESOURCES') . 'sections/');
    }
    private static function securePath(string $name, $offsetFolder = null)
    {
        $name = DataCleanerHelper::cleanValue($name);

        $root = requireConfig('PATHS.ROOT');
        if (file_exists($path = $root . ($offsetFolder ?? '') . "{$name}.php"))
            return $path;
        if (file_exists($path = $root .  "{$name}.php"))
            return $path;
        
            
        $app = $root . requireConfig('PATHS.APP');
        if (file_exists($path = $app . ($offsetFolder ?? '') . "{$name}.php"))
            return $path;
        if (file_exists($path = $app . "{$name}.php"))
            return $path;
        

        throw new Exception('Missing file : ' . ($offsetFolder ?? '') . $name);
        exit();
    }
}