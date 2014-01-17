<?php

namespace ClassGenerator\Interfaces;


interface Decorator extends Proxy
{
    /**
     * @return object|null
     */
    public function cgGetDecorated();

    /**
     * @param object $decorated
     */
    public function cgSetDecorated($decorated);
}
