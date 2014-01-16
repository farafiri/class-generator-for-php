<?php echo $newClassNamespace ? 'namespace ' . $newClassNamespace . ';' : ''; ?>

class {{newClassName}} <?php
$interfaces = '\\' . $generatorNamespace . '\\Interfaces\\Generated, \\' . $generatorNamespace . '\\Interfaces\\Lazy';
if (interface_exists($baseClass)) {
    echo 'implements \\' . $baseClass . ', ' . $interfaces;
} else {
    echo 'extends \\' . $baseClass . ' implements ' . $interfaces;
} ?>
{
    {{method}}
    <?php
        $methodSettings[$methodName] = \ClassGenerator\Utils\Utils::getMethodLazyOptions($reflectionMethod);
    ?>
    {{\method}}

    static protected $defaultLazyProxySettings = <?php var_export($methodSettings); ?>;

    /**
     * @var null|\{{baseClass}}
     */
    protected $cgProxifiedObject = null;

    /**
     * @var null|callable
     */
    protected $cgProxifiedObjectCreator;

    /**
     * @var \ClassGenerator\Generator
     */
    protected $cgLazyProxyCreator;

    /**
     * @var array
     */
    protected $cgLazyProxySettings;

    /**
     * @var boolean
     */
    protected $cgLazyMethods;

    /**
     * @param mixed                     $proxifiedObjectCreator proxifiedObjectCreator or object itself
     * @param \ClassGenerator\Generator $lazyProxyCreator
     * @param boolean                   $lazyMethods
     */
    public function __construct($proxifiedObjectCreator, $lazyProxyCreator = null, $lazyMethods = true)
    {
        if ($proxifiedObjectCreator instanceof \Closure) {
            $this->cgProxifiedObjectCreator = $proxifiedObjectCreator;
        } else {
            $this->cgProxifiedObject = $proxifiedObjectCreator;
        }

        if ($lazyProxyCreator) {
            $this->cgLazyProxyCreator = $lazyProxyCreator;
        } else {
            $this->cgLazyProxyCreator = \ClassGenerator\Generator::getInstance();
        }

        $this->cgLazyProxySettings = static::$defaultLazyProxySettings;
        $this->cgLazyMethods = $lazyMethods;
    }

    /**
     * @return \{{baseClass}}
     */
    public function cgGetProxifiedObject()
    {
        if ($this->cgProxifiedObject === null) {
             $this->cgProxifiedObject = call_user_func($this->cgProxifiedObjectCreator);
        }

        return $this->cgProxifiedObject;
    }

    function __clone()
    {
        if ($this->cgProxifiedObject) {
            $this->cgProxifiedObject = clone $this->cgProxifiedObject;
        }
    }

    public function __sleep()
    {
        if ($this->cgProxifiedObject === null) {
            $this->cgProxifiedObject = call_user_func($this->cgProxifiedObjectCreator);
        }

        return array('cgProxifiedObject', 'cgLazyProxySettings', 'cgLazyMethods');
    }

    public function __wakeup()
    {
        $this->cgLazyProxyCreator = \ClassGenerator\Generator::getInstance();
    }

    {{method}}
    <?php if (in_array($methodName, array("__clone", "cgGetProxifiedObject", "__sleep", "__wakeup"))) continue; ?>
    {{$reflectionMethod->getDocComment() . "\n"}}
    function {{methodName}}({{parametersDefinition}})
    {
        if ($this->cgLazyMethods && $this->cgLazyProxySettings['{{methodName}}']['isLazyEvaluated']) {
            $closure = function () <?php echo $parameters ? "use ($parameters) " : ""; ?> {
                if ($this->cgProxifiedObject === null) {
                    $this->cgProxifiedObject = call_user_func($this->cgProxifiedObjectCreator);
                }

                return $this->cgProxifiedObject->{{methodName}}({{parameters}});
            };

            return $this->cgLazyProxyCreator->lazy($closure->bindTo($this, 'static'), $this->cgLazyProxySettings['{{methodName}}']['class'], $this->cgLazyProxySettings['{{methodName}}']['isLazyMethods']);
        }

        if ($this->cgProxifiedObject === null) {
            $this->cgProxifiedObject = call_user_func($this->cgProxifiedObjectCreator);
        }

        if ($this->cgLazyMethods && $this->cgLazyProxySettings['{{methodName}}']['isLazyMethods']) {
            return $this->cgLazyProxyCreator->lazyMethods($this->cgProxifiedObject->{{methodName}}({{parameters}}));
        } else {
            return $this->cgProxifiedObject->{{methodName}}({{parameters}});
        }
    }

    {{\method}}
}