<?php
/**
 * Created by PhpStorm.
 * User: Rafał
 * Date: 31.12.13
 * Time: 17:01
 */

namespace ClassGenerator\tests\ResourceClasses;


class XY extends X
{
    protected $c = 67;

    public function getC()
    {
        return $this->c;
    }

    public function setC($c)
    {
        $this->c = $c;
    }
} 