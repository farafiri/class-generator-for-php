<?php

/** @noinspection PhpUndefinedNamespaceInspection */
use ClassGenerator\tests\ResourceClasses;

class MethodOverridingTest extends BaseTest
{
    public function testWithMethodOverriding()
    {
        $x = new ResourceClasses\MethodOverridedX(array('getA' => function() { return $this->a + 1;}));

        $this->assertEquals(11, $x->getA());

        $x->setA(30);
        $this->assertEquals(31, $x->getA());
    }

    public function testWithMethodOverridingWithOneClosureOverridesAllMethods()
    {
        $x = new ResourceClasses\MethodOverridedX(function($a = '') { return "abc" . $a;});

        $this->assertEquals("abc", $x->getA());
        $this->assertEquals("abcd", $x->setB("d"));
    }

    public function testWithMethodOverridingWithConstructorParameters()
    {
        $x = new ResourceClasses\MethodOverridedX(array(), 123, 234);

        $this->assertEquals(123, $x->getA());
        $this->assertEquals(234, $x->getB());
    }
} 