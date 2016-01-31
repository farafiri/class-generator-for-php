<?php
/**
 * Created by PhpStorm.
 * User: Rafał
 * Date: 30.01.16
 * Time: 22:08
 */

namespace ClassGenerator\tests\ResourceClasses;


interface ToPrimitivesInterface extends ToNumberInterface {
    public function toBool();
} 