<?php
/**
 * Created by PhpStorm.
 * User: Rafał
 * Date: 12.01.14
 * Time: 14:44
 */

namespace ClassGenerator\tests\ResourceClasses;


class ToNumberDecorator extends \ClassGenerator\BaseDecorator
{
    public function toNumber()
    {
        return (integer) $this->getA() + (integer) $this->getB();
    }
} 