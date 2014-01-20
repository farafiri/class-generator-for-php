<?php
/**
 * Created by PhpStorm.
 * User: Rafał
 * Date: 20.01.14
 * Time: 12:47
 */

namespace ClassGenerator\tests\ResourceClasses;


class IncreaseDecorator extends \ClassGenerator\BaseDecorator
{
    public $increaseBy;

    public function __construct($increaseBy)
    {
        $this->increaseBy = $increaseBy;
    }

    public function getA()
    {
        return $this->cgGetDecorated()->getA() + $this->increaseBy;
    }
} 