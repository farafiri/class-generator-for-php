<?php

namespace ClassGenerator\tests\ResourceClasses;


class ToNumberDecorator extends \ClassGenerator\BaseDecorator
{
    public function toNumber()
    {
        return (integer) $this->getA() + (integer) $this->getB();
    }
} 