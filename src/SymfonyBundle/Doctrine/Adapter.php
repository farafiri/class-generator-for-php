<?php
/**
 * Created by PhpStorm.
 * User: Rafał
 * Date: 01.10.14
 * Time: 20:28
 */

namespace ClassGenerator\SymfonyBundle\Doctrine;


class Adapter extends AbstractAdapter {
    protected function getBaseClassName($className) {
        return $className;
    }
} 