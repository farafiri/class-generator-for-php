<?php

/** @noinspection PhpUndefinedNamespaceInspection */
use ClassGenerator\tests\ResourceClasses;

class CompositeTest extends BaseTest
{
    /**
     * @dataProvider withProvider
     * @_testWith ('ClassGenerator\tests\ResourceClasses\CompositeX')
     *           ('ClassGenerator\tests\ResourceClasses\CompositeXInterface')
     */
    public function testComposite($testedClass)
    {
        $x1 = new ResourceClasses\X();
        $x2 = new ResourceClasses\X();

        $composite = new $testedClass(array($x1, $x2));
        $composite->setA(100);
        $composite->setB(101);

        $this->assertEquals(100, $x1->getA());
        $this->assertEquals(100, $x2->getA());
        $this->assertEquals(101, $x1->getB());
        $this->assertEquals(101, $x2->getB());
    }

    public function testCompositeIsInstanceOfBase()
    {
        $composite = new ResourceClasses\CompositeX(array());

        $this->assertTrue($composite instanceof ResourceClasses\X);
    }

    /**
     * @dataProvider withProvider
     * @_testWith ('ClassGenerator\tests\ResourceClasses\CompositeX')
     *           ('ClassGenerator\tests\ResourceClasses\CompositeXInterface')
     */
    public function testCompositeShouldReturnNonFalseValue($testedClass)
    {
        $x1 = new ResourceClasses\X();
        $x1->setA(false);
        $x1->setB(42);
        $x2 = new ResourceClasses\X();
        $x2->setA("abc");
        $x2->setB(0);

        $composite = new $testedClass(array($x1, $x2));

        $this->assertEquals("abc", $composite->getA());
        $this->assertEquals("42", $composite->getB());
    }

    public function testCompositeShouldReturnFirstValue()
    {
        $x1 = new ResourceClasses\X();
        $x1->setA(false);
        $x1->setB(34);
        $x2 = new ResourceClasses\X();
        $x2->setA("");
        $x2->setB(58);

        $composite = new ResourceClasses\CompositeX(array($x1, $x2));

        $this->assertEquals(false, $composite->getA());
        $this->assertEquals(34, $composite->getB());
    }

    public function testCompositeShouldReturnFirstNonNullValueFromFalsyValues()
    {
        $x1 = new ResourceClasses\X();
        $x1->setA(false);
        $x1->setB(null);
        $x2 = new ResourceClasses\X();
        $x2->setA(null);
        $x2->setB(0);

        $composite = new ResourceClasses\CompositeX(array($x1, $x2));

        $this->assertEquals(false, $composite->getA());
        $this->assertEquals(0, $composite->getB());
    }

    public function testCompositeGetChildren()
    {
        $x1 = new ResourceClasses\X();
        $x2 = new ResourceClasses\X();
        $x3 = new ResourceClasses\X();

        $composite = new ResourceClasses\CompositeX(array($x1, $x2, $x3));
        $this->assertSame(array($x1, $x2, $x3), $composite->cgGetChildren());
    }

    public function testCompositeAddChildrenWithSingleItem()
    {
        $x1 = new ResourceClasses\X();
        $x2 = new ResourceClasses\X();
        $x3 = new ResourceClasses\X();

        $composite = new ResourceClasses\CompositeX(array($x1, $x2));

        $composite->cgAddChildren($x3);
        $this->assertSame(array($x1, $x2, $x3), $composite->cgGetChildren());
    }

    public function testCompositeAddChildrenWithArray()
    {
        $x1 = new ResourceClasses\X();
        $x2 = new ResourceClasses\X();
        $x3 = new ResourceClasses\X();

        $composite = new ResourceClasses\CompositeX(array($x1));

        $composite->cgAddChildren(array($x2, $x3));
        $this->assertSame(array($x1, $x2, $x3), $composite->cgGetChildren());
    }

    public function testCompositeAddChildrenKeepChildrenUnique()
    {
        $x1 = new ResourceClasses\X();
        $x2 = new ResourceClasses\X();
        $x3 = new ResourceClasses\X();

        $composite = new ResourceClasses\CompositeX(array($x1, $x2));

        $composite->cgAddChildren(array($x3, $x2));
        $this->assertSame(array($x1, $x2, $x3), $composite->cgGetChildren());
    }

