<?php echo $newClassNamespace ? 'namespace ' . $newClassNamespace . ';' : ''; ?>
<?php $methodPostfix = '_CGExtendableDirect'; ?>
class {{newClassName}} implements \{{generatorNamespace}}\Interfaces\Decorator, \{{generatorNamespace}}\Interfaces\Redirect
{
    protected $cgDecorated;

    public function __construct($cgDecorated)
    {
        $this->cgDecorated = $cgDecorated;
    }

    /**
     * @return object|null
     */
    public function cgGetDecorated() {
        return $this->cgDecorated;
    }

    /**
     * @return object|null
     */
    public function cgGetProxifiedObject() {
        return $this->cgDecorated;
    }

    /**
     * @param object $decorated
     */
    public function cgSetDecorated($decorated) {
        $this->cgDecorated = $decorated;
    }

    {{method}}
    <?php if (in_array($methodName, array('__call', '__clone', '__sleep', '__wakeup'))) continue; ?>
    {{$reflectionMethod->getDocComment() . "\n"}}
    function {{methodName}}({{parametersDefinition}}){{returnType}}
    {
        {{ret}}$this->cgDecorated->{{methodName}}{{methodPostfix}}({{parameters}});
    }

    {{\method}}
}