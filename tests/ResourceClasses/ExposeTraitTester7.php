<?php
/**
 * Created by PhpStorm.
 * User: Rafał
 * Date: 11.12.16
 * Time: 20:00
 */

namespace ClassGenerator\tests\ResourceClasses;


class ExposeTraitTester7 {
    use X7ExposeTrait;

    protected $x;

    public function getX7() {
        return $this->x;
    }

    public function __construct($x) {
        $this->x = $x;
    }
}