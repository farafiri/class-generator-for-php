<?php

/** @noinspection PhpUndefinedNamespaceInspection */
use ClassGenerator\tests\ResourceClasses;

class ClassGeneratorTest extends \PHPUnit_Framework_TestCase
{
    static $loader;
    static $generator;

    static public function setUpBeforeClass()
    {
        static::$loader = \ClassGenerator\Autoloader::getInstance()->setCachePatch(__DIR__ . DIRECTORY_SEPARATOR . 'cache')->register();
        static::$generator = static::$loader->getGenerator();
    }

    public function testDecorator()
    {
        $this->assertTrue(class_exists('ClassGenerator\tests\ResourceClasses\DecoratorForX'));

        $x = new ResourceClasses\X();
        $decorator = new ResourceClasses\DecoratorForX($x);

        $this->assertEquals(10, $decorator->getA());

        $x->a = 32;
        $this->assertEquals(32, $decorator->getA());
    }

    public function testDecoratorIsInstanceOfBase()
    {
        $x = new ResourceClasses\X();
        $decorator = new ResourceClasses\DecoratorForX($x);

        $this->assertTrue($decorator instanceof ResourceClasses\X);
    }

    public function testBaseDecorator()
    {
        $this->assertTrue(class_exists('ClassGenerator\tests\ResourceClasses\BaseDecoratorForX'));

        $x = new ResourceClasses\X();
        $decorator = new ResourceClasses\BaseDecoratorForX($x);

        $this->assertEquals(10, $decorator->getA());

        $x->a = 32;
        $this->assertEquals(32, $decorator->getA());
    }

    public function testNull()
    {
        $this->assertTrue(class_exists('ClassGenerator\tests\ResourceClasses\NullX'));
        $nullObject = new ResourceClasses\NullX();

        $this->assertEquals(null, $nullObject->getA());
        $this->assertEquals(0, $nullObject->getB());
    }

    public function testNullObjectIsInstanceOfBase()
    {
        $n = new ResourceClasses\NullX();

        $this->assertTrue($n instanceof ResourceClasses\X);
    }

    public function testBaseNull()
    {
        $this->assertFalse(class_exists('ClassGenerator\tests\ResourceClasses\NullXBase'));
        $this->assertTrue(class_exists('ClassGenerator\tests\ResourceClasses\BaseNullX'));
        $nullObject = new ResourceClasses\BaseNullX();

        $this->assertEquals(null, $nullObject->getA());
        $this->assertEquals(0, $nullObject->getB());
    }

    public function testNullWithIteratorShouldProduceIteratorWithoutIterations()
    {
        $emptyIterator = new \NullIterator();

        foreach($emptyIterator as $x) {
            $this->fail("");
        };
    }

    public function testComposite()
    {
        $x1 = new ResourceClasses\X();
        $x2 = new ResourceClasses\X();

        $composite = new ResourceClasses\CompositeX(array($x1, $x2));
        $composite->setA(100);
        $composite->setB(101);

        $this->assertEquals(100, $x1->getA());
        $this->assertEquals(100, $x2->getA());
        $this->assertEquals(101, $x1->getB());
        $this->assertEquals(101, $x2->getB());
    }

    public function testCompositeIsInstanceOfBase()
    {
        $composite = new ResourceClasses\CompositeX(array());

        $this->assertTrue($composite instanceof ResourceClasses\X);
    }

    public function testCompositeShouldReturnNonFalseValue()
    {
        $x1 = new ResourceClasses\X();
        $x1->setA(false);
        $x1->setB(42);
        $x2 = new ResourceClasses\X();
        $x2->setA("abc");
        $x2->setB(0);

        $composite = new ResourceClasses\CompositeX(array($x1, $x2));

        $this->assertEquals("abc", $composite->getA());
        $this->assertEquals("42", $composite->getB());
    }

    public function testCompositeShouldReturnFirstValue()
    {
        $x1 = new ResourceClasses\X();
        $x1->setA(false);
        $x1->setB(34);
        $x2 = new ResourceClasses\X();
        $x2->setA("");
        $x2->setB(58);

        $composite = new ResourceClasses\CompositeX(array($x1, $x2));

        $this->assertEquals(false, $composite->getA());
        $this->assertEquals(34, $composite->getB());
    }

