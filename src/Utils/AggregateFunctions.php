<?php
/**
 * Created by PhpStorm.
 * User: Rafał
 * Date: 12.02.14
 * Time: 20:51
 */

namespace ClassGenerator\Utils;


class AggregateFunctions
{
    public static function afMax($collection)
    {
        return max($collection);
    }

    public static function afMin($collection)
    {
        return min($collection);
    }

    public static function afOr($collection)
    {
        $result = null;
        foreach($collection as $item) {
            if ($item && !$result || $item !== null && $result === null) {
                $result = $item;
            }
        }

        return $result;
    }

    public static function afAnd($collection, $default = null)
    {
        foreach($collection as $item) {
            if (!$item) {
                return $item;
            }
        }

        return isset($collection[0]) ? $collection[0] : $default;
    }

    public static function afSum($collection)
    {
        $result = 0;
        foreach($collection as $item) {
            if ($item) {
                $result += $item;
            }
        }

        return $result;
    }

    public static function afImplode($collection, $separator = '', $default = null)
    {
        $result = null;
        foreach($collection as $item) {
            if ($item !== null) {
                $result = $result ? ($result . $separator . $item) : $item;
            }
        }

        return $result === null ? $default : $result;
    }

    public static function afMerge($collection)
    {
        $result = array();
        foreach($collection as $item) {
            $result = array_merge($result, $item);
        }

        return $result;
    }

    public static function afComposite($collection, $compositeName, $canReturnNull)
    {
        if ($canReturnNull) {
            $collectionWithoutNull = array();
            foreach($collection as $item) {
                if ($item) {
                    $collectionWithoutNull[] = $item;
                }
            }

            if ($collectionWithoutNull) {
                $collection = $collectionWithoutNull;
            } else {
                return null;
            }
        }

        return new $compositeName($collection);
    }

    public function afThrow($collection, $error)
    {
        if (!$error) {
            $error = '';
        }

        if ($error instanceof \Exception) {
            $error = new \BadMethodCallException($error);
        }

        throw $error;
    }
} 