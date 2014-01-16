<?php

namespace ClassGenerator;


abstract class BaseDecorator implements Interfaces\Decorator
{
    /**
     * @var object
     */
    protected $cgDecorated;

    public function cgSetDecorated($decorated)
    {
        $this->cgDecorated = $decorated;
    }

    public function cgGetDecorated()
    {
        return $this->cgDecorated;
    }

    public function cgDecorate($object)
    {
        if ($object instanceof Interfaces\Decorator) {
            $this->cgSetDecorated($object->cgGetDecorated());
            $object->cgSetDecorated($this);
            return $object;
        } else {
            $className = explode('\\', get_class($object));
            $className[count($className) - 1] = 'DecoratorFor' . $className[count($className) - 1];
            $decoratorClassName = implode('\\', $className);
            $this->cgSetDecorated($object);
            return new $decoratorClassName($this);
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