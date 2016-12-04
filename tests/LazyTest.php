<?php

/** @noinspection PhpUndefinedNamespaceInspection */
use ClassGenerator\tests\ResourceClasses;

class LazyTest extends BaseTest
{
    /**
     * @dataProvider withProvider
     * @_testWith ('ClassGenerator\tests\ResourceClasses\LazyX', 'ClassGenerator\tests\ResourceClasses\X')
     *            ('ClassGenerator\tests\ResourceClasses\LazyXInterface', 'ClassGenerator\tests\ResourceClasses\XInterface')
     *            ('ClassGenerator\tests\ResourceClasses\LazyX7', 'ClassGenerator\tests\ResourceClasses\X7', 'minPhp' => '7.0')
     *            ('ClassGenerator\tests\ResourceClasses\LazyX71', 'ClassGenerator\tests\ResourceClasses\X71', 'minPhp' => '7.1')
     */
    public function testLazyIsInstanceOfLazy($testedClass, $parentClass)
    {
        $x = new $testedClass(1, 2);
        $this->assertTrue($x instanceof $parentClass);
    }

    /**
     * @dataProvider withProvider
     * @_testWith ('ClassGenerator\tests\ResourceClasses\LazyX', 'ClassGenerator\tests\ResourceClasses\LazyX')
     *            ('ClassGenerator\tests\ResourceClasses\LazyXInterface', 'ClassGenerator\tests\ResourceClasses\LazyX')
     *            ('ClassGenerator\tests\ResourceClasses\LazyX7', 'ClassGenerator\tests\ResourceClasses\LazyX7', 'minPhp' => '7.0')
     */
    public function testLazyProducesInstanceOfLazy($testedClass, $expectedClass)
    {
        $x = new $testedClass(1, 2);
        $x2 = $x->createAnotherX();
        $this->assertTrue($x2 instanceof $expectedClass);
    }

    /**
     * @dataProvider withProvider
     * @_testWith ('ClassGenerator\tests\ResourceClasses\LazyX')
     *            ('ClassGenerator\tests\ResourceClasses\LazyX7', 'minPhp' => '7.0')
     *            ('ClassGenerator\tests\ResourceClasses\LazyX71', 'minPhp' => '7.1')
     */
    public function testBaseLazy($testedClass)
    {
        $x = new $testedClass(1, 2);

        $this->assertEquals(1, $x->getA());
        $this->assertEquals(2, $x->getB());

        $x2 = $x->createAnotherX();

        $this->assertEquals(11, $x2->getA());
        $this->assertEquals(102, $x2->getB());
    }

    /**
     * @dataProvider withProvider
     * @_testWith ('ClassGenerator\tests\ResourceClasses\LazyX')
     *           ('ClassGenerator\tests\ResourceClasses\LazyXInterface')
     */
    public function testBaseLazyWithExistingProxifiedObject($testedClass)
    {
        $x = $testedClass::cgGet(new ResourceClasses\X(1, 2));

        $this->assertEquals(1, $x->getA());
        $this->assertEquals(2, $x->getB());

        $x2 = $x->createAnotherX();

        $this->assertEquals(11, $x2->getA());
        $this->assertEquals(102, $x2->getB());
    }

    /**
     * @dataProvider withProvider
     * @_testWith ('ClassGenerator\tests\ResourceClasses\LazyX')
     *           ('ClassGenerator\tests\ResourceClasses\LazyXInterface')
     */
    public function testBaseLazyWithClosure($testedClass)
    {
        $x = $testedClass::cgGet(function () { return new ResourceClasses\X(1, 2);});

        $this->assertEquals(1, $x->getA());
        $this->assertEquals(2, $x->getB());

        $x2 = $x->createAnotherX();

        $this->assertEquals(11, $x2->getA());
        $this->assertEquals(102, $x2->getB());
    }

    public function testLazyWithConstructorCallCount()
    {
        $x = new ResourceClasses\LazyX(1, 2);
        ResourceClasses\X::$constructorCount = 0;

        $x2 = $x->createAnotherX()->createAnotherX()->createAnotherX()->createAnotherX();

        $this->assertEquals(0, ResourceClasses\X::$constructorCount);
        $this->assertEquals(41, $x2->getA());
        $this->assertEquals(402, $x2->getB());
        $this->assertEquals(5, ResourceClasses\X::$constructorCount);
    }

    public function testGeneratorLazy()
    {
        $x = static::getGenerator()->lazy(function() {return new ResourceClasses\X(1, 2); }, 'ClassGenerator\tests\ResourceClasses\X', true);
        ResourceClasses\X::$constructorCount = 0;

        $x2 = $x->createAnotherX()->createAnotherX()->createAnotherX()->createAnotherX();

        $this->assertEquals(0, ResourceClasses\X::$constructorCount);
        $this->assertEquals(41, $x2->getA());
        $this->assertEquals(402, $x2->getB());
        //here is difference between testGeneratorLazy() and testLazy() because creating first object is also delayed
        $this->assertEquals(5, ResourceClasses\X::$constructorCount);
    }

    public function testGeneratorLazyMethods()
    {
        $x = static::getGenerator()->lazyMethods(new ResourceClasses\X(1, 2));
        ResourceClasses\X::$constructorCount = 0;

        $x2 = $x->createAnotherX()->createAnotherX()->createAnotherX()->createAnotherX();

        $this->assertEquals(0, ResourceClasses\X::$constructorCount);
        $this->assertEquals(41, $x2->getA());
        $this->assertEquals(402, $x2->getB());
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

    /**
     * @requires PHP 5.6
     */
    public function testVariadics() {
        $this->assertTrue(class_exists('ClassGenerator\tests\ResourceClasses\LazyVariadic'));

        $x = new ResourceClasses\LazyVariadic('.', ',');
        $this->assertEquals('.a|,b', $x->join('|', 'a', 'b'));
    }
}
