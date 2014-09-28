This is plugin for doctrine in symfony2

It will autogenarate entities from xml/yml files. I didn't put to much effort for integrating it with framework in fancy bundle way
so turning this on is a bit nasty:

```php
    //app\AppKernel.php

    public function boot()
    {
        parent::boot();
        $autoloader = \ClassGenerator\Autoloader::getInstance();
        $autoloader
            ->setCachePath(__DIR__ . '\cache\\' . $this->environment)
            ->setEnabledCache($this->environment != 'dev')
            ->register();
        //$entityClass = 'Acme\MyBundle\Entity\Customer';
        $manager = new \Doctrine\Bundle\DoctrineBundle\Mapping\DisconnectedMetadataFactory($this->getContainer()->get('doctrine'));
        $doctrineEntityAutoloader = new \ClassGenerator\Doctrine\AdapterGenerator($manager);
        $autoloader->getGenerator()->addGenerator($doctrineEntityAutoloader);
    }
```