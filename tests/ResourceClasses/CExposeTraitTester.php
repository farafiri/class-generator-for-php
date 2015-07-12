<?php
/**
 * Created by PhpStorm.
 * User: Rafał
 * Date: 05.10.14
 * Time: 14:29
 */

namespace ClassGenerator\tests\ResourceClasses;


class CExposeTraitTester {
    use XCExposeTrait;

    protected $x;

    public function getX() {
        return $this->x;
    }

    public function __construct($x) {
        $this->x = $x;
    }
}