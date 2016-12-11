<?php
/**
 * Created by PhpStorm.
 * User: Rafał
 * Date: 11.12.16
 * Time: 20:02
 */

namespace ClassGenerator\tests\ResourceClasses;


class ExposeTraitTesterVoid {
    use XVoidExposeTrait;

    protected $x;

    public function getXVoid() {
        return $this->x;
    }

    public function __construct($x) {
        $this->x = $x;
    }
}