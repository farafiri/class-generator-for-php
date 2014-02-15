<?php

namespace ClassGenerator\tests\ResourceClasses;

class Z
{
    public $a;
    public $b;

    public function __construct($a, $b)
    {
        $this->a = $a;
        $this->b = $b;
    }

    /**
     * @composite and
     * @return int
     */
    public function getA() {
        return $this->a;
    }

    /**
     * @composite max
     * @return int
     */
    public function getB() {
        return $this->b;
    }


    /**
     * @composite implode(',', 'empty')
     * @return string
     */
    public function __toString()
    {
        return $this->getA() . ' ' . $this->getB();
    }
}
