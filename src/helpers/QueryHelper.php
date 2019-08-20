<?php
namespace App\Helpers;

use App\Helper;
/**
 * 
 */
final class QueryHelper extends Helper
{
    public static function arrayToStatements(array $array, string $field, string $operator, string $separator)
    {
        $statement = '';
        $index = 0;

        foreach ($array as $key => $value) {
            $index++;
            $statement .= "$field $operator :$key $separator ";
        }
        $statement = trim($statement);
        $statement = trim($statement, $separator);
        $statement = trim($statement);

        return $statement;
    }
    public static function insertCodes($codes, string $style = "{message}\n", bool $list = false, int $listLength = 0, bool $allowEmpty = false)
    {
        $message = '';
        $codes = (array)$codes;
        if (is_array($codes)) {
            if (! $list) {
                $item = $style;
                foreach ($codes as $code => $content) {
                    if (is_string($content) || is_numeric($content)) {
                        $item = str_replace(('{' . $code . '}'), $content, $item);
                    } else {
                        $item = str_replace(('{' . $code . '}'), '', $item);
                    }
                }
                if ($allowEmpty || $item !== $style) {
                    $message = $item;
                }
            } else {
                $count = count($codes);
                $keys = array_keys($codes);
                for ($i = 0; $i < $count; $i++) {
                    if ($listLength == 0 || $listLength > 0 && $listLength > $i 
                    || $listLength < 0 && ($count + $listLength) == $i) {
                        $key = $keys[$i];
                        $commands = (is_object($codes[$key]))? (array) $codes[$key]: $codes[$key];
                        $item = $style;
                        if (is_array($commands)) {
                            $commands['KEY'] = $key;
                            foreach ($commands as $code => $content) {
                                if (is_string($content) || is_numeric($content)) {
                                    $item = str_replace(('{' . $code . '}'), $content, $item);
                                } else {
                                    $item = str_replace(('{' . $code . '}'), '', $item);
                                }
                            }
                        } else {
                            if (is_string($commands) || is_numeric($commands)) {
                                $item = str_replace(['{KEY}', '{VALUE}'], [$key, $commands], $item);
                            }
                        }
                    }
                    if ($allowEmpty || $item !== $style) {
                        $message .= $item;
                    }
                }
            }
        }
        return $message;
    }
    public static function scanCodes($codes, string $subject, array $defaults = [], bool $list = false, int $listLength = 0, bool $allowEmpty = false)
    {
        $codes = (array)$codes;
        $insertCount = 0;
        
        $pattern = "/({(.*?)})/";
        $callback = function ($match) use ($codes, $defaults, &$insertCount) {
            if (isset($codes[$match[2]])
            && (is_string ($codes[$match[2]])
            || is_numeric($codes[$match[2]]))) {
                $insertCount++;
                return $codes[$match[2]] ?? '';
            } else {
                if (isset($defaults[$match[2]])
                && (is_string ($defaults[$match[2]])
                || is_numeric($defaults[$match[2]]))) {
                    return $defaults[$match[2]];
                }
            }
        };
        
        if ($list) {
            $message = '';
            $codeCount = count($codes);
            $keys = array_keys($codes);
            for ($i = 0; $i < $codeCount; $i++) {
                if ($listLength == 0 || $listLength > 0 && $listLength > $i 
                || $listLength < 0 && ($codeCount + $listLength) == $i) {
                    $key = $keys[$i];
                    $commands = (is_object($codes[$key]))? (array) $codes[$key]: $codes[$key];
                    if (is_array($commands)) {
                        $commands['KEY'] = $key;
                        $message .= self::scanCodes($commands, $subject, $defaults, false, 0, $allowEmpty);
                    } else {
                        if (is_string($commands) || is_numeric($commands)) {
                            $message .= self::scanCodes([
                                'KEY' => $key,
                                'VALUE' => $commands
                            ], $subject, $defaults, false, 0, $allowEmpty);
                        }
                    }
                }
            }
        } else {
            $message = preg_replace_callback($pattern, $callback, $subject);
            if ($allowEmpty === false && $insertCount == 0) {
                $message = '';
            }
        }
        return $message;
    }
}