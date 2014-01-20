<?php

/** @noinspection PhpUndefinedNamespaceInspection */
use ClassGenerator\tests\ResourceClasses;

class ClassGeneratorTest extends BaseTest
{
    public function testCompound()
    {
        $x = new ResourceClasses\X(100);
        $d = new ResourceClasses\MethodOverridedDecoratorForX(array('getA' => function () {return parent::getA() + 1;}), $x);

        $this->assertEquals(100, $x->getA());
        $this->assertEquals(101, $d->getA());
    }

    public function testAbilityToRestrictNamespacesWhereGeneratorWork()
    {
        self::$generator->setAcceptedNamespaces(array('ClassGenerator\tests\ResourceClasses'));

        $this->assertFalse(class_exists('ClassGenerator\tests\ResourceClasses2\NullX'));

        self::$generator->setAcceptedNamespaces(array('ClassGenerator\tests\ResourceClasses', 'ClassGenerator\tests\ResourceClasses2'));

        $this->assertTrue(class_exists('ClassGenerator\tests\ResourceClasses2\NullX2'));

        self::$generator->setAcceptedNamespaces(array(''));
    }
}
