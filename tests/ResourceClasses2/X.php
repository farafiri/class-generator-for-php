<?php

namespace ClassGenerator\tests\ResourceClasses2;

class X
{
    public $a = 0;
    public $b = 0;

    public function __construct($a = 10, $b = 20)
    {
        $this->a = $a;
        $this->b = $b;
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

    public function getB() {
        return $this->b;
    }
}