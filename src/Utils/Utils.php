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

    public static function getReturnType(\ReflectionMethod $reflectionMethod)
    {
        $doc = $reflectionMethod->getDocComment();

        if ($doc && preg_match('/\* @returns?\s([^\s]+)/', $doc, $matches)) {
            return $matches[1];
        }

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