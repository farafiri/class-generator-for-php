<?php

namespace ClassGenerator;


abstract class BaseDecorator implements Interfaces\Decorator, Interfaces\CGThis
{
    const CG_DECORATED = false;

    /**
     * @var object
     */
    protected $cgDecorated;

    /**
     * @var object
     */
    protected $cgThis;

    /**
     * @param object $object
     *
     * @return boolean
     */
    protected function cgCanBeDecorated($object)
    {
        if (static::CG_DECORATED === false) {
            return true;
        }

        while($object instanceof Interfaces\Decorator) {
            $object = $object->cgGetDecorated();
        }

        return is_a($object, static::CG_DECORATED);
    }

    /**
     * @param object $decorated
     */
    public function cgSetDecorated($decorated)
    {
        if (!$this->cgCanBeDecorated($decorated)) {
            $given = $decorated ? 'null' : get_class($decorated);
            throw new Exceptions\Proxy("Cannot decorate " . get_class($decorated) . " with " . get_class($this). ' decorator. ' . $given . ' given.');
        }

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
     * @param object $decorated
     */
    public function cgSetThis($cgThis)
    {
        if (!$this->cgCanBeDecorated($cgThis)) {
            throw new Exceptions\Proxy("Cannot decorate " . get_class($cgThis) . " with " . get_class($this). ' decorator');
        }

        $this->cgThis = $cgThis;
    }

    /**
     * @return object|null
     */
    public function cgGetThis()
    {
        return $this->cgThis;
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

        if ($object instanceof Interfaces\Extendable) {
            $that->cgSetDecorated($object->cgGetDecorated());
            $object->cgSetDecorated($that);
            if ($object->cgGetLastDecorated() === $object) {
                $object->cgSetLastDecorated($this);
            }
            return $object;
        } if ($object instanceof Interfaces\Decorator) {
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

    public function cgExtend($object) {
        $that = $this->cgGetDecorated() ? clone $this: $this;

        if ($object instanceof Interfaces\Extendable) {
            $ld = $object->cgGetLastDecorated();
            $firstExtend = $ld->cgGetDecorated();
            $ld->cgSetDecorated($that);
            $that->cgSetDecorated($firstExtend);
            $decorator = $that;
            while($decorator) {
                if ($decorator instanceof Interfaces\CGThis) {
                    $decorator->cgSetThis($that);
                }
                $decorator = $decorator instanceof Interfaces\Decorator ? $decorator->cgGetDecorated() : null;
            }
        } else {
            throw new Exceptions\Proxy('cgExtended argument bust be instance of Extendable');
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
