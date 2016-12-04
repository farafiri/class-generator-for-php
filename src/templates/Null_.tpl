<?php echo $newClassNamespace ? 'namespace ' . $newClassNamespace . ';' : ''; ?>

class {{newClassName}} <?php
$interfaces = '\\' . $generatorNamespace . '\\Interfaces\\Generated, \\' . $generatorNamespace . '\\Interfaces\\NullObject';
if (interface_exists($baseClass)) {
    echo 'implements \\' . $baseClass . ', ' . $interfaces;
} else {
    echo 'extends \\' . $baseClass . ' implements ' . $interfaces;
} ?>
{
    /**
     * we don't want any parameters and actions in constructor of nullObject
     */
    public function __construct()
    {
    }

    public function __clone()
    {
    }

    public function __sleep()
    {
        return array();
    }

    <?php if (method_exists($baseClass, '__wakeup')) { ?>
    public function __wakeup()
    {
    }
    <?php } ?>

{{method}}
    <?php if (in_array($methodName, array("__clone", "__sleep", "__wakeup"))) continue; ?>
    {{$reflectionMethod->getDocComment() . "\n"}}
    function {{methodName}}({{parametersDefinition}}){{returnType}}
    {
        <?php
            $docNullObject = \ClassGenerator\Utils\Utils::getDocAttribute($reflectionMethod, 'nullObject');
            if ($docNullObject) {
                echo $docNullObject . ';';
            } else {
                echo "return ";
                $type = \ClassGenerator\Utils\Utils::getReturnType($reflectionMethod);
                if ($methodName == '__toString') {
                    echo "''";
                } elseif (!$type) {
                    echo "null";
                } elseif (preg_match('/(^|\|)null($|\|)/', $type)) {
                    echo "null";
                } elseif (preg_match('/((^|\|)(array|iterable)|\\[\\])($|\|)/', $type)) {
                    echo "array()";
                } elseif (preg_match('/(^|\|)string($|\|)/', $type)) {
                    echo "''";
                } elseif (preg_match('/(^|\|)(integer|int|float|real|double)($|\|)/', $type)) {
                    echo "0";
                } elseif (preg_match('/(^|\|)true($|\|)/', $type)) {
                    echo "true";
                } elseif (preg_match('/(^|\|)(boolean|bool|false)($|\|)/', $type)) {
                    echo "false";
                } elseif (preg_match('/(^|\|)callable($|\|)/', $type)) {
                    echo 'function() {return null;}';
                } elseif (preg_match('/(^|\|)mixed($|\|)/', $type)) {
                    echo 'null';
                } elseif (preg_match('/[a-zA-Z0-9_\\\\]+/', $type, $match)) {
                    if (substr($match[0], 0, 1) === '\\') {
                        $returnClass = substr($match[0], 1);
                    } else {
                        throw new \ClassGenerator\Exceptions\Generate("phpDoc return for $baseClass::$methodName must be full class name");
                    }
                    echo "new \\" . $generator->getClassName($returnClass) . "()";
                } else {
                    echo "null";
                }
            }
        ?>;
    }

{{\method}}
}
