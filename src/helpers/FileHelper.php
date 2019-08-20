<?php
namespace App\Helpers;

use App\Helper;
use App\Helper\QueryHelper;
/**
 * 
 */
final class FileHelper extends Helper
{
    /**
     * Loads content of file and scans for codes
     */
    public static function loadFile(string $path, array $codes = null, array $defaults = [])
    {
        if (file_exists($path) && is_file($path)) {
            if (! is_null($codes)) {
                $content = file_get_contents($path);
                return QueryHelper::scanCodes($codes, $content, $defaults);
            } else {
                return file_get_contents($path);
            }
        } else {
            return false;
        }
    }
    /**
     * Encodes and Print Image into img tag
     */
    public static function printImage(string $path, string $type = 'image/png', string $description = '', string $style = '<img src="data:{type};{base},{data}" alt="{description}">')
    {
        if (file_exists($path) && is_file($path)) {
            $file = [
                'path' => $path,
                'description' => DataCleanerHelper::cleanValue($description),
                'data' => base64_encode(file_get_contents($path)),
                'type' => $type,
                'base' => 'base64',
            ];
            return QueryHelper::insertCodes($file, $style);
        } else {
            return '';
        }
    }
    /**
     * Read File of type
     */
    public static function readFile(string $path, string $type, bool $setHeader = true)
    {
        if (file_exists($path) && is_file($path)) {
            ob_start();
            ob_clean();
            if ($setHeader) {
                header('Content-Type: ' . $type);
            }
            readfile($path);
            exit();
        } else {
            return false;
        }
    }
}