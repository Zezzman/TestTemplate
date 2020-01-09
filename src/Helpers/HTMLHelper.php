<?php
namespace App\Helpers;

use App\Helper;
use App\Helpers\QueryHelper;
use App\Helpers\DataCleanerHelper;
use App\Providers\FileProvider;
/**
 * Helper for HTML
 */
final class HTMLHelper extends Helper
{
    /**
     * URI separated into breadcrumb links
     */
    public static function breadcrumbs(string $uri, string $separator = '/', string $style = '<a href="{link}" style="">/{name}</a>')
    {
        $html = '';
        if (! empty($uri)) {
            $sections = explode($separator, $uri);
            $link = config('CLOSURES.LINK')('PUBLIC');
            foreach ($sections as $section) {
                if (! empty($section)) {
                    $link .= $section . $separator;
                    $html .= QueryHelper::scanCodes(['name' => $section, 'link' => $link], $style);
                }
            }
        }
        return $html;
    }
    /**
     * 
     */
    public static function backLink(string $url, string $linkBase = null)
    {
        $html = '';
        if (! empty($url)) {
            $linkBase = rtrim($linkBase ?? config('CLOSURES.LINK')('PUBLIC'), '/');
            $back = DataCleanerHelper::dataMap($url, '/',
            function ($result, $item) { return $result . '/' . $item; }, -1, 0);
            $html .= ('<a href="' . $linkBase . $back . '/" style="display:block">/back/</a>');
        }
        return $html;
    }
    /**
     * 
     */
    public static function folderFiles(string $dir, array $extensions = [], bool $includeFolders = false, string $linkBase = null)
    {
        $html = '';
        if (! empty($dir)) {
            $linkBase = rtrim($linkBase ?? config('CLOSURES.LINK')('PUBLIC'), '/');
            $files = FileProvider::listFiles($dir, $includeFolders);
            foreach ($files as $file) {
                $html .= '<a href="' . $linkBase . '/' . DataCleanerHelper::dataMap($file, '/')
                . '" style="display:block">' . DataCleanerHelper::dataMap($file, '/', null, 0, -1) . '</a>';
            }
        }
        return $html;
    }
}