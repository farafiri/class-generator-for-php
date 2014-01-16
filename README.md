class-generator-for-php
=======================

Tool for creating and autoloading classes for common patterns like decorator, null object, composite, lazy loading

Problem:
--------

Following OOP principles and software patterns is usually good idea but quite often it means a lot of (unnecessary) work.
Let say you have class/interface X with methods getA, getB, ... and so on, and you want to write an null object for this class. Your code will look like:

```php
class NullX implements X {
    public function __construct() {}
    
    public function getA() {
        return null;
    }

    public function getB() {
        return new NullY();
    }
    
    //and so on
}
```

If you want decorator then your code will loke like this:

```php
class DecoratorForX implements X {
    protected $decorated;
    
    public function __construct(X $decorated) {
        $this->decorated = $decorated;
    }
    
    public function getA() {
        return $this->decorated->getA();
    }

    public function getB() {
        return $this->decorated->getB();
    }
    
    //and so on
}

class DoSmthDecorator extends DecoratorForX {
    public function yourNewMethod() {
    }
}
```

We have following problems with it:

  1. You have to write it first (and there is no more painful thing for programmer than a work without some creativity) + if any method should return object you must implement additional NullObject class
  2. Whenever you change X you must remember to update NullX and DecoratorForX
  3. If you have class Y (child of X) then check: (new DoSmthDecorator(new Y) instanceof Y) will fail
  
This tool will create and load these classes on the fly when you try to use them.

Set up
------

All what you have to do is calling \ClassGenerator\Autoloader::register

```php
\ClassGenerator\Autoloader::getInstance()->register();
```

This autoloader is not standalone - it wont load any classes from your php files, you need another loader for this task.
It should be registered as last loader - otherwise it will create new class instead of loading it from your project files. (For example: if you have class \Item and \NullItem then on attempt to use \NullItem it will generate new class instead of loading your implementation)
Make sure your cache is off in development mode and on in production mode (clear cache with every change in your code).

