<?php

/** @noinspection PhpUndefinedNamespaceInspection */
use ClassGenerator\tests\ResourceClasses;

class BaseTest extends \PHPUnit_Framework_TestCase
{
    static $loader = null;
    static $generator = null;

    static public function setUpBeforeClass()
    {
        if (!self::$loader) {
            self::$loader = \ClassGenerator\Autoloader::getInstance()->setCachePatch(__DIR__ . DIRECTORY_SEPARATOR . 'cache')->register();
            self::$generator = self::$loader->getGenerator();
        }
    }

    static protected function getGenerator()
    {
        return self::$generator;
    }

    public function testO()
    {
    }
}