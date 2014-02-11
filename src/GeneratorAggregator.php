<?php

namespace ClassGenerator;


class GeneratorAggregator
{
    protected static $instance;

    protected $generators;

    /**
     * array of accepted namespaces
     *
     * @var string[]
     */
    protected $acceptedNamespaces = array('');

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
            'decorable' => new SimpleClassGenerator('Decorable*', $templateClassCodeGenerator),
            'composite' =>  new SimpleClassGenerator('Composite*', $templateClassCodeGenerator),
        );

        foreach($this->generators as $generator) {
            $generator->setGeneratorAggregator($this);
        }
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

    public function setAcceptedNamespaces($acceptedNamespaces)
    {
        $this->acceptedNamespaces = $acceptedNamespaces;
    }

    public function getAcceptedNamespaces()
    {
        return $this->acceptedNamespaces;
    }

    protected function isInAcceptedNamespace($className)
    {
        $className = $className . '\\';
        foreach($this->acceptedNamespaces as $ns) {
            if (strpos($className, $ns . '\\') === 0 || $ns === '') {
                return true;
            }
        }

        return false;
    }

    public function generateCode($className)
    {
        if (!$this->isInAcceptedNamespace($className)) {
            return null;
        }

        foreach($this->generators as $generator) {
            $code = $generator->generateCodeFor($className);
            if ($code) {
                return $code;
            }
        }
    }

    public function getNewClassNameFor($className, $generatorName)
    {
        return $this->generators[$generatorName]->getClassName($className);
    }

    public function lazy($closure, $className, $lazyMethods = true)
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

    public function decorate($object)
    {
        $decoratorClassName = $this->generators['decorator']->getClassName(get_class($object));
        return new $decoratorClassName($object);
    }
}
