<?php echo $newClassNamespace ? 'namespace ' . $newClassNamespace . ';' : ''; ?>

<?php $nullObjectClassName = $generator->getGeneratorAggregator()->getNewClassNameFor($baseClass, 'nullObject'); ?>

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
     * @var boolean
     */
    protected $cgBehaveLikeNullObject = false;

    /**
     *
     */
    protected $cgReleaseEvents = array();

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
     * @param callable $event
     */
    public function cgAddReleaseEvent($event) {
        $this->cgReleaseEvents[] = $event;
    }

    /**
     * @param callable $event
     */
    public function cgRemoveReleaseEvent($eventToRemove) {
        foreach($this->cgReleaseEvents as $key => $event) {
            if ($event === $eventToRemove) {
                unset($this->cgReleaseEvents[$key]);
            }
        }
    }

    /**
     * @param boolean $behaveLikeNullObject
     */
    public function cgSetBehaveLikeNullObject($behaveLikeNullObject)
    {
        $this->cgBehaveLikeNullObject = $behaveLikeNullObject;
        if (!$this->cgIsReferenceValid) {
            if ($behaveLikeNullObject) {
                $this->cgReferencedObject = new \{{nullObjectClassName}}();
            } else {
                $this->cgReferencedObject = null;
            }
        }
    }

    /**
     * @return boolean
     */
    public function cgGetBehaveLikeNullObject()
    {
        return $this->cgBehaveLikeNullObject;
    }

    public function cgRelease()
    {
        foreach($this->cgReleaseEvents as $event) {
            call_user_func($event, $this);
        };

        $this->cgReferencedObject = null;
        $this->cgIsReferenceValid = false;
        if ($this->cgBehaveLikeNullObject) {
            $this->cgReferencedObject = new \{{nullObjectClassName}}();
        }
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
                    $softReference->cgRelease();
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
        if ($this->cgReferencedObject) {
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
        if (!$this->cgReferencedObject) {
            throw new \ClassGenerator\Exceptions\Proxy("Attempt to serialize invalid reference");
        }

        return array('cgReferencedObject', 'cgIsReferenceValid', 'cgBehaveLikeNullObject');
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

    <?php if (in_array('Serializable', class_implements($baseClass))) { ?>
    function serialize()
    {
        if (!$this->cgReferencedObject) {
            throw new \ClassGenerator\Exceptions\Proxy("Attempt to call serialize on invalid reference");
        }

        return serialize(array($this->cgIsReferenceValid, $this->cgBehaveLikeNullObject, $this->cgReferencedObject));
    }

    function unserialize($data)
    {
        $data = unserialize($data);
        $this->cgIsReferenceValid = $data[0];
        $this->cgBehaveLikeNullObject = $data[1];
        $this->cgReferencedObject = $data[2];

        if ($this->cgIsReferenceValid) {
            $this->cgIsHardReference = true;
            self::$cgReferencesCounter[spl_object_hash($this->cgReferencedObject)] = 1;
        } else {
            $this->cgIsHardReference = false;
        }
    }
    <?php } ?>

    {{method}}
    <?php if (in_array('Serializable', class_implements($baseClass)) && in_array($methodName, array('serialize', 'unserialize'))) continue; ?>
    <?php if (in_array($methodName, array("__clone", "__sleep", "__wakeup"))) continue; ?>
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
