<?php
/**
 * Created by PhpStorm.
 * User: Rafał
 * Date: 30.01.16
 * Time: 22:10
 */

namespace ClassGenerator\tests\ResourceClasses;


class ToPrimitivesDecorator extends \ClassGenerator\BaseDecorator implements ToPrimitivesInterface {
    public function toBool() {
        return $this->toNumber() == 10;
    }

    public function toNumber() {
        return (integer) $this->getA() * (integer) $this->getB();
    }
} 