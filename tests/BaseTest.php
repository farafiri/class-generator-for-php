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
            self::$loader = \ClassGenerator\Autoloader::getInstance()->setCachePath(__DIR__ . DIRECTORY_SEPARATOR . 'cache')->register();
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

    public function withProvider($testName)
    {
        $rc = new \ReflectionMethod(get_class($this), $testName);
        if (preg_match('/\*\s@_testWith\s((.|\n)*)?(\*\s@|\*?\*\/)/', $rc->getDocComment(), $match)) {
            $arrayCode = 'array(array' . preg_replace('/\n\s*\*/', ',array', $match[1]) . ')';
            $results = array();
            foreach(eval('return ' . $arrayCode . ';') as $testData) {
                if (isset($testData['minPhp'])) {
                    if (version_compare($testData['minPhp'], PHP_VERSION) >= 0) {
                        continue;
                    }
                    unset($testData['minPhp']);
                }
                $results[] = $testData;
            };

            return $results;
        };

        throw new \Exception('No @_testWith found in ' . get_class($this) . '::' . $testName);
    }
}