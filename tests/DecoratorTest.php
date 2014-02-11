<?php

/** @noinspection PhpUndefinedNamespaceInspection */
use ClassGenerator\tests\ResourceClasses;

class DecoratorTest extends BaseTest
{
    public function testDecorator()
    {
        $this->assertTrue(class_exists('ClassGenerator\tests\ResourceClasses\DecoratorForX'));

        $x = new ResourceClasses\X();
        $decorator = new ResourceClasses\DecoratorForX($x);

        $this->assertEquals(10, $decorator->getA());

        $x->a = 32;
        $this->assertEquals(32, $decorator->getA());
    }

    public function testDecoratorIsInstanceOfBase()
    {
        $x = new ResourceClasses\X();
        $decorator = new ResourceClasses\DecoratorForX($x);

        $this->assertTrue($decorator instanceof ResourceClasses\X);
    }

    public function testBaseDecorator()
    {
        $this->assertTrue(class_exists('ClassGenerator\tests\ResourceClasses\BaseDecoratorForX'));

        $x = new ResourceClasses\X();
        $decorator = new ResourceClasses\BaseDecoratorForX($x);

        $this->assertEquals(10, $decorator->getA());

        $x->a = 32;
        $this->assertEquals(32, $decorator->getA());
    }

    public function testCloneOnDecorator()
    {
        $x1 = new ResourceClasses\X();
        $decorator = new ResourceClasses\DecoratorForX($x1);

        $decorator2 = clone $decorator;
        $this->assertNotSame($x1, $decorator2->cgGetDecorated());
        $this->assertEquals($x1, $decorator2->cgGetDecorated());
    }

    public function testSleepWakeupOnDecorator()
    {
        $decorator1 = new ResourceClasses\DecoratorForX(new ResourceClasses\X());
        $decorator2 = unserialize(serialize($decorator1));

        $this->assertEquals($decorator1, $decorator2);
    }

    public function testSerializeWithDecorator()
    {
        $x = new ResourceClasses\Serialize(1, 2);
        $decorator = new ResourceClasses\DecoratorForSerialize($x);

        $this->assertEquals($decorator, unserialize(serialize($decorator)));
    }

    public function testDecorate()
    {
        $x = new ResourceClasses\X(1, 2);
        $toNumberDecorator = new ResourceClasses\ToNumberDecorator();
        $decorated = $toNumberDecorator->cgDecorate($x);

        $this->assertTrue($decorated instanceof ResourceClasses\X);
        $this->assertEquals(1, $decorated->getA());
        $this->assertEquals(3, $decorated->toNumber());

        $decorated->setA(10);
        $this->assertEquals(12, $decorated->toNumber());
    }

    public function testWeShouldBeAbleToDoSeveralDecoratesWithSingleDecoratorClass()
    {
        $x1 = new ResourceClasses\X(1, 2);
        $x2 = new ResourceClasses\X(3, 4);

        $numberDecorator = new ResourceClasses\ToNumberDecorator();

        $x1  = $numberDecorator->cgDecorate($x1);
        $x2 = $numberDecorator->cgDecorate($x2);

        $this->assertEquals(3, $x1->toNumber());
        $this->assertEquals(7, $x2->toNumber());
    }

    public function testRestrictionOnDecoratingClass()
    {
        $x = new ResourceClasses\X(1, 2);
        $x2 = new \ClassGenerator\tests\ResourceClasses2\X(3, 4);

        $swapDecorator = new ResourceClasses\SwapDecorator();

        $swapDecorator->cgDecorate($x);

        $this->setExpectedException('ClassGenerator\Exceptions\Proxy');
        $swapDecorator->cgDecorate($x2);
    }

    public function testDecoratorHasDecoratorWithString()
    {
        $x = new ResourceClasses\X(2, 5);
        $decorator = new ResourceClasses\IncreaseDecorator(10);
        $decorated = $decorator->cgDecorate($x);

        $this->assertFalse($decorated->cgHasDecorator('ClassGenerator\tests\ResourceClasses\ToNumberDecorator'));
        $this->assertTrue($decorated->cgHasDecorator('ClassGenerator\tests\ResourceClasses\IncreaseDecorator'));
    }

    public function testDecoratorHasDecoratorWithInstance()
    {
        $x = new ResourceClasses\X(2, 5);
        $decorator = new ResourceClasses\IncreaseDecorator(10);
        $decorated = $decorator->cgDecorate($x);

        $this->assertFalse($decorated->cgHasDecorator(new ResourceClasses\IncreaseDecorator(5)));
        $this->assertTrue($decorated->cgHasDecorator(new ResourceClasses\IncreaseDecorator(10)));
    }

    public function testDecoratorHasDecoratorWithClosure()
    {
        $x = new ResourceClasses\X(2, 5);
        $decorator = new ResourceClasses\IncreaseDecorator(10);
        $decorated = $decorator->cgDecorate($x);

        $this->assertFalse($decorated->cgHasDecorator(function () {return false;}));

        $closure = function ($_decorator) use ($decorator) {
            $this->assertEquals($_decorator, $decorator);
            return true;
        };
        $this->assertTrue($decorated->cgHasDecorator($closure->bindTo($this, 'static')));
    }

    public function testDecoratorRemoveDecorator()
    {
        $x = new ResourceClasses\X(2, 5);
        $decorator1 = new ResourceClasses\IncreaseDecorator(10);
        $decorator2 = new ResourceClasses\IncreaseDecorator(100);
        $decorated = $decorator2->cgDecorate($decorator1->cgDecorate($x));

        $this->assertEquals(112, $decorated->getA());
        $decorated->cgRemoveDecorator($decorator1);
        $this->assertEquals(102, $decorated->getA());
        $decorated->cgRemoveDecorator($decorator2);
        $this->assertEquals(2, $decorated->getA());
    }

    public function testCreateDecorableObject()
    {
        $decorable = self::getGenerator()->decorate(new ResourceClasses\X(2, 5));

        $this->assertEquals(2, $decorable->getA());

        $decorator = new ResourceClasses\IncreaseDecorator(10);
        $decorator->cgDecorate($decorable);

        $this->assertEquals(12, $decorable->getA());
    }
}
