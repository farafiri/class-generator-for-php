<?php
/**
 * Created by PhpStorm.
 * User: Rafał
 * Date: 28.09.14
 * Time: 11:01
 */

namespace ClassGenerator\SymfonyBundle\Doctrine;

abstract class AbstractAdapter {
    const GENERATOR_CLASS = 'ClassGenerator\SymfonyBundle\Doctrine\Generator';
    protected $generator;
    protected $manager;

    public function __construct($manager)
    {
        $this->manager = $manager;

        $generator = static::GENERATOR_CLASS;
        $this->generator = new $generator();
        $this->standardGeneratorSetUp();
    }

    protected function standardGeneratorSetUp() {
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
        //var_dump($this);
        //echo "load[$className] in (" . get_class($this) . ')';
        $metadata = null;
        $metadataClassName = $this->getBaseClassName($className);
        //var_dump($metadataClassName);
        if ($metadataClassName && $metadata = $this->getClassMetadata($metadataClassName)) {
            $metadata->name = $className;
            $metadata->namespace = implode('\\', array_slice(explode('\\', $className), 0, -1));
            if ($metadata->rootEntityName === $metadataClassName) {
                $metadata->rootEntityName = $className;
            }
        }

        if (!$metadata) {
            return null;
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

    abstract protected function getBaseClassName($className);

    protected function getPath($className)
    {
        return 'src';
    }
} 