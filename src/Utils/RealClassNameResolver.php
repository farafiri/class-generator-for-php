<?php
/**
 * Created by PhpStorm.
 * User: Rafał
 * Date: 17.12.13
 * Time: 11:03
 */

namespace ClassGenerator\Utils;


class RealClassNameResolver
{
    public function __construct($useGetter)
    {
        $this->useGetter = $useGetter;
    }

    public function resolve($className, $contextClassName, $nonNamespaceClassMustStartWithBackslash = false)
    {
        if (substr($className, 0, 1) === '\\') {
            return substr($className, 1);
        }

        $uses = $this->useGetter->getUses($contextClassName);
        if (isset($uses[$className])) {
            return $uses[$className];
        }
        // @TODO add PartialNamespace\ClassName support

        $namespaceClassName = $this->getNamespace($contextClassName) . '\\' . $className;
        if ($nonNamespaceClassMustStartWithBackslash || class_exists($namespaceClassName) || interface_exists($namespaceClassName)) {
            return $namespaceClassName;
        } elseif (class_exists($className) || interface_exists($className)) {
            return $className;
        } else {
            return $namespaceClassName;
        }
    }

    public function getNamespace($className)
    {
        $exploded = explode('\\', $className);
        unset($exploded[count($exploded) - 1]);
        return implode('\\', $exploded);
    }
} 