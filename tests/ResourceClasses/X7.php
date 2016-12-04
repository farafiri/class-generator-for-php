<?php

namespace ClassGenerator\tests\ResourceClasses;

class X7
{
    public static $constructorCount = 0;

    public $a = 0;
    public $b = 0;

    public function __construct(int $a = 10, int $b = 20)
    {
        $this->a = $a;
        $this->b = $b;
        self::$constructorCount++;
    }

    public function setA(int $a = 30)
    {
        $this->a = $a;
    }

    public function getA(): int {
        return $this->a;
    }

    public function setB(int $b)
    {
        $this->b = $b;
    }

    /**
     * @return int
     */
    public function getB(): int {
        return $this->b;
    }

    public function createAnotherX(): X7
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