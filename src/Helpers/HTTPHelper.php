<?php
namespace App\Helpers;

use App\Helper;
/**
 * 
 */
final class HTTPHelper extends Helper
{
    /**
     * 
     */
    public static function isGET(string $param = null)
    {
        if (is_array($_GET) && ! empty($_GET)) {
            if (! is_null($param)) {
                if (((is_numeric($param) && $param >= 0)
                || ! empty($param))
                && isset($_GET[$param])) {
                    return true;
                } else {
                    return false;
                }
            }
            return true;
        } else {
            return false;
        }
    }
    /**
     * 
     */
    public static function isPost(string $param = null)
    {
        if (is_array($_POST) && ! empty($_POST)) {
            if (! is_null($param)) {
                if (((is_numeric($param) && $param >= 0)
                || ! empty($param))
                && isset($_POST[$param])) {
                    return true;
                } else {
                    return false;
                }
            }
            return true;
        } else {
            return false;
        }
    }
    /**
     * 
     */
    public static function get(string $param = null)
    {
        $get = $_GET;
        if (! is_null($param)) {
            if (HTTPHelper::isGET($param)) {
                return $get[$param];
            } else {
                return false;
            }
        } else {
            return $get;
        }
    }
    /**
     * 
     */
    public static function post(string $param = null)
    {
        $post = $_POST;
        if (! is_null($param)) {
            if (HTTPHelper::isPOST($param)) {
                return $post[$param];
            } else {
                return false;
            }
        } else {
            return $post;
        }
    }
    /**
     * 
     */
    public static function URI()
    {
        $query = $_SERVER['QUERY_STRING'];
        $query = DataCleanerHelper::cleanValue($query);
        if (empty($query)) {
            return '/';
        } else {
            return trim($query, '/');
        }
    }
    /**
     * 
     */
    public static function redirect(string $uri, array $params = null, int $responseCode = null)
    {
        // redirect to new $uri
        $url = config('LINKS.PUBLIC') . $uri;
        if (! is_null($params)) {
            $keys = array_keys($params);
            for ($i = 0; $i < count($params); $i++) {
                $key = $keys[$i];
                if (is_string($params[$key]) || is_numeric($params[$key])) {
                    $value = str_replace(' ', '%20', $params[$key]);
                } else {
                    $value = json_encode($params[$key]);
                }
                if ($i === 0) {
                    $url .= '?';
                } else {
                    $url .= '&';
                }
                if ($value === '' || ! is_string($value)) {
                    $url .= "$key";
                } else {
                    $url .= "$key=$value";
                }
            }
        }
        if (! is_null($responseCode) && $responseCode > 0) {
            header("Location: $url", true, $responseCode);
        } else {
            header("Location: $url");
        }
        exit();
    }
}