<?php
/**
 * Created by PhpStorm.
 * User: Rafał
 * Date: 28.09.14
 * Time: 11:01
 */

namespace ClassGenerator\SymfonyBundle\Doctrine;

class AdapterGenerator {
    protected $generator;
    protected $manager;
    protected $namespaceInfix = 'Base';

    public function __construct($manager)
    {
        $this->manager = $manager;

        $this->generator = new Generator();
        $this->generator->setGenerateAnnotations(false);
        $this->generator->setGenerateStubMethods(true);
        $this->generator->setRegenerateEntityIfExists(false);
        $this->generator->setUpdateEntityIfExists(true);
        $this->generator->setNumSpaces(4);
        $this->generator->setAnnotationPrefix('ORM\\');
    }

    /**
     * @param string $className class or interface name
     *
     * @return string|null
     */
    public function generateCodeFor($className)
    {
        //var_dump(class_exists($className));

        $metadataClassName = $className;
        $metadata = $this->getClassMetadata($metadataClassName);
        if ($metadata) {
            $this->generator->setFieldVisibility(Generator::FIELD_VISIBLE_PRIVATE);
        } else {
            $metadataClassName = $this->getBaseClassName($className);
            if ($metadataClassName && $metadata = $this->getClassMetadata($metadataClassName)) {
                $this->generator->setFieldVisibility(Generator::FIELD_VISIBLE_PROTECTED);
                $metadata->name = $className;
                $metadata->namespace = implode('\\', array_slice(explode('\\', $className), 0, -1));
                if ($metadata->rootEntityName === $metadataClassName) {
                    $metadata->rootEntityName = $className;
                }
            }
        }

        $code = $this->generator->generateEntityClass($metadata);

        if (strpos($code, '<?php') == 0) {
            $code = substr($code, 5);
        }
        return ($code);
    }

    protected function getClassMetadata($metadataClassName) {
        $reflection = new \ReflectionClass(get_class($this->manager));
        $method = $reflection->getMethod('getMetadataForClass');
        $method->setAccessible(true);
        $metadata = $method->invoke($this->manager, $metadataClassName)->getMetadata();

        return isset($metadata[0]) ? $metadata[0] : null;
    }

    protected function getBaseClassName($className) {
        if (preg_match('/^(.+)\\\\Base\\\\(.+)$/', $className, $match)) {
            return $match[1] . '\\' . $match[2];
        } else {
            return null;
        }
    }

    protected function getPath($className)
    {
        return 'src';
    }
} 