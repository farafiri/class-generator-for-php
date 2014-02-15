<?php
/**
 * Created by PhpStorm.
 * User: Rafał
 * Date: 30.12.13
 * Time: 13:38
 */

namespace ClassGenerator\Utils;


class Utils {
    protected static $defaultMethodLazyOptions = array(
        'isLazyEvaluated' => false,
        'isLazyMethods' => false
    );

    public static function getDocAttribute(\ReflectionMethod $reflectionMethod, $attributeName)
    {
        $doc = $reflectionMethod->getDocComment();
        if ($doc && preg_match('/\* @' . $attributeName . '\s?(.*)\r?\n/', $doc, $matches)) {
            return $matches[1];
        }
    }

    public static function getReturnType(\ReflectionMethod $reflectionMethod)
    {
        $docReturn = self::getDocAttribute($reflectionMethod, 'returns?');
        if ($docReturn) {
            preg_match('/(.*?)\s.*/', $docReturn . ' ', $matches);
            return $matches[1];
        }

        if ($reflectionMethod->getName() === 'serialize' || $reflectionMethod->getName() === '__toString') {
            return 'string';
        }

        return null;
    }

    public static function returnsArrayOrNull(\ReflectionMethod $reflectionMethod)
    {
        $type = static::getReturnType($reflectionMethod);
        $type = preg_replace('/\([^)]\)/', 'mixed', $type);
        $types = explode('|', $type);
        $result = false;
        foreach($types as $subtype) {
            if ($subtype === 'array' || substr($subtype, -2) === '[]') {
                $result = true;
            } elseif ($subtype !== 'null') {
                return false;
            }
        }

        return $result;
    }

    /**
     * @param \ReflectionMethod $reflectionMethod
     * @param boolean           $canReturnNull
     *
     * @return string|null
     */
    public static function returnedObjectClassName(\ReflectionMethod $reflectionMethod, $canReturnNull = false)
    {
        $type = static::getReturnType($reflectionMethod);
        if (preg_match('/^\\\\([A-Za-z0-9_\\\\]+)$/', $type, $match)) {
            return $match[1];
        }elseif ($canReturnNull && preg_match('/^(null\|)?\\\\([A-Za-z0-9_\\\\]+)(\|null)?$/', $type, $match)) {
            return $match[2];
        };

        return null;
    }

    public static function getMethodLazyOptions(\ReflectionMethod $reflectionMethod)
    {
        $returnType = static::getReturnType($reflectionMethod);

        if ($returnType) {
            if (!preg_match('/^\\\\[a-zA-Z_0-9\\\\]+$/', $returnType)) {
                return static::$defaultMethodLazyOptions;
            }

            $result = array(
                'class' => substr($returnType, 1),
                'isLazyEvaluated' => true,
                'isLazyMethods' => true
            );

            $doc = $reflectionMethod->getDocComment();
            if ($doc && preg_match('/\* @lazy?\s([^\n*]+)/', $doc, $matches)) {
                $lazyDoc = $matches[1];

                if (preg_match('/noLazyEvaluation/', $lazyDoc)) {
                    $result['isLazyEvaluated'] = false;
                }

                if (preg_match('/noLazyMethods/', $lazyDoc)) {
                    $result['isLazyMethods'] = false;
                }
            }

            return $result;
        }

        static::$defaultMethodLazyOptions;
    }
}
