<?php
/**
 * Created by PhpStorm.
 * User: Rafał
 * Date: 30.01.16
 * Time: 22:13
 */

/** @noinspection PhpUndefinedNamespaceInspection */
use ClassGenerator\tests\ResourceClasses;

class RedecorateTest extends BaseTest {
    public function testRedecorateAddsInterfaces() {
        $x = new ResourceClasses\DecorableX();

        $this->assertTrue($x instanceof ResourceClasses\X);
        $this->assertFalse($x instanceof ResourceClasses\ToNumberInterface);
        $this->assertFalse($x instanceof ResourceClasses\ToPrimitivesInterface);

        $x = $x->cgRedecorate(new ResourceClasses\ToNumberDecorator());
        $this->assertTrue($x instanceof ResourceClasses\X);
        $this->assertTrue($x instanceof ResourceClasses\ToNumberInterface);
        $this->assertFalse($x instanceof ResourceClasses\ToPrimitivesInterface);

        $x = $x->cgRedecorate(new ResourceClasses\ToPrimitivesDecorator());
        $this->assertTrue($x instanceof ResourceClasses\X);
        $this->assertTrue($x instanceof ResourceClasses\ToNumberInterface);
        $this->assertTrue($x instanceof ResourceClasses\ToPrimitivesInterface);
    }

    public function testReextendAddsInterfaces() {
        $x = new ResourceClasses\ExtendableX();

        $this->assertTrue($x instanceof ResourceClasses\X);
        $this->assertFalse($x instanceof ResourceClasses\ToNumberInterface);
        $this->assertFalse($x instanceof ResourceClasses\ToPrimitivesInterface);

        $x = $x->cgReextend(new ResourceClasses\ToNumberDecorator());
        $this->assertTrue($x instanceof ResourceClasses\X);
        $this->assertTrue($x instanceof ResourceClasses\ToNumberInterface);
        $this->assertFalse($x instanceof ResourceClasses\ToPrimitivesInterface);

        $x = $x->cgReextend(new ResourceClasses\ToPrimitivesDecorator());
        $this->assertTrue($x instanceof ResourceClasses\X);
        $this->assertTrue($x instanceof ResourceClasses\ToNumberInterface);
        $this->assertTrue($x instanceof ResourceClasses\ToPrimitivesInterface);
    }

    /**
     * @dataProvider withProvider
     * @testWith (0)
     *           (1)
     *           (2)
     *           (3)
     *           (4)
     */
    public function testIfStandardMethodsBehaviourIsNotAffectedByRedecoratingOrReextending($scenario)
    {
        $extendable = new ResourceClasses\ExtendableX(1, 2);
        switch($scenario) {
            case 0:
            case 3:
            case 4:
                break;
            case 1:
                $extendable = $extendable->cgRedecorate(new ResourceClasses\ToPrimitivesDecorator());
                break;
            case 2:
                $extendable = $extendable->cgReextend(new ResourceClasses\ToPrimitivesDecorator());
                break;
        }

        $extendable = $extendable->cgRedecorate(new ResourceClasses\IncreaseDecorator(10));

        $this->assertEquals(11, $extendable->getA());
        $this->assertEquals(3, $extendable->getSumAB());

        $extendable = $extendable->cgReextend(new ResourceClasses\IncreaseDecorator(100));

        $this->assertEquals(111, $extendable->getA());
        $this->assertEquals(103, $extendable->getSumAB());

        switch($scenario) {
            case 0:
            case 1:
            case 2:
                break;
            case 3:
                $extendable = $extendable->cgRedecorate(new ResourceClasses\ToPrimitivesDecorator());
                break;
            case 4:
                $extendable = $extendable->cgReextend(new ResourceClasses\ToPrimitivesDecorator());
                break;
        }

        $extendable = $extendable->cgRedecorate(new ResourceClasses\IncreaseDecorator(1000));

        $this->assertEquals(1111, $extendable->getA());
        $this->assertEquals(103, $extendable->getSumAB());

        $extendable = $extendable->cgReextend(new ResourceClasses\IncreaseDecorator(10000));

        $this->assertEquals(11111, $extendable->getA());
        $this->assertEquals(10103, $extendable->getSumAB());
    }
} 