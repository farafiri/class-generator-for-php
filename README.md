class-generator-for-php
=======================

Library for creating and autoloading classes for common patterns like decorator, null object, composite, lazy loading, proxy object
All generated classes can be cached so there is no relevant performance impact.

Examples
========

Let say you have following entity class. Note that the class is not complete (no properties declaration, etc) for sake of readability
```php
class Book implements PriceInterface {
    public function __construct($id) {
        $this->id = $id;
        $this->loadPropertiesFromDb($id);
    }

    /**
     * @return \Author
     */
    public function getAuthor() {
        return new Author($this->authorId);
    }

    /**
     * @return int
     */
    public function getPrice() {
        return $this->price;
    }

    /**
     * @return \Tag[]
     */
    public function getTags() {
        return array_map(function($id) {return new Tag($id);}, $this->tagsIds);
    }
}
```
___________
Decorating:

```php
class DiscountDecorator extends ClassGenerator\BaseDecorator {
    const CG_DECORATED = 'PriceInterface'; //by default you can decorate any class. This const will restrict to given interface or class
    protected $discount;

    public function __construct($discount = 0.8) {
        $this->discount = $discount;
    }

    public function getPrice() {
        return ceil($this->cgDecorated->getPrice() * $this->discount);
    }
}

$book = new DecorableBook($id);
$book->getPrice(); // 100
$book->cgDecorate(new DiscountDecorator(0.9));
$book->getPrice(); // 90

$book instanceof Book //return true
```
___________
NullObject:

```php
$book = new NullBook($id);
//null object gets expected result type from phpDoc and returns proper empty value
$book->getPrice(); // 0
$book->getTags(); // array()
$book->getAuthor(); // NullAuthor
```
___________
LazyConstructor:

```php
$book = new LazyConstructorBook($id);
//no DB query performed yet
$book->getPrice();
//retrieved data from DB and proper price returned
```
_____
Lazy:

```php
$book = new LazyBook($id);
//OR
$book = new LazyBook::cgGet(function() { return new Book($id); });
//no DB query performed yet
$book->getPrice();
//retrieved data from DB and proper price returned
```

Difference between lazy and lazyConstructor:
```php
$lazyConstructorBook = new LazyConstructorBook($id); //no DB query performed yet
$lazyBook = new LazyBook($id); //no DB query performed yet

$author1 = $lazyConstructorBook->getAuthor(); // 2 queries performed (book and author)
$author2 = $lazyBook->getAuthor(); // still no queries performed (LazyAuthor returned)
$author2->getFirstName(); //retrieve book and author data
```
__________
Composite:

```php
$book1 = new Book(1);
$book2 = new Book(2);

$composite = new CompositeBook(array($book1, $book2));

$composite->getAuthor(); //will return composite of book authors (new CompositeAuthor(array($book1->getAuthor(), $book2->getAuthor())))
$composite->getTags(); //will return all tags (array_merge($book1->getTags(), $book2->getTags()))
$composite->getPrice(); // will return first value which evaluate to true
```

We may customize behaviour by adding @composite annotation
For example: if we add @composite sum to price method then $composite->getPrice() will return sum of all prices

______
Set up
------

At first add into require in your composer.json: "farafiri/class-generator-for-php": "dev-master"

Next call \ClassGenerator\Autoloader::register in your code

```php
\ClassGenerator\Autoloader::getInstance()->register();
//this is not a Singleton (you can create instance with new ..., also method setInstance is available)
//but i need instance getter and I don't want make this code DI container dependent
```

If you are using Symfony 2 check Doctrine\README.md

This autoloader is not standalone - it wont load any classes from your php files, you need another loader for this task.
It should be registered as last loader - otherwise it will create new class instead of loading it from your project files. (For example: if you have class \Item and \NullItem then on attempt to use \NullItem it will generate new class instead of loading your implementation)
Make sure your cache is off in development mode and on in production mode (clear cache with every change in your code).
To turn cache on:
```php
\ClassGenerator\Autoloader::getInstance()->setCachePath($cacheDirectoryPath)->setEnabledCache(true);
```
Its good to set $cachePath even if cache is not enabled - created classes will be saved and loaded from filesystem instead of simple code eval.
Thanks to this in case of error error message will look like "Error in cacheFile.php on line X" instead of "Error in eval code" + your IDE should
recognize these classes while they are in files.


