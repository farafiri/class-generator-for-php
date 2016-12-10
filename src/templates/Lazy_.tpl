<?php echo $newClassNamespace ? 'namespace ' . $newClassNamespace . ';' : ''; ?>

class {{newClassName}} <?php
$interfaces = '\\' . $generatorNamespace . '\\Interfaces\\Generated, \\' . $generatorNamespace . '\\Interfaces\\Lazy';
if (interface_exists($baseClass)) {
    echo 'implements \\' . $baseClass . ', ' . $interfaces;
} else {
    echo 'extends \\' . $baseClass . ' implements ' . $interfaces;
} ?>
{
    public function __construct({{parametersDefinition}})
    {
        $this->cgProxifiedObjectConstructorParameters = {{arrayParameters}};
        $this->cgLazyProxyCreator = \ClassGenerator\Autoloader::getInstance()->getGenerator();
        $this->cgLazyProxySettings = static::$defaultLazyProxySettings;
        $this->cgLazyMethods = true;
    }

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
    protected $cgProxifiedObjectCreator = null;

    /**
     * @var array|null
     */
    protected $cgProxifiedObjectConstructorParameters = null;

    /**
     * @var \ClassGenerator\GeneratorAggregator
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
     * @param mixed                               $proxifiedObjectCreator proxifiedObjectCreator or object itself
     * @param \ClassGenerator\GeneratorAggregator $lazyProxyCreator
     * @param boolean                             $lazyMethods
     */
    static public function cgGet($proxifiedObjectCreator, $lazyProxyCreator = null, $lazyMethods = true)
    {
        $ref = new \ReflectionClass('{{newClass}}');
        $that = $ref->newInstanceWithoutConstructor();

        if ($proxifiedObjectCreator instanceof \Closure) {
            $that->cgProxifiedObjectCreator = $proxifiedObjectCreator;
        } else {
            $that->cgProxifiedObject = $proxifiedObjectCreator;
        }

        if ($lazyProxyCreator) {
            $that->cgLazyProxyCreator = $lazyProxyCreator;
        } else {
            $that->cgLazyProxyCreator = \ClassGenerator\Autoloader::getInstance()->getGenerator();;
        }

        $that->cgLazyProxySettings = static::$defaultLazyProxySettings;
        $that->cgLazyMethods = $lazyMethods;

        return $that;
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
        $this->cgLazyProxyCreator = \ClassGenerator\Autoloader::getInstance()->getGenerator();
    }

    <?php if (in_array('Serializable', class_implements($baseClass))) { ?>
    function serialize() {
        if ($this->cgProxifiedObject === null) {
            $this->cgProxifiedObject = call_user_func($this->cgProxifiedObjectCreator);
        }

        if ($this->cgLazyProxySettings === self::$defaultLazyProxySettings) {
            return serialize(array($this->cgProxifiedObject, $this->cgLazyMethods));
        } else {
            return serialize(array($this->cgProxifiedObject, $this->cgLazyMethods, $this->cgLazyProxySettings));
        }
    }

    function unserialize($data) {
        $data = unserialize($data);
        $this->cgProxifiedObject = $data[0];
        $this->cgLazyMethods = $data[1];
        if (isset($data[2])) {
            $this->cgLazyProxySettings = $data[1];
        } else {
            $this->cgLazyProxySettings = self::$defaultLazyProxySettings;
        }
    }
    <?php } ?>

    {{method}}
    <?php if (in_array('Serializable', class_implements($baseClass)) && in_array($methodName, array('serialize', 'unserialize'))) continue; ?>
    <?php if (in_array($methodName, array("__clone", "cgGetProxifiedObject", "__sleep", "__wakeup"))) continue; ?>
    {{$reflectionMethod->getDocComment() . "\n"}}
    function {{methodName}}({{parametersDefinition}}){{returnType}}
    {
        if ($this->cgLazyMethods && $this->cgLazyProxySettings['{{methodName}}']['isLazyEvaluated']) {
            $closure = function () <?php echo $parameters ? "use ($parametersList) " : ""; ?> {
                if ($this->cgProxifiedObject === null) {
                    if ($this->cgProxifiedObjectConstructorParameters === null) {
                        $this->cgProxifiedObject = call_user_func($this->cgProxifiedObjectCreator);
                    } else {
                        $ref = new \ReflectionClass('{{baseClass}}');
                        $this->cgProxifiedObject = $ref->newInstanceArgs($this->cgProxifiedObjectConstructorParameters);
                    }

                    if (!(is_object($this->cgProxifiedObject) && $this->cgProxifiedObject instanceof \{{baseClass}})) {
                        throw new \ClassGenerator\Exceptions\Proxy('Proxy is not instanceof \{{baseClass}}');
                    }
                }

                {{ret}}$this->cgProxifiedObject->{{methodName}}({{parameters}});<?php echo $ret ? '':'return;'; ?>
            };

            {{ret}}$this->cgLazyProxyCreator->lazy($closure->bindTo($this, 'static'), $this->cgLazyProxySettings['{{methodName}}']['class'], $this->cgLazyProxySettings['{{methodName}}']['isLazyMethods']);<?php echo $ret ? '':'return;'; ?>
        }

        if ($this->cgProxifiedObject === null) {
            if ($this->cgProxifiedObjectConstructorParameters === null) {
                $this->cgProxifiedObject = call_user_func($this->cgProxifiedObjectCreator);
            } else {
                $ref = new \ReflectionClass('{{baseClass}}');
                $this->cgProxifiedObject = $ref->newInstanceArgs($this->cgProxifiedObjectConstructorParameters);
            }

            if (!(is_object($this->cgProxifiedObject) && $this->cgProxifiedObject instanceof \{{baseClass}})) {
                throw new \ClassGenerator\Exceptions\Proxy('Proxy is not instanceof \{{baseClass}}');
            }
        }

        if ($this->cgLazyMethods && $this->cgLazyProxySettings['{{methodName}}']['isLazyMethods']) {
            {{ret}}$this->cgLazyProxyCreator->lazyMethods($this->cgProxifiedObject->{{methodName}}({{parameters}}));
        } else {
            {{ret}}$this->cgProxifiedObject->{{methodName}}({{parameters}});
        }
    }

    {{\method}}
}
