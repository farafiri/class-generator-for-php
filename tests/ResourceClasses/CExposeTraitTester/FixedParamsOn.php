<?php
/**
 * Created by PhpStorm.
 * User: Rafał
 * Date: 15.07.15
 * Time: 22:16
 */

namespace ClassGenerator\tests\ResourceClasses\CExposeTraitTester;


class FixedParamsOn {
    use \ClassGenerator\tests\ResourceClasses\XCExposeTrait\FixedParameters;

    function cgExposedX($method, $params) {
        return $params;
    }
} 