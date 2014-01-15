<?php
/**
 * Created by PhpStorm.
 * User: Rafał
 * Date: 31.12.13
 * Time: 17:19
 */

namespace ClassGenerator\Interfaces;


interface Composite
{
    /**
     * @return object[]
     */
    public function cgGetChildren();

    /**
     * @param object[] $children
     */
    public function cgSetChildren($children);

    /**
     * @param object[]|object $children
     */
    public function cgAddChildren($children);

    /**
     * @param object[]|object|\Closure $children
     *
     * @return object[] collection of removed elements
     */
    public function cgRemoveChildren($children);
} 