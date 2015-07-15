<?php
/**
 * Created by PhpStorm.
 * User: Rafał
 * Date: 14.07.15
 * Time: 21:46
 */

namespace ClassGenerator\tests\ResourceClasses\CExposeTraitTester;


class MethodTemplateName {
    use \ClassGenerator\tests\ResourceClasses\XCExposeTrait\MethodsYY;

    protected $x;

    public function getYY() {
        return $this->x;
    }

    public function __construct($x) {
        $this->x = $x;
    }
} 