    public function testCompositeShouldReturnFirstNonNullValueFromFalsyValues()
    {
        $x1 = new ResourceClasses\X();
        $x1->setA(false);
        $x1->setB(null);
        $x2 = new ResourceClasses\X();
        $x2->setA(null);
        $x2->setB(0);

        $composite = new ResourceClasses\CompositeX(array($x1, $x2));

        $this->assertEquals(false, $composite->getA());
        $this->assertEquals(0, $composite->getB());
    }

    public function testCompositeGetChildren()
    {
        $x1 = new ResourceClasses\X();
        $x2 = new ResourceClasses\X();
        $x3 = new ResourceClasses\X();

        $composite = new ResourceClasses\CompositeX(array($x1, $x2, $x3));
        $this->assertSame(array($x1, $x2, $x3), $composite->cgGetChildren());
    }

    public function testCompositeAddChildrenWithSingleItem()
    {
        $x1 = new ResourceClasses\X();
        $x2 = new ResourceClasses\X();
        $x3 = new ResourceClasses\X();

        $composite = new ResourceClasses\CompositeX(array($x1, $x2));

        $composite->cgAddChildren($x3);
        $this->assertSame(array($x1, $x2, $x3), $composite->cgGetChildren());
    }

    public function testCompositeAddChildrenWithArray()
    {
        $x1 = new ResourceClasses\X();
        $x2 = new ResourceClasses\X();
        $x3 = new ResourceClasses\X();

        $composite = new ResourceClasses\CompositeX(array($x1));

        $composite->cgAddChildren(array($x2, $x3));
        $this->assertSame(array($x1, $x2, $x3), $composite->cgGetChildren());
    }

    public function testCompositeAddChildrenKeepChildrenUnique()
    {
        $x1 = new ResourceClasses\X();
        $x2 = new ResourceClasses\X();
        $x3 = new ResourceClasses\X();

        $composite = new ResourceClasses\CompositeX(array($x1, $x2));

        $composite->cgAddChildren(array($x3, $x2));
        $this->assertSame(array($x1, $x2, $x3), $composite->cgGetChildren());
    }

    public function testCompositeSetChildren()
    {
        $x1 = new ResourceClasses\X();
        $x2 = new ResourceClasses\X();

        $composite = new ResourceClasses\CompositeX(array($x1));

        $composite->cgSetChildren(array($x2));
        $this->assertSame(array($x2), $composite->cgGetChildren());
    }

    public function testCompositeRemoveChildren()
    {
        $x1 = new ResourceClasses\X();
        $x2 = new ResourceClasses\X();
        $x3 = new ResourceClasses\X();

        $composite = new ResourceClasses\CompositeX(array($x1, $x2));

        $this->assertSame(array($x2), $composite->cgRemoveChildren(array($x3, $x2)));
        $this->assertSame(array($x1), $composite->cgGetChildren());
    }

    public function testCompositeRemoveChildrenWithCallable()
    {
        $x1 = new ResourceClasses\X();
        $x2 = new ResourceClasses\X();
        $x3 = new ResourceClasses\X();

        $composite = new ResourceClasses\CompositeX(array($x1, $x2, $x3));

        $this->assertSame(array($x2), $composite->cgRemoveChildren(function($a) use ($x2) {
            return $a === $x2;
        }));

        $this->assertSame(array($x1, $x3), $composite->cgGetChildren());
    }

    public function testCompositeGetComposite()
    {
        $x = new ResourceClasses\X();
        $xy = new ResourceClasses\XY();

        $CompositeX = new ResourceClasses\CompositeX(array($x, $xy));

        $xyComposite = $CompositeX->cgGetComposite('ClassGenerator\tests\ResourceClasses\XY');

        $this->assertTrue($xyComposite instanceof ResourceClasses\CompositeXY);
        $this->assertSame(array($xy), $xyComposite->cgGetChildren());
        $this->assertTrue(method_exists($xyComposite, 'getC'));
    }

