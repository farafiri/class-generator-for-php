<?php echo $newClassNamespace ? 'namespace ' . $newClassNamespace . ';' : ''; ?>

class {{newClassName}} extends <?php echo '\\' . $generator->getGeneratorAggregator()->getNewClassNameFor($baseClass, 'decorator'); ?>
{
    <?php echo $reflectionMethod ? $reflectionMethod->getdocComment() : ''; ?>
    public function __construct({{parametersDefinition}})
    {
        $this->cgDecorated = new \{{baseClass}}({{parameters}});
    }
}