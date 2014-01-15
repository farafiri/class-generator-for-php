<?php echo $newClassNamespace ? 'namespace ' . $newClassNamespace . ';' : ''; ?>

class {{newClassName}} <?php
$interfaces = '\\' . $generatorNamespace . '\\Interfaces\\Generated, \\SplSubject';
if (interface_exists($baseClass)) {
    echo 'implements \\' . $baseClass . ', ' . $interfaces;
} else {
    echo 'extends \\' . $baseClass . ' implements ' . $interfaces;
} ?>
{
<?php if (!property_exists($baseClass, 'observers')) { echo "\n"; ?>
    /**
     * @var \SplObjectStorage
     */
    protected $cgObservers;

    public function __construct({{parametersDefinition}})
    {
        $this->cgObservers = new \SplObjectStorage();
        <?php if ($parameters !== null) { echo "\n"; ?>
        parent::__construct({{parameters}});
        <?php } ?>
    }

<?php } ?>
<?php if (!method_exists($baseClass, 'attach')) { echo "\n"; ?>
    /**
     * @see \SplSubject::attach
     *
     * @param \SplObserver $observer
     */
    public function attach(\SplObserver $observer) {
        $this->cgObservers->attach($observer);
    }
<?php } ?>
<?php if (!method_exists($baseClass, 'detach')) { echo "\n";?>
    /**
     * @see \SplSubject::detach
     *
     * @param \SplObserver $observer
     */
    public function detach(\SplObserver $observer) {
        $this->cgObservers->detach($observer);
    }
<?php } ?>
<?php if (!method_exists($baseClass, 'notify')) { ?>
    /**
     * @see \SplSubject::detach
     *
     * @param string|null $methodName
     */
    public function notify($methodName = null) {
        if (substr($methodName, 0, 3) === 'get' ||
            substr($methodName, 0, 2) === 'is') return null;

        foreach ($this->cgObservers as $observer) {
            $observer->update($this);
        }
    }
<?php } ?>

    {{method}}
<?php if (in_array($methodName, array('notify', 'attach', 'detach', '__toString', '__get'))) continue; ?>
    {{$reflectionMethod->getDocComment() . "\n"}}
    function {{methodName}}({{parametersDefinition}})
    {
        $result = parent::{{methodName}}({{parameters}});
        $this->notify('{{methodName}}'<?php echo $parameters ? (', ' . $parameters) : ''; ?>);
        return $result;
    }

    {{\method}}
}