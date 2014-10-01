<?php
/**
 * Created by PhpStorm.
 * User: Rafał
 * Date: 01.10.14
 * Time: 21:38
 */

namespace ClassGenerator\SymfonyBundle\Doctrine;


class InterfaceAdapter extends AbstractAdapter {
    const GENERATOR_CLASS = 'ClassGenerator\SymfonyBundle\Doctrine\InterfaceGenerator';

    protected function getBaseClassName($className) {
        if (preg_match('/^(.+)Interface$/', $className, $match)) {
            return $match[1];
        } else {
            return null;
        }
    }
} 