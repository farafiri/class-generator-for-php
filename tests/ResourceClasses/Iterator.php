<?php
/**
 * Created by PhpStorm.
 * User: Rafał
 * Date: 16.01.14
 * Time: 19:08
 */

namespace ClassGenerator\tests\ResourceClasses;


class Iterator implements \Iterator {
    protected $repetitions;
    protected $value;
    protected $position = 0;

    public function __construct($repetitions, $value)
    {
        $this->repetitions = $repetitions;
        $this->value = $value;
    }

    function rewind() {
        $this->position = 0;
    }

    function current() {
        return $this->value;
    }

    function key() {
        return $this->position;
    }

    function next() {
        ++$this->position;
    }

    function valid() {
        return $this->position < $this->repetitions;
    }
}