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
    public static function insertCodes($codes, string $style = "{message}\n", bool $list = false, int $listLength = 0, bool $allowEmpty = true)
    {
        $message = '';
        $codes = (array)$codes;
        if (is_array($codes)) {
            if (! $list) {
                $item = $style;
                foreach ($codes as $code => $content) {
                    if (is_string($content) || is_numeric($content)) {
                        $item = str_replace(('{' . $code . '}'), $content, $item);
                    } elseif ($allowEmpty) {
                        $item = str_replace(('{' . $code . '}'), '', $item);
                    }
                }
                if ($item !== $style) {
                    $message = $item;
                }
            } else {
                $count = count($codes);
                $keys = array_keys($codes);
                for ($i = 0; $i < $count; $i++) {
                    if ($listLength == 0 || $listLength > 0 && $listLength > $i || $listLength < 0 && ($count + $listLength) == $i) {
                        $key = $keys[$i];
                        $commands = (is_object($codes[$key]) ? (array) $codes[$key] : $codes[$key]);
                        $item = $style;
                        if (is_array($commands)) {
                            $commands['KEY'] = $key;
                            foreach ($commands as $code => $content) {
                                if (is_string($content) || is_numeric($content)) {
                                    $item = str_replace(('{' . $code . '}'), $content, $item);
                                } elseif ($allowEmpty) {
                                    $item = str_replace(('{' . $code . '}'), '', $item);
                                }
                            }
                        } else {
                            if (is_string($commands) || is_numeric($commands)) {
                                $item = str_replace(['{KEY}', '{VALUE}'], [$key, $commands], $item);
                            }
                        }
                    }
                    if ($item !== $style) {
                        $message .= $item;
                    }
                }
            }
        }
        return $message;
    }
}