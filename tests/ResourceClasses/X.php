<?php

namespace ClassGenerator\tests\ResourceClasses;

class X
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

    public function setA($a)
    {
        $this->a = $a;
    }

    public function getA() {
        return $this->a;
    }

    public function setB($b)
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
        return new self($this->getA() + $this->getB(), $this->getA() - $this->getB());
    }
} 