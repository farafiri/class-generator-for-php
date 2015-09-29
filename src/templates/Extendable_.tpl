<?php echo $newClassNamespace ? 'namespace ' . $newClassNamespace . ';' : ''; ?>

class {{newClassName}} extends <?php echo '\\' . $generator->getGeneratorAggregator()->getNewClassNameFor($baseClass, 'decorator'); ?>
  implements \{{generatorNamespace}}\Interfaces\Extendable
{
    protected $cgTrueExteddable;
    protected $cgLastDecorated;

    public function cgGetLastDecorated() {
        return $this->cgLastDecorated;
    }

    public function cgSetLastDecorated($cgLastDecorated) {
        $this->cgLastDecorated = $cgLastDecorated;
        return $this;
    }

    /**
     * @param \ClassGenerator\BaseDecorator $decorator
     */
    public function cgExtendWith(\ClassGenerator\BaseDecorator $decorator)
    {
        $decorator->cgExtend($this);
    }

    <?php echo $reflectionMethod ? $reflectionMethod->getdocComment() : ''; ?>
    public function __construct({{parametersDefinition}})
    {
        <?php
            $trueExtendableClassName = $generator->getGeneratorAggregator()->getNewClassNameFor($baseClass, 'trueCGExtendable');
            $redirectClassName = $generator->getGeneratorAggregator()->getNewClassNameFor($baseClass, 'redirectCGExtendable');
        ?>
        $this->cgTrueExtendable = new \{{trueExtendableClassName}}({{parameters}});
        $redirect = new \{{redirectClassName}}($this->cgTrueExtendable);
        $this->cgTrueExtendable->cgSetThis($redirect);
        $this->cgLastDecorated = $this;
        $this->cgSetDecorated($redirect);
    }
}