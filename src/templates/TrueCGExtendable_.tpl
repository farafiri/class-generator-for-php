<?php echo $newClassNamespace ? 'namespace ' . $newClassNamespace . ';' : ''; ?>
<?php $methodPostfix = '_CGExtendableDirect'; ?>

class {{newClassName}} extends \{{baseClass}} implements \{{generatorNamespace}}\Interfaces\Generated, \{{generatorNamespace}}\Interfaces\CGThis
{
    protected $cgThis;

    public function cgGetThis() {
        return $this->cgThis;
    }

    public function cgSetThis($cgThis) {
        $this->cgThis = $cgThis;
    }

    {{method}}
    <?php if (in_array($methodName, array('__call', '__clone', '__sleep', '__wakeup'))) continue; ?>

    function {{methodName}}({{parametersDefinition}})
    {
        return $this->cgThis->{{methodName}}({{parameters}});
    }

    function {{methodName}}{{methodPostfix}}({{parametersDefinition}})
    {
        return parent::{{methodName}}({{parameters}});
    }

    {{\method}}
}