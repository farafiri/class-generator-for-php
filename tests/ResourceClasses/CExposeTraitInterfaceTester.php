<?php
/**
 * Created by PhpStorm.
 * User: Rafał
 * Date: 05.10.14
 * Time: 14:58
 */

namespace ClassGenerator\tests\ResourceClasses;


class CExposeTraitInterfaceTester {
    use XInterfaceCExposeTrait;

    protected $x;

    /**
     * Interface should be excluded from getter name
     */
    public function getX() {
        return $this->x;
    }

    public function __construct($x) {
        $this->x = $x;
    }
} 