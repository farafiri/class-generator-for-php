<?php
/**
 * Created by PhpStorm.
 * User: Rafał
 * Date: 24.05.15
 * Time: 19:44
 */

namespace ClassGenerator\SymfonyBundle\Doctrine;


class Kernel extends AppKernel {
    public function registerBundles() {
        $bundles = array();
        foreach(parent::registerBundles() as $bundle) {
            if ($bundle instanceof ClassGeneratorBundle) {
                break;
            }
            $bundles[] = $bundle;
        }

        return $bundles;
    }
} 