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
        $doctrineEntityAutoloader = new Doctrine\AdapterGenerator($manager);
        $autoloader->getGenerator()->addGenerator($doctrineEntityAutoloader);
    }
}