    public function testWithMethodOverriding()
    {
        $x = new ResourceClasses\MethodOverridedX(array('getA' => function() { return $this->a + 1;}));

        $this->assertEquals(11, $x->getA());

        $x->setA(30);
        $this->assertEquals(31, $x->getA());
    }

    public function testWithMethodOverridingWithOneClosureOverridesAllMethods()
    {
        $x = new ResourceClasses\MethodOverridedX(function($a = '') { return "abc" . $a;});

        $this->assertEquals("abc", $x->getA());
        $this->assertEquals("abcd", $x->setB("d"));
    }

    public function testWithMethodOverridingWithConstructorParameters()
    {
        $x = new ResourceClasses\MethodOverridedX(array(), 123, 234);

        $this->assertEquals(123, $x->getA());
        $this->assertEquals(234, $x->getB());
    }

    public function testCompound()
    {
        $x = new ResourceClasses\X(100);
        $d = new ResourceClasses\MethodOverridedDecoratorForX(array('getA' => function () {return parent::getA() + 1;}), $x);

        $this->assertEquals(100, $x->getA());
        $this->assertEquals(101, $d->getA());
    }

    public function testSubjectIsInstanceofSplSubject()
    {
        $x = new ResourceClasses\SubjectX();
        $this->assertTrue($x instanceof \SplSubject);
    }

    public function testSubject()
    {
        $observer1 = new ResourceClasses\Observer();
        $observer2 = new ResourceClasses\Observer();

        $subject1 = new ResourceClasses\SubjectX(1, 2);
        $subject2 = new ResourceClasses\SubjectXY(3, 4);

        $subject1->attach($observer1);
        $subject2->attach($observer1);
        $subject1->attach($observer2);

        $subject1->setA(11);
        $this->assertEquals(array('11,2'), $observer1->updates);
        $this->assertEquals(array('11,2'), $observer2->updates);

        $subject2->setB(12);
        $this->assertEquals(array('11,2', '3,12'), $observer1->updates);
        $this->assertEquals(array('11,2'), $observer2->updates);

        $subject1->setB(13);
        $this->assertEquals(array('11,2', '3,12', '11,13'), $observer1->updates);
        $this->assertEquals(array('11,2', '11,13'), $observer2->updates);
    }

    public function testLazyConstructor()
    {
        $x = new ResourceClasses\LazyConstructorX(101, 202);

        $this->assertEquals(0, $x->a);
        $this->assertEquals(0, $x->b);

        $this->assertEquals(101, $x->getA());
        $this->assertEquals(202, $x->getB());

        $this->assertEquals(101, $x->a);
        $this->assertEquals(202, $x->b);
    }

    public function testLazyIsInstanceOfLazy()
    {
        $x = new ResourceClasses\LazyX(new ResourceClasses\X(1, 2));
        $this->assertTrue($x instanceof \ClassGenerator\Interfaces\Lazy);
    }

    public function testLazyProducesInstanceOfLazy()
    {
        $x = new ResourceClasses\LazyX(new ResourceClasses\X(1, 2));
        $x2 = $x->createAnotherX();
        $this->assertTrue($x2 instanceof \ClassGenerator\Interfaces\Lazy);
    }

    public function testLazy()
    {
        $x = new ResourceClasses\LazyX(new ResourceClasses\X(1, 2));
        ResourceClasses\X::$constructorCount = 0;

        $x2 = $x->createAnotherX()->createAnotherX()->createAnotherX()->createAnotherX();

        $this->assertEquals(0, ResourceClasses\X::$constructorCount);
        $this->assertEquals(4, $x2->getA());
        $this->assertEquals(8, $x2->getB());
        $this->assertEquals(4, ResourceClasses\X::$constructorCount);
    }

    public function testGeneratorLazy()
    {
        $x = static::$generator->lazy(function() {return new ResourceClasses\X(1, 2); }, 'ClassGenerator\tests\ResourceClasses\X', true);
        ResourceClasses\X::$constructorCount = 0;

        $x2 = $x->createAnotherX()->createAnotherX()->createAnotherX()->createAnotherX();

        $this->assertEquals(0, ResourceClasses\X::$constructorCount);
        $this->assertEquals(4, $x2->getA());
        $this->assertEquals(8, $x2->getB());
        //here is difference between testGeneratorLazy() and testLazy() because creating first object is also delayed
        $this->assertEquals(5, ResourceClasses\X::$constructorCount);
    }

