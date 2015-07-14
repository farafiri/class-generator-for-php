<?php

namespace ClassGenerator;


class GeneratorAggregator
{
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
            'cExposeTrait' =>  new SimpleClassGenerator('*CExposeTrait', $templateClassCodeGenerator, array('Methods')),
            'exposeTrait' =>  new SimpleClassGenerator('*ExposeTrait', $templateClassCodeGenerator),
            'Property' => new Property(new GeneralTemplateClassCodeGenerator()),
            'DoctrineCollection' => new DoctrineCollection(new GeneralTemplateClassCodeGenerator())
        );

        foreach($this->generators as $generator) {
            $generator->setGeneratorAggregator($this);
        }
    }

    public function getGenerators()
    {
        return $this->generators;
    }

    public function setGenerators(array $generators)
    {
        $this->generators = $generators;
    }

    public function addGenerator($generator, $index = null) {
        if ($index === null) {
            $this->generators[] = $generator;
        } else {
            $this->generators[$index] = $generator;
        }

        return $this;
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
        return $lazyClassName::cgGet($closure, $this, $lazyMethods);
    }

    public function lazyMethods($object)
    {
        $lazyClassName = $this->generators['lazy']->getClassName(get_class($object));
        return $lazyClassName::cgGet($object, $this);
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
