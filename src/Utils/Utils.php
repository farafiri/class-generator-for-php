<?php
/**
 * Created by PhpStorm.
 * User: Rafał
 * Date: 30.12.13
 * Time: 13:38
 */

namespace ClassGenerator\Utils;


class Utils {
    protected static $phpDocTypes = array(
        'int' => 1,
        'integer' => 1,
        'bool' => 1,
        'boolean' => 1,
        'float' => 1,
        'double' => 1,
        'callback' => 1,
        'array' => 1,
        'object' => 1,
        'string' => 1,
        'null' => 1,
        'true' => 1,
        'false' => 1,
        'self' => 1);

    protected static $classNameResolver;

    protected static $defaultMethodLazyOptions = array(
        'isLazyEvaluated' => false,
        'isLazyMethods' => false
    );
    // @TODO test, test, test
    public static function getRealType($type, $classContext) {
        if (empty(self::$classNameResolver)) {
            self::$classNameResolver = new RealClassNameResolver(new UseGetter());
        }

        $docTypes = self::$phpDocTypes;
        $classNameResolver = self::$classNameResolver;
        return preg_replace_callback('/[a-zA-Z0-9\\\\]+/', function($a) use ($docTypes, $classContext, $classNameResolver) {
            $a = $a[0];
            return empty($docTypes[$a]) ? '\\' . $classNameResolver->resolve($a, $classContext, true) : $a;
        }, $type);
    }

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
            return self::getRealType($matches[1], $reflectionMethod->getDeclaringClass()->getName());
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
