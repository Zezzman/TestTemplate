<?php
namespace App\Helpers;

use App\Helper;
use App\Helpers\QueryHelper;
/**
 * Helper for HTML
 */
final class HTMLHelper extends Helper
{
    /**
     * URI separated into breadcrumb links
     */
    public static function Breadcrumbs(string $uri, string $separator = '/', string $style = '<a href="{link}" style="">/{name}</a>')
    {
        if (! empty($uri)) {
            $sections = explode($separator, $uri);
            $link = config('LINKS.PUBLIC');
            $html = '';
            foreach ($sections as $section) {
                if (! empty($section)) {
                    $link .= $section . $separator;
                    $html .= QueryHelper::scanCodes(['name' => $section, 'link' => $link], $style);
                }
            }
            return $html;
        }
    }
}