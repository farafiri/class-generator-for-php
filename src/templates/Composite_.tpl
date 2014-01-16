<?php echo $newClassNamespace ? 'namespace ' . $newClassNamespace . ';' : ''; ?>

class {{newClassName}} extends \{{baseClass}} implements \{{generatorNamespace}}\Interfaces\Composite
{
    /**
     * @var \{{baseClass}}[]
     */
    protected $cgChildren;

    /**
     * @param \{{baseClass}}[] $children
     */
    public function __construct(array $children = array())
    {
        $this->cgChildren = $children;
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

    {{method}}
    <?php if (in_array($methodName, array('__clone'))) continue; ?>
    {{$reflectionMethod->getDocComment() . "\n"}}
    function {{methodName}}({{parametersDefinition}})
    {
        $result = null;
        foreach($this->cgChildren as $child) {
            $childResult = $child->{{methodName}}({{parameters}});
            if ($childResult && !$result || $childResult !== null && $result === null) {
                $result = $childResult;
            }
        }

        return $result;
    }

    {{\method}}
}