<?php echo $newClassNamespace ? 'namespace ' . $newClassNamespace . ';' : ''; ?>

class {{newClassName}} <?php
$interfaces = '\\' . $generatorNamespace . '\\Interfaces\\Generated, \\' . $generatorNamespace . '\\Interfaces\\WithMethodOverriding';
if (interface_exists($baseClass)) {
    echo 'implements \\' . $baseClass . ', ' . $interfaces;
} else {
    echo 'extends \\' . $baseClass . ' implements ' . $interfaces;
} ?>
{
    /**
     * @var \Closure[]
     */
    protected $cgMethodOverridingClosures;

    /**
     * @param \Closure[] $methodOverridingClosures
     */
    public function __construct($methodOverridingClosures<?php echo $parametersDefinition ? (', ' . $parametersDefinition) : ''; ?>)
    {
        if ($methodOverridingClosures instanceof \Closure) {
            $methodOverridingClosures = array('*' => $methodOverridingClosures);
        }
        foreach($methodOverridingClosures as $methodName => $closure) {
            $methodOverridingClosures[$methodName] = $closure->bindTo($this, '{{newClass}}');
        }

        $this->cgMethodOverridingClosures = $methodOverridingClosures;
        <?php if ($parametersDefinition !== null) { ?>
        parent::__construct({{parameters}});
        <?php } ?>
    }

    {{method}}
    <?php if (in_array($methodName, array("__clone"))) continue; ?>
    {{$reflectionMethod->getDocComment() . "\n"}}
    function {{methodName}}({{parametersDefinition}}){{returnType}}
    {
        if (isset($this->cgMethodOverridingClosures['{{methodName}}'])) {
            $closure = $this->cgMethodOverridingClosures['{{methodName}}'];
        } elseif (isset($this->cgMethodOverridingClosures['*'])) {
            $closure = $this->cgMethodOverridingClosures['*'];
        } else {
            {{ret}}parent::{{methodName}}({{parameters}});<?php echo $ret ? '':'return;'; ?>
        }

        {{ret}}$closure({{parameters}});
    }

    {{\method}}
}
