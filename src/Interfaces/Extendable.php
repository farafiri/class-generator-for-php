<?php
/**
 * Created by PhpStorm.
 * User: Rafał
 * Date: 13.08.15
 * Time: 16:12
 */

namespace ClassGenerator\Interfaces;


interface Extendable
{
    public function cgGetLastDecorated();
    public function cgSetLastDecorated($cgLastDecorated);
} 