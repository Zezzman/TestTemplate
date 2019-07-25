<?php
namespace App\Helpers;

use App\Helper;
/**
 * Helper for cleaning data
 */
final class DataCleanerHelper extends Helper
{
    /**
     * Clean string
     * 
     * @param	string	$data		unsafe string.
     * 
     * @return	string	clean string
     */
    public static function cleanValue($data)
    {
        $cleanData = '';
        if (is_string($data) || is_numeric($data)) {
            $cleanData = trim($data);
            $cleanData = trim($cleanData, '/');

            $cleanData = strip_tags($cleanData);
            if (get_magic_quotes_gpc()) {
                $cleanData = stripslashes($cleanData);
            }
            $cleanData = htmlspecialchars($cleanData);
        }
        return $cleanData;
    }
    /**
     * Clean array of strings
     * 
     * @param	array	$data		unsafe array of strings.
     * 
     * @return	string	clean array
     */
    public static function cleanArray(array $data)
    {
        return $data;
    }
    /**
     * Clean string
     * 
     * @param	string	$email		unsafe string.
     * 
     * @return	string	return clean string
     */
    public static function cleanEmail($email)
    {
        $tags = [
            'content-type',
            'bcc:',
            'to:',
            'cc:',
            'href',
            'src='
        ];
        $cleanData = '';
        if(!is_array($email) && !is_object($email)){
            $cleanData = trim($email);
            $cleanData = trim($cleanData, '/');
            
            $cleanData = str_replace($tags, '', $cleanData);
            $cleanData = htmlspecialchars($cleanData);
        }
        return $cleanData;
    }
}