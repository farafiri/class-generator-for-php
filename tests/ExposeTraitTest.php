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
     * @testWith ('ClassGenerator\tests\ResourceClasses\ExposeTraitTester', true)
     *           ('ClassGenerator\tests\ResourceClasses\ExposeTraitInterfaceTester', false)
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
} 