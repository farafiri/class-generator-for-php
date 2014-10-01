<?php
/**
 * Created by PhpStorm.
 * User: Rafał
 * Date: 01.10.14
 * Time: 20:44
 */

namespace ClassGenerator\SymfonyBundle\Doctrine;


class TraitAdapter extends AbstractAdapter {
    const GENERATOR_CLASS = 'ClassGenerator\SymfonyBundle\Doctrine\TraitGenerator';

    protected function getBaseClassName($className) {
        if (preg_match('/^(.+)Trait$/', $className, $match)) {
            return $match[1];
        } else {
            return null;
        }
    }
} 