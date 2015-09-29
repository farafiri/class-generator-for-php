<?php
/**
 * Created by PhpStorm.
 * User: Rafał
 * Date: 13.08.15
 * Time: 19:13
 */

namespace ClassGenerator\Interfaces;


interface CGThis {
    /**
     * @return object|null
     */
    public function cgGetThis();

    /**
     * @param object $decorated
     */
    public function cgSetThis($cgThis);
} 