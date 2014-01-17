<?php echo $newClassNamespace ? 'namespace ' . $newClassNamespace . ';' : ''; ?>

class {{newClassName}} <?php
$interfaces = '\\' . $generatorNamespace . '\\Interfaces\\Generated, \\' . $generatorNamespace . '\\Interfaces\\Decorator';
if (interface_exists($baseClass)) {
    echo 'implements \\' . $baseClass . ', ' . $interfaces;
} else {
    echo 'extends \\' . $baseClass . ' implements ' . $interfaces;
} ?>
{
    /**
     * @var \{{baseClass}}|\ClassGenerator\BaseDecorator
     */
    protected $cgDecorated;

    /**
     * @param \{{baseClass}}|\ClassGenerator\BaseDecorator $decorated
     */
    public function __construct($decorated)
    {
        $this->cgSetDecorated($decorated);
    }

    /**
     * @return \{{baseClass}}|\ClassGenerator\BaseDecorator
     */
    public function cgGetDecorated()
    {
        return $this->cgDecorated;
    }

    /**
     * @return object
     */
    public function cgGetProxifiedObject()
    {
        return $this->cgDecorated;
    }

    /**
     * @param \{{baseClass}}|\ClassGenerator\BaseDecorator $decorated
     */
    public function cgSetDecorated($decorated)
    {
        if ($decorated instanceof \{{baseClass}} || $decorated instanceof \ClassGenerator\BaseDecorator) {
            $this->cgDecorated = $decorated;
        } else {
            throw new \InvalidArgumentException('Argument to {{newClass}}::cgSetDecorated must be instanceof {{baseClass}} or \ClassGenerator\BaseDecorator');
        }
    }

    public function __call($methodName, $arguments)
    {
        return call_user_func_array(array($this->cgDecorated, $methodName), $arguments);
    }

    public function __clone()
    {
        $this->cgDecorated = clone $this->cgDecorated;
    }

    public function __sleep()
    {
        return array('cgDecorated');
    }

    <?php if (method_exists($baseClass, '__wakeup')) { ?>
    public function __wakeup()
    {
    }
    <?php } ?>

{{method}}
    <?php if (in_array($methodName, array('__call', '__clone', '__sleep', '__wakeup'))) continue; ?>
    {{$reflectionMethod->getDocComment() . "\n"}}
    function {{methodName}}({{parametersDefinition}})
    {
        return $this->cgDecorated->{{methodName}}({{parameters}});
    }

{{\method}}
}