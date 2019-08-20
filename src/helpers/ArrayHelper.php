<?php
namespace App\Helpers;

use App\Helper;
/**
 * 
 */
final class ArrayHelper extends Helper
{
    // TODO: make params a fallback function
    /**
     * compareArrays returns the whole array if statements are satisfied
     * 
     * Compare the array against the parameters.
     * - if empty param array then only items with true values return.
     * - if params is '!' then only items with false values return.
     * - if params is 'value' then only items with 'value' return.
     * - if params is array('key') then only items with 'key' return.
     * - if params is array('key' => 'value') then only items equal to (key and value) return.
     * - if params is array('key' => '!') then only items equal to (key and not value) return.
     * - if params contains array('!', ...) then all true statements are excluded.
     * 
     * @param	array		$compare		array of arrays that you want to compare against.
     * @param	array		$params			parameters that will be used to compare the arrays against.
     * @param	bool		$recursive		check arrays within the array.
     * @param	int			$r				the depth of comparing the array.
     * 
     * @return	array		arrays that match parameters given
     */
    public static function compareArrays(array $compare, $params = array(), bool $recursive = true, $r = -1)
    {
        $differences = array();
        if (is_array($params) && isset($params[0]) && $params[0] === '!') {
            $excludeKeys = true;
        } else {
            $excludeKeys = false;
        }
        foreach ($compare as $key => $value) {
            $exclude = false;
            if (!is_array($params)) {
                if (is_string($params)) {
                    $param = explode(',', $params);
                    foreach ($param as $match) {
                        if ($match === '!' && empty($value)) {
                            $differences[$key] = $value;
                            break;
                        } elseif ($value === $match) {
                            $differences[$key] = $value;
                            break;
                        }
                    }
                } else {
                    if ($params === '!' && empty($value)) {
                        $differences[$key] = $value;
                    } elseif ($value === $params) {
                        $differences[$key] = $value;
                    }
                }
            } elseif (!is_array($params) || count($params) == 0) {
                if ($value) {
                    $differences[$key] = $value;
                }
            } else {
                foreach ($params as $field => $param) {
                    if ($excludeKeys) {
                        if (!is_numeric($field)) {
                            if ($param === '!' && $field === $key && empty($value)) {
                                $exclude = true;
                                break;
                            } elseif ($param === '' && $field === $key && $value) {
                                $exclude = true;
                                break;
                            } elseif ($field === $key && $param === $value) {
                                $exclude = true;
                                break;
                            } elseif (is_array($value) && (array($field => $param) === $value)) {
                                $exclude = true;
                                break;
                            }
                        } elseif ($param != '!') {
                            if ($param === $key) {
                                $exclude = true;
                                break;
                            } elseif ($param === '' && empty($value)) {
                                $exclude = true;
                                break;
                            } elseif (is_array($value) && ($param === $value)) {
                                $exclude = true;
                                break;
                            }
                        }
                    } else {
                        if (!is_numeric($field)){
                            if ($param === '!' && $field === $key && empty($value)) {
                                $differences[$key] = $value;
                                break;
                            } elseif ($param === '' && $field === $key && $value) {
                                $differences[$key] = $value;
                                break;
                            } elseif ($field === $key && $param === $value) {
                                $differences[$key] = $value;
                                break;
                            }
                        } else {
                            if ($param === $key && $value) {
                                $differences[$key] = $value;
                                break;
                            } elseif (is_array($value) && ($param === $value)) {
                                $differences[$key] = $value;
                                break;
                            }
                        }
                    }
                }
                if ($excludeKeys && !$exclude && !($recursive && is_array($value) && $r >= 0)) {
                    $differences[$key] = $value;
                } elseif ($excludeKeys && $exclude && $r == -2) {
                    $differences = array();
                }
            }
            if ($recursive && is_array($value) && !$exclude) {
                $returnArr = array();
                if ($r == -1) {
                    $returnArr = self::compareArrays($value, $params, true);
                } elseif ($r >= 0) {
                    $returnArr = self::compareArrays($value, $params, true, (($r === 0) ? -2: ($r -1)));
                }
                if (is_array($returnArr) && count($returnArr) > 0) {
                    $differences[$key] = $returnArr;
                }
            }
        }
        $hasKeys = 0;
        foreach ($differences as $head => $body) {
            if (is_string($head)) {
                foreach ($params as $key => $item) {
                    if (is_numeric($key)) {
                        if ($head == $item) {
                            $hasKeys++;
                        }
                    } elseif (is_string($key)) {
                        if ($head == $key) {
                            $hasKeys++;
                        }
                    }
                }
            } elseif (is_array($body)) {
                return $differences;
            }
        }
        if ($hasKeys > 0){
            if ($hasKeys != count($params)) {
                return array();
            }
        }

        return $differences;
    }
    /**
     * addToEntries returns a new array with items added to each entry
     * 
     * Compare the array against the parameters.
     * - if empty param array then only items with true values return.
     * - if params is '!' then only items with false values return.
     * - if params is 'value' then only items with 'value' return.
     * - if params is array('key') then only items with 'key' return.
     * - if params is array('key' => 'value') then only items equal to (key and value) return.
     * - if params is array('key' => '!') then only items equal to (key and not value) return.
     * - if params contains array('!', ...) then all true are statements are excluded.
     * 
     * @param	array		$entries		array to added items to.
     * @param	array		$append			items to added to matching array.
     * @param	array		$params			parameters that will be used to compare the arrays against.
     * @param	bool		$recursive		check array within the array.
     * 
     * @return	array		arrays with new items added
     */
    public static function addToEntries(array $entries, $append, $params = [], bool $recursive = true)
    {
        if (empty($append)) {
            return $entries;
        }
        if (empty($params)) {
            $newEntries = $entries;
            foreach ($entries as $key => $entry) {
                if (is_array($append)) {
                    foreach ($append as $index => $item) {
                        if (is_array($newEntries[$key])) {
                            $newEntries[$key][$index] = $item;
                        }
                    }
                } else {
                    $newEntries[$key][] = $append;
                }
            }
        } else {
            $newEntries = $entries;
            $filter = self::compareArrays($entries, $params, $recursive, 0);
            $keys = array_keys($filter);

            foreach ($keys as $key) {
                if (isset($newEntries[$key])) {
                    if (is_array($append)) {
                        foreach ($append as $index => $item) {
                            $newEntries[$key][$index] = $item;
                        }
                    } else {
                        $newEntries[$key][] = $item;
                    }
                }
            }
        }

        return $newEntries;
    }
    /**
     * hasKeys uses collectKeys to get keys 
     * and returns a boolean if found
     * 
     * @param	array		$arr			array to added items to.
     * @param	array		$keys			parameters with the keys to check the array against.
     * 
     * @return	bool		returns true if array has keys
     */
    public static function hasKeys(array $arr, $keys)
    {
        $keys = self::collectKeys($arr, $keys, true);
        if (empty($keys) || !is_array($keys) || count($keys) == 0) {
            return false;
        }
        return true;
    }
    /**
     * collectKeys returns the array with the key requested
     * 
     * Collect the keys against the parameters.
     * - if params is 'key' then only the last item with 'key' return. // TODO: get first item
     * - if params is array('key') then only items with 'key' return.
     * 
     * @param	array		$arr			array to added items to.
     * @param	array		$keys			parameters with the keys to check the array against.
     * @param	bool		$recursive		check arrays within the array.
     * @param	int			$r				the depth of comparing the array.
     * 
     * @return	array		returns the array with the keys
     */
    public static function collectKeys(array $arr, $keys, bool $recursive = true, $r = 0)
    {
        $items = array();
        $r++;
        foreach ($arr as $index => $value) {
            if (is_array($keys)) {
                $obj = array();
                foreach ($keys as $pointer => $key) {
                    if (!is_numeric($key)) {
                        if ($index === $key) {
                            $obj = $value;
                            break;
                        } elseif (is_array($value) && $recursive) {
                                if ($r == 0) {
                                $returnArr = self::collectKeys($value, $key, $recursive);
                            } elseif ($r > 0) {
                                $returnArr = self::collectKeys($value, $key, $recursive, $r);
                            }
                            if (!empty($returnArr)) {
                                $obj = $returnArr;
                                break;
                            }
                        }
                    }
                    $obj = array();
                }
                if (is_array($obj) && count($obj) > 0 || $obj) {
                    $items[] = $obj;
                }
            } elseif (!is_numeric($keys)){
                if ($index === $keys) {
                    $items = $value;
                } elseif ($recursive && is_array($value)) {
                    $returnArr = array();
                    if ($r == 0) {
                        $returnArr = self::collectKeys($value, $keys, $recursive);
                    } elseif ($r > 0) {
                        $returnArr = self::collectKeys($value, $keys, $recursive, $r);
                    }
                    if (!empty($returnArr)) {
                        $items = $returnArr;
                    }
                }
            }
        }
        if (empty($items)) {
            return false;
        }
        return $items;
    }
    /**
     *  List entries with incrementing keyName index
     */
    public static function customKeys(array $arr, string $keyName)
    {
        $newArray = [];
        $index = 0;

        foreach ($arr as $key => $value) {
            $newArray[$keyName . '_' . $index] = $value;
            $index++;
        }

        return $newArray;
    }
    /**
     * 
     */
    public static function toCSV(array $array)
    {
        return self::toInLine($array, ', ');
    }
    /**
     * 
     */
    public static function toInLine(array $array, string $separator)
    {
        $csv = [];
        foreach ($array as $row => $column) {
            foreach ($column as $key => $value) 
            {
                $csv .= $value . $separator;
            }
            $csv .= "\n";
        }
        $csv = trim($csv, $separator);
        return $csv;
    }
    /**
     * 
     */
    public static function outerMerge(array $array)
    {
        $newArray = [];
        foreach ($array as $key => $value) {
            if (is_array($value)) {
                $newArray = array_merge($newArray, $value);
            } elseif (is_object ($value)) {
                $newArray = array_merge($newArray, (array)$value);
            } elseif (! is_null ($value)) {
                array_push($newArray, $value);
            }
        }
        return $newArray;
    }
    /**
     * Search deep into array
     * 
     * @param       array           $array          array to search
     * @param       string          $index          indexString that contains path to key within array
     *                                              e.g. 'firstKey.secondKey.wantedKey'
     * @return      mix             value of index or null if key is not found
     */
    public static function deepSearch($array, string $index, string $delimiter = '.')
    {
        if (! empty($index)) {
            $index = trim($index, $delimiter);
            $indexes = explode($delimiter, $index);
            if (count($indexes) > 0) {
                $item = (array) $array;
                foreach ($indexes as $key) {
                    if (is_object($item)) {
                        $item = (array) $item;
                    }
                    if (isset($item[$key])) {
                        $item = $item[$key];
                    } else {
                        return null;
                    }
                }
                return $item;
            }
        }
        return null;
    }
}