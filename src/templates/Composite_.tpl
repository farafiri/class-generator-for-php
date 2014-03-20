<?php echo $newClassNamespace ? 'namespace ' . $newClassNamespace . ';' : ''; ?>

class {{newClassName}} <?php
$interfaces = '\\' . $generatorNamespace . '\\Interfaces\\Generated, \\' . $generatorNamespace . '\\Interfaces\\Composite';
if (interface_exists($baseClass)) {
    echo 'implements \\' . $baseClass . ', ' . $interfaces;
} else {
    echo 'extends \\' . $baseClass . ' implements ' . $interfaces;
} ?>
{
    /**
     * @var \{{baseClass}}[]
     */
    protected $cgChildren;

    <?php if (in_array('Iterator', class_implements($baseClass)) || $baseClass == 'Iterator') { ?>
    protected $cgIterator;
    <?php } ?>

    /**
     * @param \{{baseClass}}[] $children
     */
    public function __construct(array $children = array())
    {
        $this->cgChildren = $children;
        <?php if (in_array('Iterator', class_implements($baseClass)) || $baseClass == 'Iterator') { ?>
        $this->cgIterator = new \ClassGenerator\Utils\ConsecutiveIteratorsIterator(new \ArrayIterator($this->cgChildren));
        <?php } ?>
    }

    /**
     * @return \{{baseClass}}[]
     */
    public function cgGetChildren()
    {
        return $this->cgChildren;
    }

    /**
     * @param \{{baseClass}}[] $children
     */
    public function cgSetChildren($children)
    {
        $this->cgChildren = $children;
    }

    /**
     * @param \{{baseClass}}[]|\{{baseClass}} $children
     */
    public function cgAddChildren($children)
    {
        if (!is_array($children)) {
            if (!in_array($children, $this->cgChildren, true)) {
                $this->cgChildren[] = $children;
            }

            return null;
        }

        $hashes = array();
        foreach($this->cgChildren as $child) {
            $hashes[spl_object_hash($child)] = true;
        }

        foreach($children as $child) {
            if (empty($hashes[spl_object_hash($child)])) {
                $this->cgChildren[] = $child;
            }
        }
    }

    /**
     * @param \{{baseClass}}[]|\{{baseClass}}|\Closure $children
     *
     * @return \{{baseClass}}[] collection of removed elements
     */
    public function cgRemoveChildren($children)
    {
        if ($children instanceof \Closure) {
            $callback = $children;
        } else {
            if (!is_array($children)) {
                $children = array($children);
            }

            $hashes = array();
            foreach($children as $child) {
                $hashes[spl_object_hash($child)] = true;
            }

            $callback = function ($x) use ($hashes) {
                return isset($hashes[spl_object_hash($x)]);
            };
        }

        $children = array(array(), array());
        foreach($this->cgChildren as $child) {
            $children[$callback($child)][] = $child;
        }

        $this->cgChildren = $children[false];
        return $children[true];
    }

    /**
     * @param string  $className
     * @param boolean $returnNullIfEmpty if you prefer null than empty Composite
     *
     * @return \{{baseClass}} composite containing subset of $this->children where elements are instances of $className
     */
    public function cgGetComposite($className, $returnNullIfEmpty = false)
    {
        $children = array();
        foreach($this->cgChildren as $child) {
            if ($child instanceof $className) {
                $children[] = $child;
            }
        }

        if ($returnNullIfEmpty && $children === array()) {
            return null;
        }

        $compositeClassName = \ClassGenerator\Autoloader::getInstance()->getGenerator()->getNewClassNameFor($className, 'composite');
        return new $compositeClassName($children);
    }

    public function __clone()
    {
        foreach($this->cgChildren as &$child) {
            $child = clone $child;
        }
    }

    public function __sleep()
    {
        return array('cgChildren');
    }

    <?php if (method_exists($baseClass, '__wakeup')) { ?>
    public function __wakeup()
    {
    }
    <?php } ?>

    <?php if (in_array('Iterator', class_implements($baseClass)) || $baseClass == 'Iterator') { ?>
    public function rewind() {
        return $this->cgIterator->rewind();
    }

    public function current() {
        return $this->cgIterator->current();
    }

    public function key() {
        return $this->cgIterator->key();
    }

    public function next() {
        return $this->cgIterator->next();
    }

    public function valid() {
        return $this->cgIterator->valid();
    }
    <?php } ?>
    <?php if (in_array('InnerIterator', class_implements($baseClass)) || $baseClass == 'InnerIterator') { ?>
    public function valid() {
        return $this->cgIterator->getInnerIterator();
    }

    <?php } ?>
    <?php if (in_array('RecursiveIterator', class_implements($baseClass)) || $baseClass == 'RecursiveIterator') { ?>
    function getChildren() {
        return $this->cgIterator->getChildren();
    }

    function hasChildren() {
        return $this->cgIterator->hasChildren();
    }

    <?php } ?>

    <?php if (in_array('IteratorAggregate', class_implements($baseClass)) || $baseClass == 'IteratorAggregate') { ?>
    function getIterator() {
        return new \ClassGenerator\Utils\ConsecutiveIteratorsIterator(new \ArrayIterator($this->cgChildren));
    }

    <?php } ?>

    <?php if (in_array('Serializable', class_implements($baseClass))) { ?>
    function serialize() {
        return serialize($this->cgChildren);
    }

    function unserialize($data) {
        return $this->cgChildren = unserialize($data);
    }
    <?php } ?>


    {{method}}
    <?php if (in_array('Serializable', class_implements($baseClass)) && in_array($methodName, array('serialize', 'unserialize'))) continue; ?>
    <?php if ((in_array('IteratorAggregate', class_implements($baseClass)) || $baseClass == 'IteratorAggregate') && in_array($methodName, array('getIterator'))) continue; ?>
    <?php if ((in_array('OuterIterator', class_implements($baseClass)) || $baseClass == 'OuterIterator') && in_array($methodName, array('getInnerIterator'))) continue; ?>
    <?php if ((in_array('RecursiveIterator', class_implements($baseClass)) || $baseClass == 'RecursiveIterator') && in_array($methodName, array('getChildren', 'hasChildren'))) continue; ?>
    <?php if ((in_array('Iterator', class_implements($baseClass)) || $baseClass == 'Iterator') && in_array($methodName, array('rewind', 'current', 'key', 'next', 'valid'))) continue; ?>
    <?php if (in_array($methodName, array('__clone', '__sleep', '__wakeup'))) continue; ?>
    {{$reflectionMethod->getDocComment() . "\n"}}
    function {{methodName}}({{parametersDefinition}})
    {
        $collection = array();
        foreach($this->cgChildren as $child) {
            $collection[] = $child->{{methodName}}({{parameters}});
        }

        <?php
            $compositeFunction = 'or';
            if ($x = \ClassGenerator\Utils\Utils::getDocAttribute($reflectionMethod, 'composite')) {
                $compositeFunction = $x;
            } else {
                if (\ClassGenerator\Utils\Utils::returnsArrayOrNull($reflectionMethod)) {
                    $compositeFunction = 'merge';
                } elseif (\ClassGenerator\Utils\Utils::returnedObjectClassName($reflectionMethod, true)) {
                    $returnClassName = \ClassGenerator\Utils\Utils::returnedObjectClassName($reflectionMethod, true);
                    $canReturnNull = !(\ClassGenerator\Utils\Utils::returnedObjectClassName($reflectionMethod, false));
                    if (in_array('ClassGenerator\Interfaces\Composite', class_implements($returnClassName))) {
                        $compositeClassName = $returnClassName;
                    } else {
                        $compositeClassName = \ClassGenerator\Autoloader::getInstance()->getGenerator()->getNewClassNameFor($returnClassName, 'composite');
                    }

                    $strCanReturnNull = $canReturnNull ? 'true' : 'false';
                    $compositeFunction = "composite('$compositeClassName', $strCanReturnNull)";
                }
            }

            $bracketPosition = strpos($compositeFunction, '(');
            if ($bracketPosition) {
                $fn = substr($compositeFunction, 0, $bracketPosition + 1) . '$collection, ' . substr($compositeFunction, $bracketPosition + 1);
            } else {
                $fn = $compositeFunction . '($collection)';
            }

            $fn = ucfirst($fn);
        ?>
        return \ClassGenerator\Utils\AggregateFunctions::af{{fn}};
    }

    {{\method}}
}
