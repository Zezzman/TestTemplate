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
            $link = config('LINKS.PUBLIC');
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
            $linkBase = rtrim($linkBase ?? config('LINKS.PUBLIC'), '/');
            $back = DataCleanerHelper::dataMap($url, '/',
            function ($result, $item) { return $result . '/' . $item; }, -1, 0);
            $html .= ('<a href="' . $linkBase . $back . '/" style="display:block">/back/</a>');
        }
        return $html;
    }
    /**
     * 
     */
    public static function folderFiles(string $dir, array $extensions = [], bool $includeFolders = true, string $linkBase = null)
    {
        $html = '';
        if (! empty($dir)) {
            $root = config('PATHS.ROOT');
            $linkBase = rtrim($linkBase ?? config('LINKS.PUBLIC'), '/');
            if (is_dir($root . $dir)) {
                $rootLength = count(explode('/', trim($root, '/')));
                $files = FileProvider::listFiles($root . $dir, $includeFolders);
                foreach ($files as $file) {
                    $html .= '<a href="' . $linkBase . DataCleanerHelper::dataMap($file, '/',
                    function ($result, $item) { return $result . '/' . $item; }, 0, $rootLength)
                    . '/" style="display:block">' . DataCleanerHelper::dataMap($file, '/',
                    function ($result, $item) { return $result . '/' . $item; }, 0, -1) . '/</a>';
                }
            }
        }
        return $html;
    }
}