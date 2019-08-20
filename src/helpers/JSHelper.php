<?php
namespace App\Helpers;

use App\Helper;
use App\Helpers\QueryHelper;
use App\Helpers\FileHelper;
/**
 * 
 */
final class JSHelper extends Helper
{
    public static function varPrint(array $variables)
    {
        $style = 'var {KEY} = {VALUE};';
        $code = QueryHelper::insertCodes($variables, $style, true);
        return self::loadScripts([
            ['code' => $code]
        ]);
    }
    public static function loadScripts(array $scripts)
    {
        $html = '';
        $style = '<script {src} {async} {defer} {type} {charset}>{code}</script>';

        foreach ($scripts as $script) {
            if (is_array($script)) {
                // Required fields
                $script['async'] = (in_array('async', $script)) ? 'async' : '';
                $script['defer'] = (in_array('defer', $script)) ? 'defer' : '';
                $script['charset'] = $script['charset'] ?? '';
                $script['type'] = $script['type'] ?? '';
                $script['code'] = $script['code'] ?? '';
                $script['src'] = $script['src'] ?? '';
                // Format fields
                if ($script['src'] !== '') {
                    $script['src'] = 'src="' . $script['src'] . '"';
                }
                if ($script['type'] !== '') {
                    $script['type'] = 'type="' . $script['type'] . '"';
                }
                if ($script['charset'] !== '') {
                    $script['charset'] = 'charset="' . $script['charset'] . '"';
                }
                if (isset($script['path']) && ! empty($script['path'])) {
                    $file = FileHelper::loadFile($script['path']);
                    if (! empty($file)) {
                        $script['code'] .= $file;
                    }
                }
                // Create html
                $html .= QueryHelper::scanCodes($script, $style);
            }
        }
        
        return $html;
    }
}