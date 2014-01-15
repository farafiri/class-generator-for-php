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
        return null;
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
```

We have two problems with it:

  1. You have to write it first (and there is no more painful thing for programmer than a work without some creativity)
  2. Whenever you change X you must remember to update NullX and DecoratorForX
  
This tool will create and load these classes on the fly when you try to use them.
