<?php
/**
 * Created by PhpStorm.
 * User: Rafał
 * Date: 24.05.15
 * Time: 11:15
 */

namespace ClassGenerator;


class NamespacedGenerator extends BaseClassGenerator {
    protected $prefix;

    public function __construct($templateClassGenerator) {
        $this->templateClassGenerator = $templateClassGenerator;
    }

    /**
     * @param string $className class or interface name
     *
     * @return string|null
     */
    public function generateCodeFor($className)
    {
        $classNameData = $this->getClassNameData($className);
        if ($classNameData) {
            $data = array_merge($classNameData, array('generator' => $this));
            return $this->templateClassGenerator->generate($className, $this->getTemplate(), $data);
        }
    }

    protected function getClassNameData($className) {
        if (strpos($className, $this->getPrefix() . '\\') === 0) {
            $rest = substr($className, strlen('\\') + strlen($this->getPrefix()));
            $sRest = explode('\\', $rest);
            $sClassName = explode('\\', $className);
            return array(
                'full' => $className,
                'part' => $rest,
                'parts' => $sRest,
                'head' => lcfirst($sRest[0]),
                'newClassName' => $sClassName[count($sClassName) - 1],
                'newClassNamespace' => implode('\\', array_slice($sClassName, 0, -1)),
                'prefix' => $this->getPrefix(),
                'tail' => implode('\\', array_slice($sRest, 1))
            );
        }

        return null;
    }

    protected function getPrefix() {
        if (!$this->prefix) {
            $this->prefix = get_class($this);
        }

        return $this->prefix;
    }

    protected function setPrefix($prefix) {
        $this->prefix = $prefix;
    }

    public function getTemplateFile()
    {
        $classNameTail = array_slice(explode('\\', $this->getPrefix()), -1);
        return  __DIR__ . DIRECTORY_SEPARATOR . 'templates' . DIRECTORY_SEPARATOR . $classNameTail[0] . '.tpl';
    }
} 