<?php
/**
 * Created by PhpStorm.
 * User: Rafał
 * Date: 01.01.14
 * Time: 12:00
 */

namespace ClassGenerator\tests\ResourceClasses;


class Observer implements \SplObserver
{
    public $updates = array();

    public function update(\SplSubject $subject)
    {
        $this->updates[] = $subject->getA() . ',' . $subject->getB();
    }
} 