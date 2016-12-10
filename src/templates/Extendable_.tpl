<?php echo $newClassNamespace ? 'namespace ' . $newClassNamespace . ';' : '';
$interfaces = '';
foreach($extraInterfaces as $extraInterface) {
    $interfaces .= ', \\' . $extraInterface;
}
?>

class {{newClassName}} extends <?php echo '\\' . $generator->getGeneratorAggregator()->getNewClassNameFor($baseClass, 'decorator'); ?>
  implements \{{generatorNamespace}}\Interfaces\Extendable {{interfaces}}
{
    protected $cgTrueExteddable;
    protected $cgLastDecorated;

    public function cgGetLastDecorated() {
        return $this->cgLastDecorated;
    }

    public function cgSetLastDecorated($cgLastDecorated) {
        $this->cgLastDecorated = $cgLastDecorated;
        return $this;
    }

    /**
     * @param \ClassGenerator\BaseDecorator $decorator
     */
    public function cgExtendWith(\ClassGenerator\BaseDecorator $decorator)
    {
        $decorator->cgExtend($this);
    }

    /**
     * @param \ClassGenerator\BaseDecorator $decorator
     */
    public function cgReextend(\ClassGenerator\BaseDecorator $decorator = null)
    {
        if ($decorator) {
            $this->cgExtendWith($decorator);
        }

        return \ClassGenerator\Autoloader::getInstance()->getGenerator()->redecorate($this);
    }

    public function __sleep()
    {
        return array('cgDecorated', 'cgTrueExteddable', 'cgLastDecorated');
    }

    <?php echo $reflectionMethod ? $reflectionMethod->getdocComment() : ''; ?>
    public function __construct({{parametersDefinition}})
    {
        <?php
            $trueExtendableClassName = $generator->getGeneratorAggregator()->getNewClassNameFor($baseClass, 'trueCGExtendable');
            $redirectClassName = $generator->getGeneratorAggregator()->getNewClassNameFor($baseClass, 'redirectCGExtendable');
        ?>
        $this->cgTrueExtendable = new \{{trueExtendableClassName}}({{parameters}});
        $redirect = new \{{redirectClassName}}($this->cgTrueExtendable);
        $this->cgTrueExtendable->cgSetThis($redirect);
        $this->cgLastDecorated = $this;
        $this->cgSetDecorated($redirect);
    }

    static public function cgCopySettingsFrom($obj) {
        $r = new \ReflectionClass('{{newClass}}');
        $access = new \ReflectionClass(get_class($obj));
        $instance = $r->newInstanceWithoutConstructor();
        foreach(array('cgTrueExteddable', 'cgLastDecorated', 'cgDecorated') as $propertyName) {
            $property = $access->getProperty($propertyName);
            $property->setAccessible(true);
            $value = $property->getValue($obj);
            $instance->$propertyName = ($value === $obj) ? $instance : $value;
        }
        return $instance;
    }

// copy of [method] template from decoratorFor
{{method}}
    <?php if (in_array('Serializable', class_implements($baseClass)) && in_array($methodName, array('serialize', 'unserialize'))) continue; ?>
    <?php if (in_array($methodName, array('__call', '__clone', '__sleep', '__wakeup'))) continue; ?>
    {{$reflectionMethod->getDocComment() . "\n"}}
    function {{methodName}}({{parametersDefinition}}){{returnType}}
    {
        {{ret}}$this->cgDecorated->{{methodName}}({{parameters}});
    }

{{\method}}
}