<?php

/** @noinspection PhpUndefinedNamespaceInspection */
use ClassGenerator\tests\ResourceClasses;

class LazyConstructor extends BaseTest
{
    public function testLazyConstructor()
    {
        $x = new ResourceClasses\LazyConstructorX(101, 202);

        $this->assertEquals(0, $x->a);
        $this->assertEquals(0, $x->b);

        $this->assertEquals(101, $x->getA());
        $this->assertEquals(202, $x->getB());

        $this->assertEquals(101, $x->a);
        $this->assertEquals(202, $x->b);
    }

    public function testCloneLazyConstructor()
    {
        $std = new \stdClass();
        $x1 = new ResourceClasses\LazyConstructorX($std, $std);
        $x2 = clone $x1;

        $this->assertNotSame($x1->getA(), $x2->getA());
        $this->assertEquals($x1->getA(), $x2->getA());
        $this->assertSame($x1->getB(), $x2->getB());
    }

    public function testSleepWakeupLazyConstructor()
    {
        $x1 = new ResourceClasses\LazyConstructorX(new \stdClass(), 4);
        $x2 = unserialize(serialize($x1));

        $this->assertEquals($x1, $x2);
    }

    public function testSerializeWithLazyConstructor()
    {
        $x = new ResourceClasses\LazyConstructorSerialize(1, 2);

        $this->assertEquals($x, unserialize(serialize($x)));
    }

    /**
     * @requires PHP 5.6
     */
    public function testVariadics() {
        $this->assertTrue(class_exists('ClassGenerator\tests\ResourceClasses\LazyConstructorVariadic'));

        $x = new ResourceClasses\LazyConstructorVariadic('.', ',');
        $this->assertNull($x->postfixes);
        $this->assertEquals('.a|,b', $x->join('|', 'a', 'b'));
        $this->assertEquals(array('.', ','), $x->postfixes);
    }
} 