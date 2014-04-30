<?php

namespace ClassGenerator\tests\ResourceClasses;


class X2 extends X
{
    /**
     * @lazy noLazyEvaluation
     *
     * @return \ClassGenerator\tests\ResourceClasses\X2
     */
    public function createAnotherXWithoutLazyEvaluation()
    {
        return new self($this->getA() + 10, $this->getB() + 100);
    }

    /**
     * @lazy noLazyMethods
     *
     * @return \ClassGenerator\tests\ResourceClasses\X2
     */
    public function createAnotherXWithoutLazyMethods()
    {
        return new self($this->getA() + 10, $this->getB() + 100);
    }
}
