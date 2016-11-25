<?php

/** @noinspection PhpUndefinedNamespaceInspection */
use ClassGenerator\tests\ResourceClasses;

class ExtendableTest extends BaseTest {

    /**
     * @dataProvider withProvider
     * @_testWith ('', 1, 3)
     *           ('cgDecorate', 11, 3)
     *           ('cgExtend', 11, 13)
     */
    public function testBaseExtendable($method, $getAExpectedResult, $getSumExpectedResult)
    {
        $extendable = new ResourceClasses\ExtendableX(1, 2);

        $extender = new ResourceClasses\IncreaseDecorator(10);
        if ($method) {
            $extender->$method($extendable);
        }

        $this->assertEquals($getAExpectedResult, $extendable->getA());
        $this->assertEquals($getSumExpectedResult, $extendable->getSumAB());
    }

    public function testExtendAndDecorateMix()
    {
        $extendable = new ResourceClasses\ExtendableX(1, 2);

        $extendable->cgDecorateWith(new ResourceClasses\IncreaseDecorator(10));

        $this->assertEquals(11, $extendable->getA());
        $this->assertEquals(3, $extendable->getSumAB());

        $extendable->cgExtendWith(new ResourceClasses\IncreaseDecorator(100));

        $this->assertEquals(111, $extendable->getA());
        $this->assertEquals(103, $extendable->getSumAB());

        $extendable->cgDecorateWith(new ResourceClasses\IncreaseDecorator(1000));

        $this->assertEquals(1111, $extendable->getA());
        $this->assertEquals(103, $extendable->getSumAB());

        $extendable->cgExtendWith(new ResourceClasses\IncreaseDecorator(10000));

        $this->assertEquals(11111, $extendable->getA());
        $this->assertEquals(10103, $extendable->getSumAB());
    }
} 