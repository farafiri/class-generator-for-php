<?php
/**
 * Created by PhpStorm.
 * User: Rafał
 * Date: 24.05.15
 * Time: 21:27
 */

class DoctrineCollectionTest extends BaseTest {
    /**
     * @dataProvider withProvider
     * @_testWith ('ClassGenerator\DoctrineCollection\Name')
     */
    public function testTraitGenerated($traitName) {
        $this->assertTrue(trait_exists($traitName, true));
    }

    public function testUsingClassLoad() {
        $this->assertTrue(class_exists('ClassGenerator\tests\TestClass\DoctrineTester'));
    }

    public function testMethodExists() {
        $instance = new \ClassGenerator\tests\TestClass\DoctrineTester();
        $this->assertTrue(method_exists($instance, 'getPoints'));
        $this->assertTrue(method_exists($instance, 'addPoint'));
        $this->assertTrue(method_exists($instance, 'removePoint'));
    }
}