    public function testGeneratorLazyMethods()
    {
        $x = static::$generator->lazyMethods(new ResourceClasses\X(1, 2));
        ResourceClasses\X::$constructorCount = 0;

        $x2 = $x->createAnotherX()->createAnotherX()->createAnotherX()->createAnotherX();

        $this->assertEquals(0, ResourceClasses\X::$constructorCount);
        $this->assertEquals(4, $x2->getA());
        $this->assertEquals(8, $x2->getB());
        $this->assertEquals(4, ResourceClasses\X::$constructorCount);
    }

    public function testGetProxifiedObject()
    {
        $x = new ResourceClasses\LazyX(new ResourceClasses\X(1, 2));
        ResourceClasses\X::$constructorCount = 0;

        $x2 = $x->createAnotherX()->createAnotherX();
        $this->assertEquals(0, ResourceClasses\X::$constructorCount);

        $this->assertFalse($x2->cgGetProxifiedObject() instanceof \ClassGenerator\Interfaces\Lazy);
        $this->assertEquals(2, ResourceClasses\X::$constructorCount);
    }

    public function testLazyWithNoLazyEvaluation()
    {
        $x = new ResourceClasses\LazyX2(new ResourceClasses\X2(1, 2));

        ResourceClasses\X::$constructorCount = 0;

        $x2 = $x->createAnotherXWithoutLazyEvaluation();
        $this->assertEquals(1, ResourceClasses\X::$constructorCount);
        $this->assertTrue($x2 instanceof \ClassGenerator\Interfaces\Lazy);
    }

    public function testLazyWithNoLazyMethods()
    {
        $x = new ResourceClasses\LazyX2(new ResourceClasses\X2(1, 2));

        ResourceClasses\X::$constructorCount = 0;

        $x2 = $x->createAnotherXWithoutLazyMethods();
        $this->assertEquals(0, ResourceClasses\X::$constructorCount);

        $x3 = $x2->createAnotherXWithoutLazyMethods();
        $this->assertEquals(2, ResourceClasses\X::$constructorCount);
        $this->assertFalse($x3 instanceof \ClassGenerator\Interfaces\Lazy);
    }


    public function testReferenceIsInstanceofOrigin()
    {
        $hard = static::$generator->hardReference(new ResourceClasses\X(1, 2));
        $this->assertTrue($hard instanceof ResourceClasses\X);

        $weak = $hard->cgGetWeakReference();
        $this->assertTrue($weak instanceof ResourceClasses\X);
    }

    public function testAllReferencesPointsToSameObject()
    {
        $hard = static::$generator->hardReference(new ResourceClasses\X(1, 2));
        $weak = $hard->cgGetWeakReference();

        $this->assertEquals(1, $weak->getA());

        $weak->setA(23);
        $this->assertEquals(23, $hard->getA());

        $weak->cgGetHardReference()->setA(34);
        $this->assertEquals(34, $weak->getA());
    }

    public function testIsHardReference()
    {
        $hard = static::$generator->hardReference(new ResourceClasses\X(1, 2));
        $this->assertTrue($hard->cgIsHardReference());

        $weak = $hard->cgGetWeakReference();
        $this->assertFalse($weak->cgIsHardReference());
    }

    public function testIsReferenceValid()
    {
        $hard = static::$generator->hardReference(new ResourceClasses\X(1, 2));
        $this->assertTrue($hard->cgIsReferenceValid());

        $weak = $hard->cgGetWeakReference();
        $this->assertTrue($hard->cgIsReferenceValid());
        $this->assertTrue($weak->cgIsReferenceValid());
        $this->assertTrue($hard->cgGetWeakReference()->cgIsReferenceValid());

        unset($hard);
        gc_collect_cycles();

        $this->assertFalse($weak->cgIsReferenceValid());
    }

