<?php

/** @noinspection PhpUndefinedNamespaceInspection */
use ClassGenerator\tests\ResourceClasses;

class NullTest extends BaseTest
{
    /**
     * @dataProvider withProvider
     * @_testWith ('ClassGenerator\tests\ResourceClasses\NullX')
     *            ('ClassGenerator\tests\ResourceClasses\NullXInterface')
     */
    public function testNull($testedClass)
    {
        $this->assertTrue(class_exists('ClassGenerator\tests\ResourceClasses\NullX'));
        $nullObject = new $testedClass();

        $this->assertEquals(null, $nullObject->getA());
        $this->assertEquals(0, $nullObject->getB());
    }

    /**
     * @dataProvider withProvider
     * @_testWith ('ClassGenerator\tests\ResourceClasses\NullX', 'ClassGenerator\tests\ResourceClasses\X')
     *           ('ClassGenerator\tests\ResourceClasses\NullXInterface', 'ClassGenerator\tests\ResourceClasses\XInterface')
     */
    public function testNullObjectIsInstanceOfBase($testedClass, $parentClass)
    {
        $n = new $testedClass();

        $this->assertTrue($n instanceof $parentClass);
    }

    public function testBaseNull()
    {
        $this->assertFalse(class_exists('ClassGenerator\tests\ResourceClasses\NullXBase'));
        $this->assertTrue(class_exists('ClassGenerator\tests\ResourceClasses\BaseNullX'));
        $nullObject = new ResourceClasses\BaseNullX();

        $this->assertEquals(null, $nullObject->getA());
        $this->assertEquals(0, $nullObject->getB());
    }

    public function testNullWithIteratorShouldProduceIteratorWithoutIterations()
    {
        $emptyIterator = new \NullIterator();

        foreach($emptyIterator as $x) {
            $this->fail("");
        };
    }

    public function testSerializeWithNullObject()
    {
        $x = new ResourceClasses\NullSerialize(1, 2);

        $this->assertEquals($x, unserialize(serialize($x)));
    }

    public function testPhpDocReturn()
    {
        $x = new ResourceClasses\NullZ();

        $this->assertEquals(1, $x->getA());
        $this->assertEquals('empty', (string) $x);
    }

    public function testPhpDocThrow()
    {
        $x = new ResourceClasses\NullZ();

        $this->setExpectedException('BadFunctionCallException');
        $x->getB();
    }

    public function testNullOnClassWithStatic()
    {
        $this->assertTrue(class_exists('ClassGenerator\tests\ResourceClasses\NullClassWithStatic'));

        $x = new ResourceClasses\NullClassWithStatic();

        $this->assertTrue($x instanceof ResourceClasses\ClassWithStatic);
    }

    /**
     * @requires PHP 7.0
     */
    public function testTypeFromReturnTypehint() {
        $this->assertTrue(class_exists('ClassGenerator\tests\ResourceClasses\NullX7'));

        $x = new ResourceClasses\NullX7();
        $this->assertEquals(0, $x->getA());
        $this->assertEquals(0, $x->getB());
        $this->assertTrue($x->createAnotherX() instanceof ResourceClasses\NullX7);
    }

    /**
     * @requires PHP 7.1
     */
    public function testTypeFromReturnNullableTypehint() {
        $this->assertTrue(class_exists('ClassGenerator\tests\ResourceClasses\NullX71'));

        $x = new ResourceClasses\NullX71();
        $this->assertEquals(null, $x->getA());
        $this->assertEquals(0, $x->getB()); //phpDoc type have higher priority than typehint
        $this->assertEquals(null, $x->createAnotherX());
    }
}
