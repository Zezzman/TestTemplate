<?php
namespace App\Helpers;

use App\Helper;
/**
 * Helper for CURL commands
 */
final class CURLHelper extends Helper
{
    public static function json(string $url, array $options = [])
    {
        $defaults = array(
            CURLOPT_HEADER => 0,
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_HTTPHEADER => ['X-Requested-With: XMLHttpRequest'],
            CURLOPT_FORBID_REUSE => 1,
            CURLOPT_FRESH_CONNECT => 1,
            CURLOPT_TIMEOUT => 1,
            // CURLOPT_POSTFIELDS => http_build_query($post)
        );
        $curl = curl_init();
        curl_setopt_array($curl, ($defaults + $options));
        if( ! $result = curl_exec($curl))
        {
            trigger_error(curl_error($curl));
        }
        curl_close($curl);
        return (array) json_decode($result, true);
    }
}