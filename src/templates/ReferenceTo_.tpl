<?php echo $newClassNamespace ? 'namespace ' . $newClassNamespace . ';' : ''; ?>

class {{newClassName}} <?php
$interfaces = '\\' . $generatorNamespace . '\\Interfaces\\Generated, \\' . $generatorNamespace . '\\Interfaces\\Reference';
if (interface_exists($baseClass)) {
    echo 'implements \\' . $baseClass . ', ' . $interfaces;
} else {
    echo 'extends \\' . $baseClass . ' implements ' . $interfaces;
} ?>
{
    /**
     * @var integer[]
     */
    static protected $cgReferencesCounter = array();

    /**
     * @var \{{baseClass}}[][]
     */
    static protected $cgSoftReferences = array();

    /**
     * @var \{{baseClass}}
     */
    protected $cgRefernecedObject;

    /**
     * @var boolean
     */
    protected $cgIsHardReference = true;

    /**
     * @var boolean
     */
    protected $cgIsReferenceValid = true;

    /**
     * @param \{{baseClass}} $object
     */
    public function __construct($object)
    {
        $this->cgReferencedObject = $object;
        $hash = spl_object_hash($this->cgReferencedObject);
        self::$cgReferencesCounter[$hash] = isset(self::$cgReferencesCounter[$hash]) ? (self::$cgReferencesCounter[$hash] + 1) : 1;
    }

    /**
     * @return \{{baseClass}}|null
     */
    public function cgGetProxifiedObject()
    {
        return $this->cgReferencedObject;
    }

    /**
     * @return boolean
     */
    public function cgIsHardReference()
    {
        return $this->cgIsHardReference;
    }

    /**
     * @return boolean
     */
    public function cgIsReferenceValid()
    {
        return $this->cgIsReferenceValid;
    }

    /**
     * decrease reference counter and if it reach 0 then relase object and make soft references invalid
     *
     * @param string $hash
     */
    protected static function cgDecreaseReferenceCounter($hash)
    {
        if (!--self::$cgReferencesCounter[$hash]) {
            unset(self::$cgReferencesCounter[$hash]);
            if (isset(self::$cgSoftReferences[$hash])) {
                foreach(self::$cgSoftReferences[$hash] as $softReference) {
                    $softReference->cgReferencedObject = null;
                    $softReference->cgIsReferenceValid = false;
                }
                unset(self::$cgSoftReferences[$hash]);
            }
        }
    }

    /**
     * @return \{{newClass}}
     */
    public function cgGetWeakReference()
    {
        if (!$this->cgIsReferenceValid) {
            throw new \ClassGenerator\Exceptions\Proxy("Attempt to call cgGetWeakReference on invalid reference");
        }

        $reference = new self($this->cgReferencedObject);
        $reference->cgSetIsHardReference(false);
        return $reference;
    }

    /**
     * @return \{{newClass}}
     */
    public function cgGetHardReference()
    {
        if (!$this->cgIsReferenceValid) {
            throw new \ClassGenerator\Exceptions\Proxy("Attempt to call cgGetHardReference on invalid reference");
        }

        $reference = new self($this->cgReferencedObject);
        return $reference;
    }

    /**
     * you can change weak reference to hard reference and hard reference into weak reference
     *
     * @param boolean $isHardReference
     */
    public function cgSetIsHardReference($isHardReference)
    {
        if (!$this->cgIsReferenceValid) {
            throw new \ClassGenerator\Exceptions\Proxy("Attempt to call cgSetIsHardReference on invalid reference");
        }

        $isHardReference = (bool) $isHardReference;
        if ($isHardReference === $this->cgIsHardReference) {
            return ;
        }

        if ($isHardReference) {
            $this->cgIsHardReference = true;
            $hash = spl_object_hash($this->sgReferencedObject);
            self::$cgReferencesCounter[$hash]++;
            unset(self::$cgSoftReferences[$hash][spl_object_hash($this)]);

        } else {
            $this->cgIsHardReference = false;
            $hash = spl_object_hash($this->cgReferencedObject);
            self::$cgSoftReferences[$hash][spl_object_hash($this)] = $this;
            self::cgDecreaseReferenceCounter($hash);
        }
    }

    /**
     * @param \{{baseClass}} $object
     *
     * @return boolean
     */
    public function cgIsReferenceEqualTo($object)
    {
        if (!$this->cgIsReferenceValid) {
            throw new \ClassGenerator\Exceptions\Proxy("Attempt to call cgIsReferenceEqualTo on invalid reference");
        }

        if ($object instanceof \ClassGenerator\Interfaces\Reference) {
            return $this->cgReferencedObject === $object->cgReferencedObject;
        } else {
            return $this->cgReferencedObject === $object;
        }
    }

    function __destruct()
    {
        if ($this->cgIsHardReference) {
            self::cgDecreaseReferenceCounter(spl_object_hash($this->cgReferencedObject));
        }
    }

    function __clone()
    {
        if ($this->cgIsReferenceValid) {
            $this->cgReferencedObject = clone $this->cgReferencedObject;
            $this->cgIsHardReference = true;
            $hash = spl_object_hash($this->cgReferencedObject);
            self::$cgReferencesCounter[$hash] = 1;
        } else {
            throw new \ClassGenerator\Exceptions\Proxy("Attempt to clone invalid reference");
        }
    }

    public function __sleep()
    {
        return array('cgReferencedObject', 'cgIsReferenceValid');
    }

    public function __wakeup()
    {
        if ($this->cgIsReferenceValid) {
            $this->cgIsHardReference = true;
            self::$cgReferencesCounter[spl_object_hash($this->cgReferencedObject)] = 1;
        } else {
            $this->cgIsHardReference = false;
        }
    }

    {{method}}
    <?php if (in_array($methodName, array("__clone"))) continue; ?>
    {{$reflectionMethod->getDocComment() . "\n"}}
    function {{methodName}}({{parametersDefinition}})
    {
        if (!$this->cgReferencedObject) {
            throw new \ClassGenerator\Exceptions\Proxy("Attempt to call {{methodName}} on invalid reference");
        }

        return $this->cgReferencedObject->{{methodName}}({{parameters}});
    }

    {{\method}}
}
