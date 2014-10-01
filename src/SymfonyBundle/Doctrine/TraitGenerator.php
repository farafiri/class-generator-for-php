<?php
/**
 * Created by PhpStorm.
 * User: Rafał
 * Date: 01.10.14
 * Time: 20:12
 */

namespace ClassGenerator\SymfonyBundle\Doctrine;

use Doctrine\ORM\Mapping\ClassMetadataInfo;

class TraitGenerator extends \Doctrine\ORM\Tools\EntityGenerator {
    /**
     * @param ClassMetadataInfo $metadata
     *
     * @return string
     */
    protected function generateEntityClassName(ClassMetadataInfo $metadata)
    {
        return 'trait ' . $this->getClassName($metadata);
    }
} 