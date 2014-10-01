<?php
/**
 * Created by PhpStorm.
 * User: Rafał
 * Date: 01.10.14
 * Time: 21:40
 */

namespace ClassGenerator\SymfonyBundle\Doctrine;

use Doctrine\ORM\Mapping\ClassMetadataInfo;
use Doctrine\ORM\Tools\EntityGenerator;

class InterfaceGenerator extends EntityGenerator {
    protected static function swapSingleValue(&$a, &$b) {
        if ($a === null) {
            $a = $b;
        } elseif ($b === null) {
            $b = $a;
        } else {
            $c = $a;
            $a = $b;
            $b = $c;
        }
    }

    protected static function swap() {
        static::swapSingleValue(EntityGenerator::$getMethodTemplate, static::$getMethodTemplate);
        static::swapSingleValue(EntityGenerator::$setMethodTemplate, static::$setMethodTemplate);
        static::swapSingleValue(EntityGenerator::$addMethodTemplate, static::$addMethodTemplate);
        static::swapSingleValue(EntityGenerator::$removeMethodTemplate, static::$removeMethodTemplate);
        static::swapSingleValue(EntityGenerator::$lifecycleCallbackMethodTemplate, static::$lifecycleCallbackMethodTemplate);
        static::swapSingleValue(EntityGenerator::$constructorMethodTemplate, static::$constructorMethodTemplate);
    }
    /**
     * @var string
     */
    protected static $getMethodTemplate =
'/**
 * <description>
 *
 * @return <variableType>
 */
public function <methodName>();
';

    /**
     * @var string
     */
    protected static $setMethodTemplate =
'/**
 * <description>
 *
 * @param <variableType>$<variableName>
 * @return <entity>
 */
public function <methodName>(<methodTypeHint>$<variableName><variableDefault>);
';

    /**
     * @var string
     */
    protected static $addMethodTemplate =
'/**
 * <description>
 *
 * @param <variableType>$<variableName>
 * @return <entity>
 */
public function <methodName>(<methodTypeHint>$<variableName>);
';

    /**
     * @var string
     */
    protected static $removeMethodTemplate =
'/**
 * <description>
 *
 * @param <variableType>$<variableName>
 */
public function <methodName>(<methodTypeHint>$<variableName>);
';

    /**
     * @var string
     */
    protected static $lifecycleCallbackMethodTemplate ='';
    protected static $constructorMethodTemplate = '';

    public function generateEntityClass(ClassMetadataInfo $metadata)
    {
        static::swap();
        $result = parent::generateEntityClass($metadata);
        static::swap();

        return $result;
    }

    /**
     * @param ClassMetadataInfo $metadata
     *
     * @return string
     */
    protected function generateEntityClassName(ClassMetadataInfo $metadata)
    {
        return 'interface ' . $this->getClassName($metadata);
    }

    protected function generateEntityAssociationMappingProperties(ClassMetadataInfo $metadata) {
        return '';
    }

    /**
     * @param ClassMetadataInfo $metadata
     *
     * @return string
     */
    protected function generateEntityFieldMappingProperties(ClassMetadataInfo $metadata)
    {
        return '';
    }

    protected function generateEntityStubMethod(ClassMetadataInfo $metadata, $type, $fieldName, $typeHint = null,  $defaultValue = null)
    {
        if (strtolower($fieldName) === 'id') {
            return '';
        } else {
            return parent::generateEntityStubMethod($metadata, $type, $fieldName, $typeHint, $defaultValue);
        }
    }
}