    public function testIsReferenceValidWithTwoHardReferences()
    {
        $hard = static::$generator->hardReference(new ResourceClasses\X(1, 2));
        $weak = $hard->cgGetWeakReference();
        $hard2 = $weak->cgGetHardReference();

        unset($hard);
        $this->assertTrue($weak->cgIsReferenceValid());

        unset($hard2);
        $this->assertFalse($weak->cgIsReferenceValid());
    }

    public function testIsReferenceEqualToOrigin()
    {
        $origin = new ResourceClasses\X(1, 2);
        $hard = static::$generator->hardReference($origin);
        $this->assertTrue($hard->cgIsReferenceEqualTo($origin));
        $this->assertTrue($hard->cgGetWeakReference()->cgIsReferenceEqualTo($origin));

        $origin2 = new ResourceClasses\X(1, 2);
        $hard2 = static::$generator->hardReference($origin2);
        $this->assertTrue($hard2->cgIsReferenceEqualTo($origin2));

        $this->assertFalse($hard2->cgIsReferenceEqualTo($origin));
        $this->assertFalse($hard2->cgGetWeakReference()->cgIsReferenceEqualTo($origin));
    }

    public function testIsReferenceEqualToAnotherReference()
    {
        $hard = static::$generator->hardReference(new ResourceClasses\X(1, 2));
        $weak = $hard->cgGetWeakReference();

        $this->assertTrue($weak->cgIsReferenceEqualTo($hard));
        $this->assertTrue($hard->cgIsReferenceEqualTo($hard));
        $this->assertTrue($hard->cgIsReferenceEqualTo($weak));
        $this->assertTrue($weak->cgIsReferenceEqualTo($weak));
        $this->assertTrue($hard->cgIsReferenceEqualTo($weak->cgGetHardReference()));

        $hard2 = static::$generator->hardReference(new ResourceClasses\X(1, 2));
        $weak2 = $hard2->cgGetWeakReference();
        $this->assertTrue($weak2->cgIsReferenceEqualTo($hard2));
        $this->assertTrue($hard2->cgIsReferenceEqualTo($weak2));

        $this->assertFalse($weak->cgIsReferenceEqualTo($hard2));
        $this->assertFalse($hard->cgIsReferenceEqualTo($hard2));
        $this->assertFalse($hard2->cgIsReferenceEqualTo($weak));
        $this->assertFalse($weak2->cgIsReferenceEqualTo($hard));
        $this->assertFalse($weak2->cgIsReferenceEqualTo($weak));
    }

    public function testCloneOnComposite()
    {
        $x1 = new ResourceClasses\X();
        $composite = new ResourceClasses\CompositeX(array($x1));

        $composite2 = clone $composite;
        $composite2Children = $composite2->cgGetChildren();
        $this->assertNotSame($x1, $composite2Children[0]);
        $this->assertEquals($x1, $composite2Children[0]);
    }

    public function testCloneOnDecorator()
    {
        $x1 = new ResourceClasses\X();
        $decorator = new ResourceClasses\DecoratorForX($x1);

        $decorator2 = clone $decorator;
        $this->assertNotSame($x1, $decorator2->cgGetDecorated());
        $this->assertEquals($x1, $decorator2->cgGetDecorated());
    }

    public function testCloneLazyConstructor()
    {
        $std = new \stdClass();
        $x1 = new ResourceClasses\LazyConstructorX($std, $std);
        $x2 = clone $x1;

        $this->assertNotSame($x1->getA(), $x2->getA());
        $this->assertEquals($x1->getA(), $x2->getA());
        $this->assertSame($x1->getB(), $x2->getB());
    }

    public function testCloneOnReferenceMakesFromSoftHardRef()
    {
        $origin = new ResourceClasses\X(1, 2);
        $hard = static::$generator->hardReference($origin);
        $soft = $hard->cgGetWeakReference();

        $softClone = clone $soft;
        $this->assertFalse($softClone->cgIsReferenceEqualTo($soft));
        $this->assertTrue($softClone->cgIsReferenceValid());
        $this->assertTrue($softClone->cgIsHardReference());

        $softCloneSoft = $softClone->cgGetWeakReference();
        $this->assertEquals($soft, $softCloneSoft);
        $this->assertTrue($softCloneSoft->cgIsReferenceValid());
        $this->assertFalse($softCloneSoft->cgIsHardReference());

        unset($hard);
        $this->assertFalse($soft->cgIsReferenceValid());
        $this->assertTrue($softCloneSoft->cgIsReferenceValid());

        unset($softClone);
        $this->assertFalse($softCloneSoft->cgIsReferenceValid());
    }

