<?php
/**
 * Created by PhpStorm.
 * User: Rafał
 * Date: 12.10.14
 * Time: 13:21
 */

namespace ClassGenerator\tests\ResourceClasses;


class ClassWithStatic {
    public function get1() {
        return 1;
    }

    static public function get2() {
        return 2;
    }
} 