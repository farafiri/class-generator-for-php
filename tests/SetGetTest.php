<?php
/**
 * Created by PhpStorm.
 * User: Rafał
 * Date: 24.05.15
 * Time: 12:27
 */

class SetGetTest extends BaseTest {
    /**
     * @dataProvider withProvider
     * @testWith ('ClassGenerator\SetGet\Name')
     */
    public function testTraitGenerated($traitName) {
        $this->assertTrue(trait_exists($traitName, true));
        //var_dump(get_declared_traits());
    }

    public function testUsingClassLoad() {
        $this->assertTrue(class_exists('ClassGenerator\tests\TestClass\SetGetSimpleTester'));
    }

    public function testMethodExists() {
        $instance = new ClassGenerator\tests\TestClass\SetGetSimpleTester();
        $this->assertTrue(method_exists($instance, 'getName'));
        $this->assertTrue(method_exists($instance, 'getTitle'));
        $this->assertTrue(method_exists($instance, 'setName'));
        $this->assertTrue(method_exists($instance, 'getTitle'));
    }

    public function testMethodWorking() {
        $instance = new ClassGenerator\tests\TestClass\SetGetSimpleTester();
        $instance->setName('name 1');
        $this->assertEquals('name 1', $instance->getName());
        $instance->setName('name 2');
        $this->assertEquals('name 2', $instance->getName());
    }

    public function testSetterResult() {
        $instance = new ClassGenerator\tests\TestClass\SetGetSimpleTester();
        $this->assertEquals($instance, $instance->setTitle('name 1'));
    }
} 