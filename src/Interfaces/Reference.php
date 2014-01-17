<?php

namespace ClassGenerator\Interfaces;


interface Reference extends Proxy
{
    /**
     * @return boolean
     */
    public function cgIsHardReference();

    /**
     * @return boolean
     */
    public function cgIsReferenceValid();

    /**
     * @return object
     */
    public function cgGetWeakReference();

    /**
     * @return object
     */
    public function cgGetHardReference();

    /**
     * you can change weak reference to hard reference and hard reference into weak reference
     *
     * @param boolean $isHardReference
     */
    public function cgSetIsHardReference($isHardReference);

    /**
     * @param object $object
     *
     * @return boolean
     */
    public function cgIsReferenceEqualTo($object);
} 