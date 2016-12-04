<?php

namespace ClassGenerator;

use ClassGenerator\Utils\Utils;

class TemplateClassCodeGenerator
{
    public function generate($newClass, $baseClass, $template, $extraData = array())
    {
        extract($extraData);
        $explodedNewClass = explode('\\', $newClass);
        $newClassName = array_pop($explodedNewClass);
        $newClassNamespace = implode('\\', $explodedNewClass);
        $reflectionClass = new \ReflectionClass($baseClass);
        try {
            $reflectionMethod = $reflectionClass->getMethod('__construct');
            $parametersDefinition = $this->helper_getParametersDefinition($reflectionMethod);
            $parameters = $this->helper_getParameters($reflectionMethod);
            $arrayParameters = $this->helper_getArrayParameters($reflectionMethod);
            $parametersList = $this->helper_getParameters($reflectionMethod, true);
        } catch(\Exception $e) {
            $reflectionMethod = null;
            $parametersDefinition = null;
            $parameters = null;
            $parametersList = null;
            $arrayParameters = 'array()';
        }
        $generatorNamespace = __NAMESPACE__;
        $reflectionMethods = $this->getMethods($reflectionClass, $extraData);
        $template = str_replace('{{method}}', '<?php foreach($reflectionMethods as $reflectionMethod) {
            $methodName = $reflectionMethod->getName();
            if ($methodName === "__construct" || $methodName === "__destruct") continue;
            if ($reflectionMethod->isStatic()) continue;
            $parametersDefinition = $this->helper_getParametersDefinition($reflectionMethod);
            $parameters = $this->helper_getParameters($reflectionMethod);
            $arrayParameters = $this->helper_getArrayParameters($reflectionMethod);
            $parametersList = $this->helper_getParameters($reflectionMethod, true);
            $returnType = $this->helper_getReturnTypeDefinition($reflectionMethod);
        ?>', $template);

        $template = str_replace('{{\method}}', '<?php } ?>', $template);

        $template = preg_replace_callback('/\{\{([a-zA-Z_0-9]+)\}\}/', function ($a) {
            return '<?php echo $' . $a[1] . '; ?>';
        }, $template);

        $template = preg_replace_callback('/\{\{((.|\n)+?)\}\}/', function ($a) {
            return '<?php echo ' . $a[1] . '; ?>';
        }, $template);

        ob_start();
        eval('?>' . $template );
        $result = ob_get_contents();
        ob_end_clean();

        return $result;
    }

    public function helper_getReturnTypeDefinition(\ReflectionMethod $reflectionMethod) {
        if (method_exists($reflectionMethod, 'getReturnType') && $reflectionMethod->getReturnType()) {
            return ': ' . Utils::typeToString($reflectionMethod->getReturnType());
        }

        return '';
    }

    public function helper_getParametersDefinition(\ReflectionMethod $reflectionMethod)
    {
        $parameters = array();
        foreach($reflectionMethod->getParameters() as $parameter) {
            if (method_exists($parameter, 'getType')) {
                $parameterStr = Utils::typeToString($parameter->getType());
            } elseif ($parameter->getClass()) {
                $parameterStr = '\\' .$parameter->getClass()->getName() . ' ';
            } elseif ($parameter->isArray()) {
                $parameterStr = 'array ';
            } elseif ($parameter->isCallable()) {
                $parameterStr = 'callable ';
            } else {
                $parameterStr = '';
            }

            if (!$parameter->canBePassedByValue()) {
                $parameterStr .= '&';
            }

            if (method_exists($parameter, 'isVariadic') && $parameter->isVariadic()) {
                $parameterStr .= '...';
            }

            $parameterStr .= '$' . $parameter->getName();

            if ($parameter->isDefaultValueAvailable()) {
                if (method_exists($parameter, 'isDefaultValueConstant') && $parameter->isDefaultValueConstant()) {
                    $default = $parameter->getDefaultValueConstantName();
                } else {
                    $default = var_export($parameter->getDefaultValue(), true);
                }

                $parameterStr .= ' = ' . $default;
            }

            $parametersNames[] = '$' . $parameter->getName();
            $parameters[] = $parameterStr;
        }

        return implode(',', $parameters);
    }

    public function helper_getParameters(\ReflectionMethod $reflectionMethod, $parametersList = false)
    {
        $parameters = array();
        foreach($reflectionMethod->getParameters() as $parameter) {
            $isVariadic = !$parametersList && method_exists($parameter, 'isVariadic') && $parameter->isVariadic();
            $parameters[] = ($isVariadic ? '...' : '') . '$' . $parameter->getName();
        }

        return implode(',', $parameters);
    }

    public function helper_getArrayParameters(\ReflectionMethod $reflectionMethod)
    {
        $parameters = array();
        if (method_exists($reflectionMethod, 'isVariadic') && $reflectionMethod->isVariadic()) {
            foreach($reflectionMethod->getParameters() as $parameter) {
                $isVariadic = method_exists($parameter, 'isVariadic') && $parameter->isVariadic();
                $p = '$' . $parameter->getName();
                $parameters[] = $isVariadic ? $p : ('[' . $p . ']');
            }

            return 'array_merge(' . implode(',', $parameters) . ')';
        } else {
            foreach($reflectionMethod->getParameters() as $parameter) {
                $parameters[] = '$' . $parameter->getName();
            }

            return 'array(' . implode(',', $parameters) . ')';
        }
    }

    protected function getMethods($reflectionClass, $extraData) {
        return isset($extraData['@methods']) ? $extraData['@methods'] : $reflectionClass->getMethods(\ReflectionMethod::IS_PUBLIC);
    }
}
