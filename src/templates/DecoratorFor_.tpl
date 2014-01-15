namespace {{newClassNamespace}};

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

{{method}}
    <?php if (in_array($methodName, array('__call'))) continue; ?>
    {{$reflectionMethod->getDocComment() . "\n"}}
    function {{methodName}}({{parametersDefinition}})
    {
        return $this->cgDecorated->{{methodName}}({{parameters}});
    }

{{\method}}
}