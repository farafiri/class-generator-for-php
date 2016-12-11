<?php
/**
 * Created by PhpStorm.
 * User: Rafał
 * Date: 11.12.16
 * Time: 20:01
 */

namespace ClassGenerator\tests\ResourceClasses;


class ExposeTraitTester71 {
    use X71ExposeTrait;

    protected $x;

    public function getX71() {
        return $this->x;
    }

    public function __construct($x) {
        $this->x = $x;
    }
}