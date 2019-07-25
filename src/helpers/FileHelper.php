<?php
namespace App\Helpers;

use App\Helper;
/**
 * 
 */
final class FileHelper extends Helper
{
    /**
     * 
     */
    public static function requireFile(string $path)
    {
        if (file_exists($path) && is_file($path)) {
            return require_once($path);
        } else {
            throw new Exception("File not found: ({$path})");
        }
    }
    /**
     * 
     */
    public static function loadFile(string $path)
    {
        if (file_exists($path) && is_file($path)) {
            return include($path);
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
    public static function readFile(string $path, string $type)
    {
        if (file_exists($path) && is_file($path)) {
            ob_start();
            ob_clean();
            header('Content-Type: ' . $type);
            readfile($path);
            exit();
        } else {
            return false;
        }
    }
}