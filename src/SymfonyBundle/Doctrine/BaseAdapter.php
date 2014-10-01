<?php
/**
 * Created by PhpStorm.
 * User: Rafał
 * Date: 01.10.14
 * Time: 20:28
 */

namespace ClassGenerator\SymfonyBundle\Doctrine;


class BaseAdapter extends AbstractAdapter{
    protected function getBaseClassName($className) {
        if (preg_match('/^(.+)\\\\Base\\\\(.+)$/', $className, $match)) {
            return $match[1] . '\\' . $match[2];
        } else {
            return null;
        }
    }
} 