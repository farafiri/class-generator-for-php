<?php

use ClassGenerator\tests\ResourceClasses;

class BaseDecoratorTest extends \PHPUnit_Framework_TestCase
{
    static public function setUpBeforeClass()
    {
        \ClassGenerator\Autoloader::getInstance()->setCachePatch(__DIR__ . DIRECTORY_SEPARATOR . 'cache')->register();
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
} 