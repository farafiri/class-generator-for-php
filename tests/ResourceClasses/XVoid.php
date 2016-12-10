<?php
/**
 * Created by PhpStorm.
 * User: Rafał
 * Date: 09.12.16
 * Time: 14:36
 */

namespace ClassGenerator\tests\ResourceClasses;

class XVoid implements XInterface
{
    public static $constructorCount = 0;

    public $a = 0;
    public $b = 0;

    public function __construct($a = 10, $b = 20)
    {
        $this->a = $a;
        $this->b = $b;
        self::$constructorCount++;
    }

    public function setA($a = 30): void
    {
        $this->a = $a;
    }

    public function getA() {
        return $this->a;
    }

    public function setB($b): void
    {
        $this->b = $b;
    }

    /**
     * @return int
     */
    public function getB() {
        return $this->b;
    }

    /**
     * @return \ClassGenerator\tests\ResourceClasses\X
     */
    public function createAnotherX()
    {
        return new self($this->getA() + 10, $this->getB() + 100);
    }

    /**
     * @return int[]
     */
    public function getRangeToB()
    {
        return range(1, $this->getB());
    }

    public function __clone()
    {
        if (is_object($this->a)) {
            $this->a = clone $this->a;
        }
    }

    public function dummyMethod()
    {
        return null;
    }

    public function getSumAB() {
        return $this->getA() + $this->getB();
    }
}
