<?php echo $newClassNamespace ? 'namespace ' . $newClassNamespace . ';' : ''; ?>

class {{newClassName}} <?php
$interfaces = '\\' . $generatorNamespace . '\\Interfaces\\Generated, \\' . $generatorNamespace . '\\Interfaces\\Decorator';
foreach($extraInterfaces as $extraInterface) {
    $interfaces .= ', \\' . $extraInterface;
}

if (interface_exists($baseClass)) {
    $addIteratorAggregatorInterface = (in_array('Traversable', class_implements($baseClass)) || $baseClass === 'Traversable') &&
                                      !(in_array('Iterator', class_implements($baseClass)) || $baseClass === 'Iterator') &&
                                      !(in_array('IteratorAggregate', class_implements($baseClass)) || $baseClass === 'IteratorAggregate');

    if ($addIteratorAggregatorInterface) {
        echo 'implements \\IteratorAggregate , ' . $interfaces;
    } else {
        echo 'implements \\' . $baseClass . ', ' . $interfaces;
    }
} else {
    $addIteratorAggregatorInterface = false;
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

    <?php if ($addIteratorAggregatorInterface) { ?>
    /**
     * @return \Traversable
     */
    public function getIterator() {
        if ($this->cgDecorated instanceof \Iterator) {
            return $this->cgDecorated;
        } else {
            return $this->cgDecorated->getIterator();
        }
    }
    <?php } ?>

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
     * @param \ClassGenerator\BaseDecorator $decorator
     */
    public function cgDecorateWith(\ClassGenerator\BaseDecorator $decorator)
    {
        $decorator->cgDecorate($this);
    }

    public function cgRedecorate(\ClassGenerator\BaseDecorator $decorator = null) {
        if ($decorator) {
            $this->cgDecorateWith($decorator);
        }

        return \ClassGenerator\Autoloader::getInstance()->getGenerator()->redecorate($this);
    }

    /**
     * @param \{{baseClass}}|\ClassGenerator\BaseDecorator $decorated
     */
    public function cgSetDecorated($decorated)
    {
        if ($decorated instanceof \{{baseClass}} ||
            $decorated instanceof \ClassGenerator\BaseDecorator ||
            $decorated instanceof \ClassGenerator\Interfaces\Redirect) {
            $this->cgDecorated = $decorated;
        } else {
            throw new \InvalidArgumentException('Argument to {{newClass}}::cgSetDecorated must be instanceof {{baseClass}} or \ClassGenerator\BaseDecorator');
        }
    }

    /**
     * @param \ClassGenerator\Interfaces\Decorator|\Closure|string $decoratorMatcher
     *
     * @return self
     */
    public function cgRemoveDecorator($decoratorMatcher)
    {
        $object = $this;
        while ($object->cgGetDecorated() instanceof \ClassGenerator\Interfaces\Decorator) {
            if ($this->cgMatchDecorator($object->cgGetDecorated(), $decoratorMatcher)) {
                $object->cgSetDecorated($object->cgGetDecorated()->cgGetDecorated());
            } else {
                $object = $object->cgGetDecorated();
            }
        }

        return $this;
    }

    /**
     * @param \ClassGenerator\Interfaces\Decorator|\Closure|string $decoratorMatcher
     *
     * @return boolean
     */
    public function cgHasDecorator($decoratorMatcher)
    {
        $object = $this;

        while ($object->cgGetDecorated() instanceof \ClassGenerator\Interfaces\Decorator) {
            if ($this->cgMatchDecorator($object->cgGetDecorated(), $decoratorMatcher)) {
                return true;
            } else {
                $object = $object->cgGetDecorated();
            }
        }

        return false;
    }

    /**
     * @param \ClassGenerator\Interfaces\Decorator                 $decorator
     * @param \ClassGenerator\Interfaces\Decorator|\Closure|string $decoratorMatcher
     *
     * @return boolean
     */
    protected function cgMatchDecorator($decorator, $decoratorMatcher)
    {
        if (is_string($decoratorMatcher)) {
            return is_a($decorator, $decoratorMatcher);
        } elseif ($decoratorMatcher instanceof \ClassGenerator\Interfaces\Decorator) {
            if (get_class($decoratorMatcher) === get_class($decorator)) {
                $decoratorMatcher = clone $decoratorMatcher;
                $decoratorMatcher->cgSetDecorated($decorator->cgGetDecorated());
                return $decoratorMatcher == $decorator;
            }

            return false;
        } elseif ($decoratorMatcher instanceof \Closure) {
            return $decoratorMatcher($decorator);
        } else {
            throw new \InvalidArgumentException("Argument provided for cgHasDecorator/cgRemoveDecorator should be string or
                                                 instanceof Closure or instanceof ClassGenerator\Interfaces\Decorator");
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

    <?php if (in_array('Serializable', class_implements($baseClass))) { ?>
    function serialize() {
        return serialize($this->cgDecorated);
    }

    function unserialize($data) {
        $this->cgDecorated = unserialize($data);
    }
    <?php } ?>

{{method}}
    <?php if (in_array('Serializable', class_implements($baseClass)) && in_array($methodName, array('serialize', 'unserialize'))) continue; ?>
    <?php if (in_array($methodName, array('__call', '__clone', '__sleep', '__wakeup'))) continue; ?>
    {{$reflectionMethod->getDocComment() . "\n"}}
    function {{methodName}}({{parametersDefinition}})
    {
        return $this->cgDecorated->{{methodName}}({{parameters}});
    }

{{\method}}
}
