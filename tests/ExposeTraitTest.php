<?php
/**
 * Created by PhpStorm.
 * User: Rafał
 * Date: 05.10.14
 * Time: 14:35
 */

use ClassGenerator\tests\ResourceClasses;

class ExposeTraitTest extends BaseTest {
    /**
     * @dataProvider withProvider
     * @_testWith ('ClassGenerator\tests\ResourceClasses\ExposeTraitTester', true)
     *           ('ClassGenerator\tests\ResourceClasses\CExposeTraitTester\BaseTester', true)
     *           ('ClassGenerator\tests\ResourceClasses\CExposeTraitTester\MethodTemplateName', true)
     *           ('ClassGenerator\tests\ResourceClasses\ExposeTraitInterfaceTester', false)
     *           ('ClassGenerator\tests\ResourceClasses\CExposeTraitTester\InterfaceTester', false)
     */
    public function testBaseClass($testedClass, $haveDummyMethod) {
        $exposedObject = new ResourceClasses\X(101, 102);
        $tested = new $testedClass($exposedObject);
        $this->assertEquals($exposedObject->getA(), $tested->getA());
        $this->assertEquals($exposedObject->getB(), $tested->getB());
        $this->assertEquals($exposedObject->getA(), $tested->getA());
        $tested->setB(201);
        $this->assertEquals(201, $tested->getB());
        $this->assertEquals(201, $exposedObject->getB());

        $this->assertEquals($haveDummyMethod, method_exists($tested, 'dummyMethod'));
    }

    /**
     * @dataProvider withProvider
     * @_testWith ('ClassGenerator\tests\ResourceClasses\CExposeTraitTester\FixedParamsOn', array(30))
     *           ('ClassGenerator\tests\ResourceClasses\CExposeTraitTester\FixedParamsOff', array())
     */
    public function testFixedParameters($variant, $expectedParams) {
        $o = new $variant();
        $this->assertEquals($expectedParams, $o->setA());
    }

    /**
     * @dataProvider withProvider
     * @_testWith ('ClassGenerator\tests\ResourceClasses\CExposeTraitTester\BaseTester', true)
     *           ('ClassGenerator\tests\ResourceClasses\CExposeTraitTester\RefMethods', false)
     */
    public function testRefMethod($variant, $methodExists) {
        $o = new $variant(new ResourceClasses\X(0, 0));
        $this->assertEquals($methodExists, method_exists($o, 'cgExposedX'));
        $this->assertEquals($methodExists, method_exists($o, 'cgExposedGetX'));
        $this->assertEquals($methodExists, method_exists($o, 'cgExposedMapX'));
        $this->assertEquals($methodExists, method_exists($o, 'cgExposedOnEmptyX'));
    }

    /**
     * @dataProvider withProvider
     * @_testWith ('getA', false)
     *           ('setA', true)
     *           ('getB', true)
     *           ('setB', false)
     */
    public function testNo($methodName, $methodExists) {
        $o = new \ClassGenerator\tests\ResourceClasses\CExposeTraitTester\NoTester(new ResourceClasses\X(0, 0));
        $this->assertEquals($methodExists, method_exists($o, $methodName));
    }
} 