    public function testSleepWakeupOnComposite()
    {
        $composite1 = new ResourceClasses\CompositeX(array(new ResourceClasses\X()));
        $composite2 = unserialize(serialize($composite1));

        $this->assertEquals($composite1, $composite2);
    }

    public function testSleepWakeupOnDecorator()
    {
        $decorator1 = new ResourceClasses\DecoratorForX(new ResourceClasses\X());
        $decorator2 = unserialize(serialize($decorator1));

        $this->assertEquals($decorator1, $decorator2);
    }

    public function testSleepWakeupLazyConstructor()
    {
        $x1 = new ResourceClasses\LazyConstructorX(new \stdClass(), 4);
        $x2 = unserialize(serialize($x1));

        $this->assertEquals($x1, $x2);
    }

    public function testSleepWakeupOnReferenceMakesFromSoftHardRef()
    {
        $origin = new ResourceClasses\X(1, 2);
        $hard = static::$generator->hardReference($origin);
        $soft = $hard->cgGetWeakReference();

        $unserialized = unserialize(serialize($soft));
        $this->assertTrue($unserialized->cgIsReferenceValid());
        $this->assertTrue($unserialized->cgIsHardReference());

        $unserializedSoft = $unserialized->cgGetWeakReference();
        $this->assertTrue($unserializedSoft->cgIsReferenceValid());
        $this->assertFalse($unserializedSoft->cgIsHardReference());
        $this->assertEquals($soft, $unserializedSoft);

        unset($unserialized);

        $this->assertFalse($unserializedSoft->cgIsReferenceValid());
    }

    public function testSleepWakeupOnLazy()
    {
        $x = static::$generator->lazyMethods(new ResourceClasses\X(101, 102));
        $x2 = unserialize(serialize($x));

        $this->assertEquals(101, $x2->getA());
        $this->assertEquals(102, $x2->getB());
    }

    public function testSleepWakeupOnSubject()
    {
        $observer1 = new ResourceClasses\Observer();
        $subject1 = new ResourceClasses\SubjectX(201, 202);
        $subject1->attach($observer1);

        $subject2 = unserialize(serialize($subject1));

        $this->assertEquals(201, $subject2->getA());
        $this->assertEquals(202, $subject2->getB());

        $rp = new \ReflectionProperty('ClassGenerator\tests\ResourceClasses\SubjectX', 'cgObservers');
        $rp->setAccessible(true);
        $this->assertEquals(0, $rp->getValue($subject2)->count());
    }

    public function testCompositeOfIteratorShouldIterateOverAllChildren()
    {
        $iterator1 = new ResourceClasses\Iterator(0, 1);
        $iterator2 = new ResourceClasses\Iterator(2, 10);
        $iterator3 = new ResourceClasses\Iterator(3, 100);
        $iterator4 = new ResourceClasses\Iterator(0, 1000);
        $iterator5 = new ResourceClasses\Iterator(4, 10000);
        $composite = new ResourceClasses\CompositeIterator(array($iterator1, $iterator2, $iterator3, $iterator4, $iterator5));

        $sum = 0;
        foreach($composite as $value) {
            $sum += $value;
        }

        $this->assertEquals(40320, $sum);
    }

    public function testCompositeOfIteratorAggregateShouldIterateOverAllChildren()
    {
        $iterator1 = new ResourceClasses\IteratorAggregate(0, 1);
        $iterator2 = new ResourceClasses\IteratorAggregate(2, 10);
        $iterator3 = new ResourceClasses\IteratorAggregate(3, 100);
        $iterator4 = new ResourceClasses\IteratorAggregate(0, 1000);
        $iterator5 = new ResourceClasses\IteratorAggregate(4, 10000);
        $composite = new ResourceClasses\CompositeIteratorAggregate(array($iterator1, $iterator2, $iterator3, $iterator4, $iterator5));

        $sum = 0;
        foreach($composite as $value) {
            $sum += $value;
        }

        $this->assertEquals(40320, $sum);
    }

