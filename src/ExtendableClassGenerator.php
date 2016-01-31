<?php
/**
 * Created by PhpStorm.
 * User: Rafał
 * Date: 30.01.16
 * Time: 20:29
 */

namespace ClassGenerator;


class ExtendableClassGenerator extends SimpleClassGenerator {
    protected $coder;

    public function __construct($classNamePattern, $templateClassGenerator, $genParams = array(), $coder = null) {
        parent::__construct($classNamePattern, $templateClassGenerator, $genParams);
        $this->coder = $coder ? $coder : new Utils\Coder();
    }

    public function getClassName($baseClassName, $params = array())
    {
        if (!empty($params['interfaces'])) {
            $params['interfaces'] = $this->normalizeInterfaces($params['interfaces'], $baseClassName);
            foreach($params['interfaces'] as $interface) {
                $params['AddInterface'][] = $this->coder->encode($interface);
            }

            unset($params['interfaces']);
        }

        return parent::getClassName($baseClassName, $params);
    }

    protected function getClassData($className) {
        if ($data = parent::getClassData($className)) {
            $interfaces = array();
            if (isset($data[1]['addInterfaceArr'])) {
                foreach($data[1]['addInterfaceArr'] as $encodedInterface) {
                    $interfaces[] = $this->coder->decode($encodedInterface);
                }
            }

            $data[1]['extraInterfaces'] = $interfaces;
            $classes = array_merge(array($data[0]), $interfaces);
            $methods = array();
            foreach($classes as $className) {
                $reflectionClass = new \ReflectionClass($className);
                foreach($reflectionClass->getMethods(\ReflectionMethod::IS_PUBLIC) as $method) {
                    $methodName = $method->getName();
                    if (!isset($methods[$methodName])) {
                        $methods[$methodName] = $method;
                    }
                }
            }

            $data[1]['@methods'] = array_values($methods);
        }

        return $data;
    }

    protected function normalizeInterfaces($interfaces, $className) {
        sort($interfaces);
        return array_unique($interfaces);
    }
} 