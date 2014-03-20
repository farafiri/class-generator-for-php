<?php

/** @noinspection PhpUndefinedNamespaceInspection */
use ClassGenerator\tests\ResourceClasses;

class LazyTest extends BaseTest
{
    public function testLazyIsInstanceOfLazy()
    {
        $x = new ResourceClasses\LazyX(1, 2);
        $this->assertTrue($x instanceof \ClassGenerator\Interfaces\Lazy);
    }

    public function testLazyProducesInstanceOfLazy()
    {
        $x = new ResourceClasses\LazyX(1, 2);
        $x2 = $x->createAnotherX();
        $this->assertTrue($x2 instanceof \ClassGenerator\Interfaces\Lazy);
    }

    public function testBaseLazy()
    {
        $x = new ResourceClasses\LazyX(1, 2);

        $this->assertEquals(1, $x->getA());
        $this->assertEquals(2, $x->getB());

        $x2 = $x->createAnotherX();

        $this->assertEquals(3, $x2->getA());
        $this->assertEquals(-1, $x2->getB());
    }

    public function testBaseLazyWithExistingProxifiedObject()
    {
        $x = ResourceClasses\LazyX::cgGet(new ResourceClasses\X(1, 2));

        $this->assertEquals(1, $x->getA());
        $this->assertEquals(2, $x->getB());

        $x2 = $x->createAnotherX();

        $this->assertEquals(3, $x2->getA());
        $this->assertEquals(-1, $x2->getB());
    }

    public function testBaseLazyWithClosure()
    {
        $x = ResourceClasses\LazyX::cgGet(function () { return new ResourceClasses\X(1, 2);});

        $this->assertEquals(1, $x->getA());
        $this->assertEquals(2, $x->getB());

        $x2 = $x->createAnotherX();

        $this->assertEquals(3, $x2->getA());
        $this->assertEquals(-1, $x2->getB());
    }

    public function testLazyWithConstructorCallCount()
    {
        $x = new ResourceClasses\LazyX(1, 2);
        ResourceClasses\X::$constructorCount = 0;

        $x2 = $x->createAnotherX()->createAnotherX()->createAnotherX()->createAnotherX();

        $this->assertEquals(0, ResourceClasses\X::$constructorCount);
        $this->assertEquals(4, $x2->getA());
        $this->assertEquals(8, $x2->getB());
        $this->assertEquals(5, ResourceClasses\X::$constructorCount);
    }

    public function testGeneratorLazy()
    {
        $x = static::getGenerator()->lazy(function() {return new ResourceClasses\X(1, 2); }, 'ClassGenerator\tests\ResourceClasses\X', true);
        ResourceClasses\X::$constructorCount = 0;

        $x2 = $x->createAnotherX()->createAnotherX()->createAnotherX()->createAnotherX();

        $this->assertEquals(0, ResourceClasses\X::$constructorCount);
        $this->assertEquals(4, $x2->getA());
        $this->assertEquals(8, $x2->getB());
        //here is difference between testGeneratorLazy() and testLazy() because creating first object is also delayed
        $this->assertEquals(5, ResourceClasses\X::$constructorCount);
    }

    public function testGeneratorLazyMethods()
    {
        $x = static::getGenerator()->lazyMethods(new ResourceClasses\X(1, 2));
        ResourceClasses\X::$constructorCount = 0;

        $x2 = $x->createAnotherX()->createAnotherX()->createAnotherX()->createAnotherX();

        $this->assertEquals(0, ResourceClasses\X::$constructorCount);
        $this->assertEquals(4, $x2->getA());
        $this->assertEquals(8, $x2->getB());
        $this->assertEquals(4, ResourceClasses\X::$constructorCount);
    }

    public function testGetProxifiedObject()
    {
        $x = ResourceClasses\LazyX::cgGet(new ResourceClasses\X(1, 2));
        ResourceClasses\X::$constructorCount = 0;

        $x2 = $x->createAnotherX()->createAnotherX();
        $this->assertEquals(0, ResourceClasses\X::$constructorCount);

        $this->assertFalse($x2->cgGetProxifiedObject() instanceof \ClassGenerator\Interfaces\Lazy);
        $this->assertEquals(2, ResourceClasses\X::$constructorCount);
    }

    public function testLazyWithNoLazyEvaluation()
    {
        $x = ResourceClasses\LazyX2::cgGet(new ResourceClasses\X2(1, 2));

        ResourceClasses\X::$constructorCount = 0;

        $x2 = $x->createAnotherXWithoutLazyEvaluation();
        $this->assertEquals(1, ResourceClasses\X::$constructorCount);
        $this->assertTrue($x2 instanceof \ClassGenerator\Interfaces\Lazy);
    }

    public function testLazyWithNoLazyMethods()
    {
        $x = ResourceClasses\LazyX2::cgGet(new ResourceClasses\X2(1, 2));

        ResourceClasses\X::$constructorCount = 0;

        $x2 = $x->createAnotherXWithoutLazyMethods();
        $this->assertEquals(0, ResourceClasses\X::$constructorCount);

        $x3 = $x2->createAnotherXWithoutLazyMethods();
        $this->assertEquals(2, ResourceClasses\X::$constructorCount);
        $this->assertFalse($x3 instanceof \ClassGenerator\Interfaces\Lazy);
    }

    public function testSleepWakeupOnLazy()
    {
        $x = static::getGenerator()->lazyMethods(new ResourceClasses\X(101, 102));
        $x2 = unserialize(serialize($x));

        $this->assertEquals(101, $x2->getA());
        $this->assertEquals(102, $x2->getB());
    }

    public function testSerializeWithLazy()
    {
        $x = self::$generator->lazy(function() {
            return new ResourceClasses\Serialize(1, 2);
        }, 'ClassGenerator\tests\ResourceClasses\Serialize');

        $this->assertEquals($x->getA(), unserialize(serialize($x))->getA());
        $this->assertEquals($x->getB(), unserialize(serialize($x))->getB());
    }
}
