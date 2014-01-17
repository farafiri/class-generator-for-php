<?php

namespace ClassGenerator;


class Generator {
    protected static $instance;

    protected $generators;

    public function __construct()
    {
        $templateClassCodeGenerator = new TemplateClassCodeGenerator();
        $this->generators = array(
            'subject' => new SimpleClassGenerator('Subject*', $templateClassCodeGenerator),
            'nullObject' => new SimpleClassGenerator('Null*', $templateClassCodeGenerator),
            'lazyConstructor' => new SimpleClassGenerator('LazyConstructor*', $templateClassCodeGenerator),
            'lazy' => new SimpleClassGenerator('Lazy*', $templateClassCodeGenerator),
            'reference' => new SimpleClassGenerator('ReferenceTo*', $templateClassCodeGenerator),
            'override' => new SimpleClassGenerator('MethodOverrided*', $templateClassCodeGenerator),
            'decorator' => new SimpleClassGenerator('DecoratorFor*', $templateClassCodeGenerator),
            'composite' =>  new SimpleClassGenerator('Composite*', $templateClassCodeGenerator),
        );
    }

    public static function getInstance()
    {
        if (empty(self::$instance)) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function getGenerators()
    {
        return $this->generators;
    }

    public function setGenerators(array $generators)
    {
        $this->generators = $generators;
    }

    public function generateCode($className)
    {
        foreach($this->generators as $generator) {
            $code = $generator->generateCodeFor($className);
            if ($code) {
                return $code;
            }
        }
    }

    public function getNullObjectClassName($baseClassName)
    {
        if (is_object($baseClassName)) {
            $baseClassName = get_class($baseClassName);
        }

        $this->generators['nullObject']->getClassName($baseClassName);
    }

    public function getNewClassNameFor($className, $generatorName)
    {
        return $this->generators[$generatorName]->getClassName($className);
    }

    public function lazy($closure, $className, $lazyMethods)
    {
        $lazyClassName = $this->generators['lazy']->getClassName($className);
        return new $lazyClassName($closure, $this, $lazyMethods);
    }

    public function lazyMethods($object)
    {
        $lazyClassName = $this->generators['lazy']->getClassName(get_class($object));
        return new $lazyClassName($object, $this);
    }

    public function hardReference($object)
    {
        $referenceClassName = $this->generators['reference']->getClassName(get_class($object));
        return new $referenceClassName($object);
    }
}
