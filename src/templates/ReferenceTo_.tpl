namespace {{newClassNamespace}};

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
        $reference = new self($this->cgReferencedObject);
        $reference->cgSetIsHardReference(false);
        return $reference;
    }

    /**
     * @return \{{newClass}}
     */
    public function cgGetHardReference()
    {
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
            return false;
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

    {{method}}
    {{$reflectionMethod->getDocComment() . "\n"}}
    function {{methodName}}({{parametersDefinition}})
    {
        return $this->cgReferencedObject->{{methodName}}({{parameters}});
    }

    {{\method}}
}