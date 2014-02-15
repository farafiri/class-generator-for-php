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
     * @nullObject return 1
     * @composite and
     * @return int
     */
    public function getA() {
        return $this->a;
    }

    /**
     * @nullObject throw new \BadFunctionCallException()
     * @composite max
     * @return int
     */
    public function getB() {
        return $this->b;
    }


    /**
     * @nullObject return 'empty'
     * @composite implode(',', 'empty')
     * @return string
     */
    public function __toString()
    {
        return $this->getA() . ' ' . $this->getB();
    }
}
