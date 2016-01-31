<?php

namespace ClassGenerator;


class SimpleClassGenerator extends BaseClassGenerator
{
    protected $classNamePattern;
    protected $regexPatterns = null;
    protected $template = null;
    protected $genParams;

    public function __construct($classNamePattern, $templateClassGenerator, $genParams = array())
    {
        $this->classNamePattern = $classNamePattern;
        $this->templateClassGenerator = $templateClassGenerator;
        $this->genParams = $genParams;
    }

    protected function getRegexPatterns()
    {
        if (!$this->regexPatterns) {
            $genParams = '';
            foreach($this->genParams as $param) {
                $genParams .= '((?:\\\\' . $param . '[A-Za-z0-9_]*)*)';
            }

            $e = explode('*', $this->classNamePattern);
            $this->regexPatterns = array(
                '/(.*)(\\\\|^)' . $e[0] . '([A-Za-z0-9_]+)' . $e[1] . $genParams . '$/',
                '/(.*)(\\\\|^)' . ($e[0] ? 'Base' : '') . $e[0] . '([A-Za-z0-9_]+)' . ($e[0] ? '' : 'Base') . $e[1] . $genParams . '$/'
            );
        }

        return $this->regexPatterns;
    }

    static public function getSTemplateFile($classNamePattern)
    {
        return  __DIR__ . DIRECTORY_SEPARATOR . 'templates' . DIRECTORY_SEPARATOR . str_replace('*', '_', $classNamePattern) . '.tpl';
    }

    public function getTemplateFile() {
        return static::getSTemplateFile($this->classNamePattern);
    }

    /**
     * @param string $className class or interface name
     *
     * @return string|null
     */
    public function generateCodeFor($className)
    {
        if ($classData = $this->getClassData($className)) {
            list($baseClassName, $params) = $classData;
            $params['generator'] = $this;
            return $this->templateClassGenerator->generate($className, $baseClassName, $this->getTemplate(), $params);
        }
    }

    /**
     * @param string $baseClassName class or interface name
     *
     * @return string class or interface name
     */
    public function getClassName($baseClassName, $params = array())
    {
        $explodedPattern = explode('*', $this->classNamePattern);
        $explodedClassName = explode('\\', $baseClassName);
        array_push($explodedClassName, $explodedPattern[0] . array_pop($explodedClassName) . $explodedPattern[1]);

        $className = implode('\\', $explodedClassName);
        foreach($params as $paramName => $values) {
            foreach((array) $values as $value) {
                $className .= '\\' . $paramName . $value;
            }
        }

        return $className;
    }

    public function getBaseClassName($className, $returnNull = true) {
        $data = $this->getClassData($className);
        return $data ? $data[0] : ($returnNull ? null : $className);
    }

    protected function getClassData($className)
    {
        foreach($this->getRegexPatterns() as $regexPattern) {
            if (preg_match($regexPattern, $className, $matches)) {
                $baseClass = $matches[1] . $matches[2] . $matches[3];
                if (class_exists($baseClass) || interface_exists($baseClass)) {
                    return array($baseClass, $this->generateParams($matches));
                }
            }
        }

        return null;
    }

    protected function generateParams($matches) {
        $result = array();
        $index = 4;
        foreach($this->genParams as $param) {
            $pmatches = $matches[$index++];
            if ($pmatches) {
                foreach(explode('\\', substr($pmatches, 1)) as $match) {
                    $val = str_replace($param, '', $match);
                    $result[lcfirst($param)] = $val;
                    $result[lcfirst($param) . 'Arr'][] = $val;
                }
            } else {
                $result[lcfirst($param)] = null;
                $result[lcfirst($param) . 'Arr'] = array();
            }
        }

        return $result;
    }
}