    public function testCompositeSetChildren()
    {
        $x1 = new ResourceClasses\X();
        $x2 = new ResourceClasses\X();

        $composite = new ResourceClasses\CompositeX(array($x1));

        $composite->cgSetChildren(array($x2));
        $this->assertSame(array($x2), $composite->cgGetChildren());
    }

    public function testCompositeRemoveChildren()
    {
        $x1 = new ResourceClasses\X();
        $x2 = new ResourceClasses\X();
        $x3 = new ResourceClasses\X();

        $composite = new ResourceClasses\CompositeX(array($x1, $x2));

        $this->assertSame(array($x2), $composite->cgRemoveChildren(array($x3, $x2)));
        $this->assertSame(array($x1), $composite->cgGetChildren());
    }

    public function testCompositeRemoveChildrenWithCallable()
    {
        $x1 = new ResourceClasses\X();
        $x2 = new ResourceClasses\X();
        $x3 = new ResourceClasses\X();

        $composite = new ResourceClasses\CompositeX(array($x1, $x2, $x3));

        $this->assertSame(array($x2), $composite->cgRemoveChildren(function($a) use ($x2) {
            return $a === $x2;
        }));

        $this->assertSame(array($x1, $x3), $composite->cgGetChildren());
    }

    public function testCompositeGetComposite()
    {
        $x = new ResourceClasses\X();
        $xy = new ResourceClasses\XY();

        $CompositeX = new ResourceClasses\CompositeX(array($x, $xy));

        $xyComposite = $CompositeX->cgGetComposite('ClassGenerator\tests\ResourceClasses\XY');

        $this->assertTrue($xyComposite instanceof ResourceClasses\CompositeXY);
        $this->assertSame(array($xy), $xyComposite->cgGetChildren());
        $this->assertTrue(method_exists($xyComposite, 'getC'));
    }

    /**
     * @dataProvider withProvider
     * @_testWith ('ClassGenerator\tests\ResourceClasses\CompositeX')
     *           ('ClassGenerator\tests\ResourceClasses\CompositeXInterface')
     */
    public function testCloneOnComposite($testedClass)
    {
        $x1 = new ResourceClasses\X();
        $composite = new $testedClass(array($x1));

        $composite2 = clone $composite;
        $composite2Children = $composite2->cgGetChildren();
        $this->assertNotSame($x1, $composite2Children[0]);
        $this->assertEquals($x1, $composite2Children[0]);
    }

    /**
     * @dataProvider withProvider
     * @_testWith ('ClassGenerator\tests\ResourceClasses\CompositeX')
     *           ('ClassGenerator\tests\ResourceClasses\CompositeXInterface')
     */
    public function testSleepWakeupOnComposite($testedClass)
    {
        $composite1 = new $testedClass(array(new ResourceClasses\X()));
        $composite2 = unserialize(serialize($composite1));

        $this->assertEquals($composite1, $composite2);
    }

    /**
     * @dataProvider withProvider
     * @_testWith ('ClassGenerator\tests\ResourceClasses\CompositeIterator')
     *           ('CompositeIterator')
     */
    public function testCompositeOfIteratorShouldIterateOverAllChildren($testedClass)
    {
        $iterator1 = new ResourceClasses\Iterator(0, 1);
        $iterator2 = new ResourceClasses\Iterator(2, 10);
        $iterator3 = new ResourceClasses\Iterator(3, 100);
        $iterator4 = new ResourceClasses\Iterator(0, 1000);
        $iterator5 = new ResourceClasses\Iterator(4, 10000);
        $composite = new $testedClass(array($iterator1, $iterator2, $iterator3, $iterator4, $iterator5));

        $sum = 0;
        foreach($composite as $value) {
            $sum += $value;
        }

        $this->assertEquals(40320, $sum);
    }

