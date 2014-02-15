<?php

namespace ClassGenerator\tests\ResourceClasses;


class XY extends X
{
    protected $c = 67;

    /**
     * @composite sum
     */
    public function getC()
    {
        return $this->c;
    }

    public function setC($c)
    {
        $this->c = $c;
    }
}
