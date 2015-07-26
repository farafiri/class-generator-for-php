<?php
/**
 * Created by PhpStorm.
 * User: Rafał
 * Date: 29.09.14
 * Time: 18:42
 */

namespace ClassGenerator\SymfonyBundle;

class ClassGeneratorBundle extends \Symfony\Component\HttpKernel\Bundle\Bundle {
    protected $autoloader = null;

    public function __construct($kernel = null) {
        if ($kernel) {
            $this->initAutoloader($kernel);
        }
    }

    public function boot()
    {
        if (!$this->autoloader) {
            $this->initAutoloader($this->container->get('kernel'));
        }

        $manager = new \Doctrine\Bundle\DoctrineBundle\Mapping\DisconnectedMetadataFactory($this->container->get('doctrine'));
        $this->autoloader->getGenerator()
            ->addGenerator(new Doctrine\Adapter($manager))
            ->addGenerator(new Doctrine\BaseAdapter($manager))
            ->addGenerator(new Doctrine\TraitAdapter($manager))
            ->addGenerator(new Doctrine\InterfaceAdapter($manager));
    }

    protected function initAutoloader($kernel) {
        $this->autoloader = \ClassGenerator\Autoloader::getInstance();
        $this->autoloader
            ->setCachePath($kernel->getCacheDir())
            ->setEnabledCache($kernel->getEnvironment() != 'dev')
            ->register();
    }

    public function build(\Symfony\Component\DependencyInjection\ContainerBuilder $container)
    {
        $container->addCompilerPass(new DependencyInjection\Main());
    }
}