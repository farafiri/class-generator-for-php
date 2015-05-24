<?php

namespace ClassGenerator;


class SimpleClassGenerator extends BaseClassGenerator
{
    protected $classNamePattern;
    protected $regexPatterns = null;
    protected $template = null;

    public function __construct($classNamePattern, $templateClassGenerator)
    {
        $this->classNamePattern = $classNamePattern;
        $this->templateClassGenerator = $templateClassGenerator;
    }

    protected function getRegexPatterns()
    {
        if (!$this->regexPatterns) {
            $e = explode('*', $this->classNamePattern);
            $this->regexPatterns = array(
                '/(.*)(\\\\|^)' . $e[0] . '([A-Za-z0-9_]+)' . $e[1] . '$/',
                '/(.*)(\\\\|^)' . ($e[0] ? 'Base' : '') . $e[0] . '([A-Za-z0-9_]+)' . ($e[0] ? '' : 'Base') . $e[1] . '$/'
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
        $baseClassName = $this->getBaseClassName($className);
        if ($baseClassName) {
            return $this->templateClassGenerator->generate($className, $baseClassName, $this->getTemplate(), array('generator' => $this));
        }
    }

    /**
     * @param string $baseClassName class or interface name
     *
     * @return string class or interface name
     */
    public function getClassName($baseClassName)
    {
        $explodedPattern = explode('*', $this->classNamePattern);
        $explodedClassName = explode('\\', $baseClassName);
        array_push($explodedClassName, $explodedPattern[0] . array_pop($explodedClassName) . $explodedPattern[1]);

        return implode('\\', $explodedClassName);
    }

    protected function getBaseClassName($className)
    {
        foreach($this->getRegexPatterns() as $regexPattern) {
            if (preg_match($regexPattern, $className, $matches)) {
                $baseClass = $matches[1] . $matches[2] . $matches[3];
                if (class_exists($baseClass) || interface_exists($baseClass)) {
                    return $baseClass;
                }
            }
        }
    }
}
