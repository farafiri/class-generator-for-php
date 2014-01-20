<?php
/**
 * Created by PhpStorm.
 * User: Rafał
 * Date: 20.01.14
 * Time: 10:50
 */

namespace ClassGenerator\tests\ResourceClasses;


class SwapDecorator extends \ClassGenerator\BaseDecorator
{
    const CG_DECORATED = 'ClassGenerator\tests\ResourceClasses\X';

    public function swap()
    {
        $a = $this->getA();
        $this->setA($this->getB());
        $this->setB($a);
    }
}