    /**
     * @dataProvider withProvider
     * @_testWith ('ClassGenerator\tests\ResourceClasses\CompositeIteratorAggregate')
     *           ('CompositeIteratorAggregate')
     */
    public function testCompositeOfIteratorAggregateShouldIterateOverAllChildren($testedClass)
    {
        $iterator1 = new ResourceClasses\IteratorAggregate(0, 1);
        $iterator2 = new ResourceClasses\IteratorAggregate(2, 10);
        $iterator3 = new ResourceClasses\IteratorAggregate(3, 100);
        $iterator4 = new ResourceClasses\IteratorAggregate(0, 1000);
        $iterator5 = new ResourceClasses\IteratorAggregate(4, 10000);
        $composite = new $testedClass(array($iterator1, $iterator2, $iterator3, $iterator4, $iterator5));

        $sum = 0;
        foreach($composite as $value) {
            $sum += $value;
        }

        $this->assertEquals(40320, $sum);
    }

    /**
     * @dataProvider withProvider
     * @_testWith ('ClassGenerator\tests\ResourceClasses\CompositeX')
     *           ('ClassGenerator\tests\ResourceClasses\CompositeXInterface')
     */
    public function testCompositeIfMethodReturnsArrayThenCompositeShouldReturnMergedArray($testedClass)
    {
        $x1 = new ResourceClasses\X(1, 2);
        $x2 = new ResourceClasses\X(3, 4);
        $x3 = new ResourceClasses\X(5, 6);
        $composite = new $testedClass(array($x1, $x2, $x3));

        $this->assertEquals(array(1, 2, 1, 2, 3, 4, 1, 2, 3, 4, 5, 6), $composite->getRangeToB());
    }

    public function testCompositeIfMethodReturnsObjectThenCompositeShouldBeReturned()
    {
        $x1 = new ResourceClasses\X(1, 2);
        $x2 = new ResourceClasses\X(3, 4);
        $x3 = new ResourceClasses\X(5, 6);
        $composite = new ResourceClasses\CompositeX(array($x1, $x2, $x3));

        $resultOfCreateAnotherX = $composite->createAnotherX();
        $this->assertTrue($resultOfCreateAnotherX instanceof ResourceClasses\CompositeX);
        $this->assertEquals(array(
            new ResourceClasses\X(11, 102),
            new ResourceClasses\X(13, 104),
            new ResourceClasses\X(15, 106),
        ), $resultOfCreateAnotherX->cgGetChildren());
    }

    public function testSerializeWithComposite()
    {
        $x1 = new ResourceClasses\Serialize(1, 2);
        $x2 = new ResourceClasses\Serialize(3, 4);
        $composite = new ResourceClasses\CompositeSerialize(array($x1, $x2));

        $this->assertEquals($composite, unserialize(serialize($composite)));
    }

    public function testCompositeWithAndReturnsFirstFalsyValue()
    {
        $x1 = new ResourceClasses\Z(23, 0);
        $x2 = new ResourceClasses\Z(0, 0);

        $composite = new ResourceClasses\CompositeZ(array($x1, $x2));

        $this->assertEquals(0, $composite->getA());
    }

    public function testCompositeWithAndReturnsFirstValueIfNoFalsyValueFound()
    {
        $x1 = new ResourceClasses\Z(23, 0);
        $x2 = new ResourceClasses\Z(12, 0);

        $composite = new ResourceClasses\CompositeZ(array($x1, $x2));

        $this->assertEquals(23, $composite->getA());
    }

    public function testCompositeWithMaxReturnsMaximumValue()
    {
        $x1 = new ResourceClasses\Z(0, 5);
        $x2 = new ResourceClasses\Z(0, 12);
        $x3 = new ResourceClasses\Z(0, 7);

        $composite = new ResourceClasses\CompositeZ(array($x1, $x2, $x3));

        $this->assertEquals(12, $composite->getB());
    }

    public function testCompositeWithImplodeReturnsImplodedValue()
    {
        $x1 = new ResourceClasses\Z(1, 2);
        $x2 = new ResourceClasses\Z(3, 4);

        $composite = new ResourceClasses\CompositeZ(array($x1, $x2));

        $this->assertEquals('1 2,3 4', (string) $composite);
    }
}
