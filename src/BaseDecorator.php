<?php

namespace ClassGenerator;


abstract class BaseDecorator implements Interfaces\Decorator
{
    /**
     * @var object
     */
    protected $cgDecorated;

    /**
     * @param object $decorated
     */
    public function cgSetDecorated($decorated)
    {
        $this->cgDecorated = $decorated;
    }

    /**
     * @return object|null
     */
    public function cgGetDecorated()
    {
        return $this->cgDecorated;
    }

    /**
     * @return object|null
     */
    public function cgGetProxifiedObject()
    {
        return $this->cgDecorated;
    }

    /**
     * @param object $object
     *
     * @return object
     */
    public function cgDecorate($object)
    {
        $that = $this->cgGetDecorated() ? clone $this: $this;

        if ($object instanceof Interfaces\Decorator) {
            $that->cgSetDecorated($object->cgGetDecorated());
            $object->cgSetDecorated($that);
            return $object;
        } else {
            $className = explode('\\', get_class($object));
            $className[count($className) - 1] = 'DecoratorFor' . $className[count($className) - 1];
            $decoratorClassName = implode('\\', $className);
            $that->cgSetDecorated($object);
            return new $decoratorClassName($that);
        }
    }

    public function __call($methodName, $arguments)
    {
        return call_user_func_array(array($this->cgDecorated, $methodName), $arguments);
    }

    public function  __set($name, $value)
    {
        $this->cgDecorated->$name = $value;
    }

    public function __get($name)
    {
        return $this->cgDecorated->$name;
    }

    public function __isset($name)
    {
        return isset($this->cgDecorated->$name);
    }

    public function __unset($name)
    {
        unset($this->cgDecorated->$name);
    }

    public function __clone()
    {
        if (is_object($this->cgDecorated)) {
            $this->cgDecorated = clone $this->cgDecorated;
        }
    }
}