    public function testCompositeIfMethodReturnsArrayThenCompositeShouldReturnMergedArray()
    {
        $x1 = new ResourceClasses\X(1, 2);
        $x2 = new ResourceClasses\X(3, 4);
        $x3 = new ResourceClasses\X(5, 6);
        $composite = new ResourceClasses\CompositeX(array($x1, $x2, $x3));

        $this->assertEquals(array(1, 2, 1, 2, 3, 4, 1, 2, 3, 4, 5, 6), $composite->getRangeToB());
    }

    public function testCompositeIfMethodReturnsObjectThenCompositeShouldBeReturned()
    {
        $x1 = new ResourceClasses\X(1, 2);
        $x2 = new ResourceClasses\X(3, 4);
        $x3 = new ResourceClasses\X(5, 6);
        $composite = new ResourceClasses\CompositeX(array($x1, $x2, $x3));

        $resultOfCreateAnotherX = $composite->createAnotherX();
        $this->assertTrue($resultOfCreateAnotherX instanceof ResourceClasses\CompositeX);
        $this->assertEquals(array(
            new ResourceClasses\X(3, -1),
            new ResourceClasses\X(7, -1),
            new ResourceClasses\X(11, -1),
        ), $resultOfCreateAnotherX->cgGetChildren());
    }

    public function testInvalidReferenceThrowsErrorOnAttemptToUse()
    {
        $hard = static::$generator->hardReference(new ResourceClasses\X(1, 2));
        $weak = $hard->cgGetWeakReference();
        unset($hard);
        gc_collect_cycles();

        $this->setExpectedException('ClassGenerator\Exceptions\Proxy');
        $weak->getA();
    }

    public function testInvalidReferenceMayBehaveLikeNullObject()
    {
        $hard = static::$generator->hardReference(new ResourceClasses\X(1, 2));
        $weak = $hard->cgGetWeakReference();
        $weak->cgSetBehaveLikeNullObject(true);
        unset($hard);
        gc_collect_cycles();

        $this->assertNull($weak->getA());
        $this->assertEquals(0, $weak->getB());
    }

    public function testInvalidReferenceMayBehaveLikeNullObjectCheckWithClone()
    {
        $hard = static::$generator->hardReference(new ResourceClasses\X(1, 2));
        $weak = $hard->cgGetWeakReference();
        $weak->cgSetBehaveLikeNullObject(true);
        unset($hard);
        gc_collect_cycles();

        $cloneOfWeak = clone $weak;

        $this->assertNull($cloneOfWeak->getA());
        $this->assertEquals(0, $cloneOfWeak->getB());
    }

    public function testInvalidReferenceMayBehaveLikeNullObjectCheckWithSerialize()
    {
        $hard = static::$generator->hardReference(new ResourceClasses\X(1, 2));
        $weak = $hard->cgGetWeakReference();
        $weak->cgSetBehaveLikeNullObject(true);
        unset($hard);
        gc_collect_cycles();

        $serializedWeak = unserialize(serialize($weak));

        $this->assertNull($serializedWeak->getA());
        $this->assertEquals(0, $serializedWeak->getB());
    }

    public function testEvenIfReferenceBehaveLikeNullObjectIsSetStillThrowErrorOnAttemptOfGetReference()
    {
        $hard = static::$generator->hardReference(new ResourceClasses\X(1, 2));
        $weak = $hard->cgGetWeakReference();
        $weak->cgSetBehaveLikeNullObject(true);
        unset($hard);
        gc_collect_cycles();

        $this->setExpectedException('ClassGenerator\Exceptions\Proxy');
        $weak->cgGetWeakReference();
    }

    public function testReferenceAddReleaseEvent()
    {
        $hard = static::$generator->hardReference(new ResourceClasses\X(1, 2));
        $weak = $hard->cgGetWeakReference();

        $releaseEvent = function($reference) use (&$weakReferences) {
            unset($weakReferences[$reference->getA()]);
        };

        $weakReferences[$weak->getA()] = $weak;
        $weak->cgAddReleaseEvent($releaseEvent);

        $this->assertTrue(isset($weakReferences[1]));
        unset($hard);

        $this->assertTrue(empty($weakReferences[1]));
    }

