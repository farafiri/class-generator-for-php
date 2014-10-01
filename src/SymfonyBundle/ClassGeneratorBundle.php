<?php
/**
 * Created by PhpStorm.
 * User: Rafał
 * Date: 29.09.14
 * Time: 18:42
 */

namespace ClassGenerator\SymfonyBundle;


class ClassGeneratorBundle extends \Symfony\Component\HttpKernel\Bundle\Bundle {
    public function boot()
    {
        $kernel = $this->container->get('kernel');

        $autoloader = \ClassGenerator\Autoloader::getInstance();
        $autoloader
            ->setCachePath($kernel->getCacheDir())
            ->setEnabledCache($kernel->getEnvironment() != 'dev')
            ->register();

        $manager = new \Doctrine\Bundle\DoctrineBundle\Mapping\DisconnectedMetadataFactory($this->container->get('doctrine'));
        $autoloader->getGenerator()
            ->addGenerator(new Doctrine\Adapter($manager))
            ->addGenerator(new Doctrine\BaseAdapter($manager))
            ->addGenerator(new Doctrine\TraitAdapter($manager))
            ->addGenerator(new Doctrine\InterfaceAdapter($manager));
    }
}