<?php
/**
 * Created by PhpStorm.
 * User: Rafał
 * Date: 28.09.14
 * Time: 11:01
 */

namespace ClassGenerator\SymfonyBundle\Doctrine;


class Generator extends \Doctrine\ORM\Tools\EntityGenerator {
    protected $fullEntityNameOnSettersReturnAnnotation = true;
    protected $fieldVisibility = self::FIELD_VISIBLE_PROTECTED;
} 