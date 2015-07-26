<?php
/**
 * Created by PhpStorm.
 * User: Rafał
 * Date: 26.07.15
 * Time: 15:15
 */

namespace ClassGenerator\SymfonyBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Reference;

class Main implements CompilerPassInterface {
    public function process(ContainerBuilder $container)
    {
        $mainGenerator = \ClassGenerator\Autoloader::getInstance()->getGenerator();
        $generators = $mainGenerator->getGenerators();
        $decorable = $generators['decorable'];
        foreach ($container->findTaggedServiceIds('cg.decorated') as $id => $tags) {
            $definition = $container->findDefinition($id);
            $definition->setClass($decorable->getClassName($definition->getClass()));
            foreach($tags as $tag) {
                $definition->addMethodCall(
                    'cgDecorateWith',
                    array(new Reference($tag['by']))
                );
            }
        }
    }
} 