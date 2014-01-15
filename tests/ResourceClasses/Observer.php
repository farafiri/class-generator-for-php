<?php

namespace ClassGenerator\tests\ResourceClasses;


class Observer implements \SplObserver
{
    public $updates = array();

    public function update(\SplSubject $subject)
    {
        $this->updates[] = $subject->getA() . ',' . $subject->getB();
    }
} 