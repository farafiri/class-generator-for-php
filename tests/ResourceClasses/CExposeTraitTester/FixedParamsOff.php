<?php
/**
 * Created by PhpStorm.
 * User: Rafał
 * Date: 15.07.15
 * Time: 22:18
 */

namespace ClassGenerator\tests\ResourceClasses\CExposeTraitTester;


class FixedParamsOff {
    use \ClassGenerator\tests\ResourceClasses\XCExposeTrait;

    function cgExposedX($method, $params) {
        return $params;
    }
} 