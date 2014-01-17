<?php
/**
 * Created by PhpStorm.
 * User: Rafał
 * Date: 16.01.14
 * Time: 19:18
 */

namespace ClassGenerator\tests\ResourceClasses;


class IteratorAggregate implements \IteratorAggregate{
    protected $repetitions;
    protected $value;

    public function __construct($repetitions, $value)
    {
        $this->repetitions = $repetitions;
        $this->value = $value;
    }

    public function getIterator()
    {
        return new Iterator($this->repetitions, $this->value);
    }
}
