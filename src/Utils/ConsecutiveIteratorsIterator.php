<?php
/**
 * Created by PhpStorm.
 * User: Rafał
 * Date: 16.01.14
 * Time: 20:15
 */

namespace ClassGenerator\Utils;


class ConsecutiveIteratorsIterator implements \RecursiveIterator, \OuterIterator
{
    protected $iteratorOfIterators;
    protected $currentIterator;

    public function __construct($iteratorOfIterators)
    {
        while($iteratorOfIterators instanceof \IteratorAggregate) {
            $iteratorOfIterators = $iteratorOfIterators->getIterator();
        }

        $this->iteratorOfIterators = $iteratorOfIterators;
    }

    function rewind() {
        $this->iteratorOfIterators->rewind();
        while ($this->iteratorOfIterators->valid()) {
            $this->currentIterator = $this->iteratorOfIterators->current();
            while($this->currentIterator instanceof \IteratorAggregate) {
                $this->currentIterator = $this->currentIterator->getIterator();
            }
            $this->currentIterator->rewind();
            if ($this->currentIterator->valid()) {
                return ;
            }
            $this->iteratorOfIterators->next();
        }

        $this->currentIterator = null;
    }

    function current() {
        return $this->currentIterator ? $this->currentIterator->current() : null;
    }

    function key() {
        return $this->currentIterator ? $this->currentIterator->key() : null;
    }

    function next() {
        if ($this->currentIterator) {
            $this->currentIterator->next();
            if ($this->currentIterator->valid()) {
                return;
            }

            $this->iteratorOfIterators->next();
            while ($this->iteratorOfIterators->valid()) {
                $this->currentIterator = $this->iteratorOfIterators->current();
                while($this->currentIterator instanceof \IteratorAggregate) {
                    $this->currentIterator = $this->currentIterator->getIterator();
                }
                $this->currentIterator->rewind();
                if ($this->currentIterator->valid()) {
                    return ;
                }
                $this->iteratorOfIterators->next();
            }
        }

        $this->currentIterator = null;
    }

    function valid() {
        return (boolean) $this->currentIterator;
    }

    public function getInnerIterator()
    {
        if ($this->currentIterator instanceof \OuterIterator) {
            $this->currentIterator->getInnerIterator();
        } else {
            return null;
        }
    }

    function getChildren() {
        if ($this->currentIterator instanceof \RecursiveIterator) {
            $this->currentIterator->getChildren();
        } else {
            return array();
        }
    }

    function hasChildren() {
        if ($this->currentIterator instanceof \RecursiveIterator) {
            $this->currentIterator->hasChildren();
        } else {
            return false;
        }
    }
}