    public function testReferenceRemoveReleaseEvent()
    {
        $hard = static::$generator->hardReference(new ResourceClasses\X(1, 2));
        $weak = $hard->cgGetWeakReference();

        $releaseEvent = function($reference) use (&$weakReferences) {
            unset($weakReferences[$reference->getA()]);
        };

        $weakReferences[$weak->getA()] = $weak;
        $weak->cgAddReleaseEvent($releaseEvent);
        $weak->cgRemoveReleaseEvent($releaseEvent);

        $this->assertTrue(isset($weakReferences[1]));
        unset($hard);

        $this->assertTrue(isset($weakReferences[1]));
    }

    public function testSerializeWithNullObject()
    {
        $x = new ResourceClasses\NullSerialize(1, 2);

        $this->assertEquals($x, unserialize(serialize($x)));
    }

    public function testSerializeWithComposite()
    {
        $x1 = new ResourceClasses\Serialize(1, 2);
        $x2 = new ResourceClasses\Serialize(3, 4);
        $composite = new ResourceClasses\CompositeSerialize(array($x1, $x2));

        $this->assertEquals($composite, unserialize(serialize($composite)));
    }

    public function testSerializeWithDecorator()
    {
        $x = new ResourceClasses\Serialize(1, 2);
        $decorator = new ResourceClasses\DecoratorForSerialize($x);

        $this->assertEquals($decorator, unserialize(serialize($decorator)));
    }

    public function testSerializeWithLazy()
    {
        $x = self::$generator->lazy(function() {
            return new ResourceClasses\Serialize(1, 2);
        }, 'ClassGenerator\tests\ResourceClasses\Serialize');

        $this->assertEquals($x->getA(), unserialize(serialize($x))->getA());
        $this->assertEquals($x->getB(), unserialize(serialize($x))->getB());
    }

    public function testSerializeWithLazyConstructor()
    {
        $x = new ResourceClasses\LazyConstructorSerialize(1, 2);

        $this->assertEquals($x, unserialize(serialize($x)));
    }

    public function testSerializeWithReference()
    {
        $origin = new ResourceClasses\Serialize(1, 2);
        $hard = static::$generator->hardReference($origin);
        $soft = $hard->cgGetWeakReference();

        $unserialized = unserialize(serialize($soft));
        $this->assertTrue($unserialized->cgIsReferenceValid());
        $this->assertTrue($unserialized->cgIsHardReference());

        $unserializedSoft = $unserialized->cgGetWeakReference();
        $this->assertTrue($unserializedSoft->cgIsReferenceValid());
        $this->assertFalse($unserializedSoft->cgIsHardReference());
        $this->assertEquals($soft, $unserializedSoft);

        unset($unserialized);

        $this->assertFalse($unserializedSoft->cgIsReferenceValid());
    }

    public function testAbilityToRestrictNamespacesWhereGeneratorWork()
    {
        self::$generator->setAcceptedNamespaces(array('ClassGenerator\tests\ResourceClasses'));

        $this->assertFalse(class_exists('ClassGenerator\tests\ResourceClasses2\NullX'));

        self::$generator->setAcceptedNamespaces(array('ClassGenerator\tests\ResourceClasses', 'ClassGenerator\tests\ResourceClasses2'));

        $this->assertTrue(class_exists('ClassGenerator\tests\ResourceClasses2\NullX2'));

        self::$generator->setAcceptedNamespaces(array(''));
    }

    public function testWeShouldBeAbleToDoSeveralDecoratesWithSingleDecoratorClass()
    {
        $x1 = new ResourceClasses\X(1, 2);
        $x2 = new ResourceClasses\X(3, 4);

        $numberDecorator = new ResourceClasses\ToNumberDecorator();

        $x1  = $numberDecorator->cgDecorate($x1);
        $x2 = $numberDecorator->cgDecorate($x2);

        $this->assertEquals(3, $x1->toNumber());
        $this->assertEquals(7, $x2->toNumber());
    }
}
