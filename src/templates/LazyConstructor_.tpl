<?php echo $newClassNamespace ? 'namespace ' . $newClassNamespace . ';' : ''; ?>

class {{newClassName}} <?php
$interfaces = '\\' . $generatorNamespace . '\\Interfaces\\Generated';
if (interface_exists($baseClass)) {
    throw new \Exception("LazyConstructor classes can be created only on class, $baseClass is an interface");
} else {
    echo 'extends \\' . $baseClass . ' implements ' . $interfaces;
};
if ($parametersDefinition === null) {
    throw new \Exception("LazyConstructor classes can be created only on class with constructor, $baseClass::__construct is not defined");
}

 ?>
{
    /**
     * @var array|null array if constructor was not called and null after constructor call
     */
    protected $cgConstructorParams = null;

    {{$reflectionMethod->getDocComment() . "\n"}}
    public function __construct({{parametersDefinition}})
    {
        if ($this->cgConstructorParams === null) {
            $this->cgConstructorParams = array({{parameters}});
        } else {
            $this->cgConstructorParams = null;
            parent::__construct({{parameters}});
        }
    }

    public function __clone()
    {
        if ($this->cgConstructorParams !== null) {
            call_user_func_array(array($this, '__construct'), $this->cgConstructorParams);
        }
        <?php if (method_exists($baseClass, '__clone')) { ?>
        parent::__clone();
        <?php } ?>
    }

{{method}}
    <?php if (in_array($methodName, array("__clone"))) continue; ?>
    {{$reflectionMethod->getDocComment() . "\n"}}
    function {{methodName}}({{parametersDefinition}})
    {
        if ($this->cgConstructorParams !== null) {
            call_user_func_array(array($this, '__construct'), $this->cgConstructorParams);
        }

        return parent::{{methodName}}({{parameters}});
    }

{{\method}}
}
