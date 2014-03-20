<?php

namespace ClassGenerator;

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
        } catch(\Exception $e) {
            $reflectionMethod = null;
            $parametersDefinition = null;
        }
        $generatorNamespace = __NAMESPACE__;
        $template = str_replace('{{method}}', '<?php foreach($reflectionClass->getMethods(\\ReflectionMethod::IS_PUBLIC) as $reflectionMethod) {
            $methodName = $reflectionMethod->getName();
            if ($methodName === "__construct" || $methodName === "__destruct") continue;
            $parametersDefinition = $this->helper_getParametersDefinition($reflectionMethod);
            $parameters = $this->helper_getParameters($reflectionMethod);
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

    public function helper_getParametersDefinition(\ReflectionMethod $reflectionMethod)
    {
        $parameters = array();
        foreach($reflectionMethod->getParameters() as $parameter) {
            if ($parameter->getClass()) {
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

    public function helper_getParameters(\ReflectionMethod $reflectionMethod)
    {
        $parameters = array();
        foreach($reflectionMethod->getParameters() as $parameter) {
            $parameters[] = '$' . $parameter->getName();
        }

        return implode(',', $parameters);
    }
}
