<?php
/**
 * Created by PhpStorm.
 * User: Rafał
 * Date: 20.03.14
 * Time: 20:04
 */

namespace ClassGenerator\tests\ResourceClasses;


interface XInterface
{
    public function setA($a);
    public function getA();
    public function setB($b);

    /**
     * @return int
     */
    public function getB();

    /**
     * @return \ClassGenerator\tests\ResourceClasses\X
     */
    public function createAnotherX();

    /**
     * @return int[]
     */
    public function getRangeToB();
}