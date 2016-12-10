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

    <?php if (!method_exists($baseClass, '__sleep')) { ?>
    public function __sleep()
    {
        return array_diff(array_keys(get_object_vars($this)), array('cgObservers'));
    }
    <?php } ?>

    public function __wakeup()
    {
        $this->cgObservers = new \SplObjectStorage();
        <?php if (method_exists($baseClass, '__wakeup')) { ?>
        parent::__wakeup();
        <?php } ?>
    }

    <?php if (in_array('Serializable', class_implements($baseClass))) { ?>
    function unserialize($data) {
        $this->cgObservers = new \SplObjectStorage();
        parent::unserialize($data);
    }
    <?php } ?>

    {{method}}
<?php if (in_array('Serializable', class_implements($baseClass)) && in_array($methodName, array('serialize', 'unserialize'))) continue; ?>
<?php if (in_array($methodName, array('notify', 'attach', 'detach', '__toString', '__get', '__wakeup', '__sleep'))) continue; ?>
    {{$reflectionMethod->getDocComment() . "\n"}}
    function {{methodName}}({{parametersDefinition}}){{returnType}}
    {
        $result = parent::{{methodName}}({{parameters}});
        $this->notify('{{methodName}}'<?php echo $parameters ? (', ' . $parameters) : ''; ?>);
        {{ret}}$result;
    }

    {{\method}}
}