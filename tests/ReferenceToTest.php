<?php

/** @noinspection PhpUndefinedNamespaceInspection */
use ClassGenerator\tests\ResourceClasses;

class ReferenceToTest extends BaseTest
{
    public function testReferenceIsInstanceofOrigin()
    {
        $hard = static::getGenerator()->hardReference(new ResourceClasses\X(1, 2));
        $this->assertTrue($hard instanceof ResourceClasses\X);

        $weak = $hard->cgGetWeakReference();
        $this->assertTrue($weak instanceof ResourceClasses\X);
    }

    public function testAllReferencesPointsToSameObject()
    {
        $hard = static::getGenerator()->hardReference(new ResourceClasses\X(1, 2));
        $weak = $hard->cgGetWeakReference();

        $this->assertEquals(1, $weak->getA());

        $weak->setA(23);
        $this->assertEquals(23, $hard->getA());

        $weak->cgGetHardReference()->setA(34);
        $this->assertEquals(34, $weak->getA());
    }

    public function testIsHardReference()
    {
        $hard = static::getGenerator()->hardReference(new ResourceClasses\X(1, 2));
        $this->assertTrue($hard->cgIsHardReference());

        $weak = $hard->cgGetWeakReference();
        $this->assertFalse($weak->cgIsHardReference());
    }

    public function testIsReferenceValid()
    {
        $hard = static::getGenerator()->hardReference(new ResourceClasses\X(1, 2));
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
        $hard = static::getGenerator()->hardReference(new ResourceClasses\X(1, 2));
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
        $hard = static::getGenerator()->hardReference($origin);
        $this->assertTrue($hard->cgIsReferenceEqualTo($origin));
        $this->assertTrue($hard->cgGetWeakReference()->cgIsReferenceEqualTo($origin));

        $origin2 = new ResourceClasses\X(1, 2);
        $hard2 = static::getGenerator()->hardReference($origin2);
        $this->assertTrue($hard2->cgIsReferenceEqualTo($origin2));

        $this->assertFalse($hard2->cgIsReferenceEqualTo($origin));
        $this->assertFalse($hard2->cgGetWeakReference()->cgIsReferenceEqualTo($origin));
    }

    public function testIsReferenceEqualToAnotherReference()
    {
        $hard = static::getGenerator()->hardReference(new ResourceClasses\X(1, 2));
        $weak = $hard->cgGetWeakReference();

        $this->assertTrue($weak->cgIsReferenceEqualTo($hard));
        $this->assertTrue($hard->cgIsReferenceEqualTo($hard));
        $this->assertTrue($hard->cgIsReferenceEqualTo($weak));
        $this->assertTrue($weak->cgIsReferenceEqualTo($weak));
        $this->assertTrue($hard->cgIsReferenceEqualTo($weak->cgGetHardReference()));

        $hard2 = static::getGenerator()->hardReference(new ResourceClasses\X(1, 2));
        $weak2 = $hard2->cgGetWeakReference();
        $this->assertTrue($weak2->cgIsReferenceEqualTo($hard2));
        $this->assertTrue($hard2->cgIsReferenceEqualTo($weak2));

        $this->assertFalse($weak->cgIsReferenceEqualTo($hard2));
        $this->assertFalse($hard->cgIsReferenceEqualTo($hard2));
        $this->assertFalse($hard2->cgIsReferenceEqualTo($weak));
        $this->assertFalse($weak2->cgIsReferenceEqualTo($hard));
        $this->assertFalse($weak2->cgIsReferenceEqualTo($weak));
    }

    public function testCloneOnReferenceMakesFromSoftHardRef()
    {
        $origin = new ResourceClasses\X(1, 2);
        $hard = static::getGenerator()->hardReference($origin);
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

    public function testSleepWakeupOnReferenceMakesFromSoftHardRef()
    {
        $origin = new ResourceClasses\X(1, 2);
        $hard = static::getGenerator()->hardReference($origin);
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

    public function testInvalidReferenceThrowsErrorOnAttemptToUse()
    {
        $hard = static::getGenerator()->hardReference(new ResourceClasses\X(1, 2));
        $weak = $hard->cgGetWeakReference();
        unset($hard);
        gc_collect_cycles();

        $this->setExpectedException('ClassGenerator\Exceptions\Proxy');
        $weak->getA();
    }

    public function testInvalidReferenceMayBehaveLikeNullObject()
    {
        $hard = static::getGenerator()->hardReference(new ResourceClasses\X(1, 2));
        $weak = $hard->cgGetWeakReference();
        $weak->cgSetBehaveLikeNullObject(true);
        unset($hard);
        gc_collect_cycles();

        $this->assertNull($weak->getA());
        $this->assertEquals(0, $weak->getB());
    }

    public function testInvalidReferenceMayBehaveLikeNullObjectCheckWithClone()
    {
        $hard = static::getGenerator()->hardReference(new ResourceClasses\X(1, 2));
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
        $hard = static::getGenerator()->hardReference(new ResourceClasses\X(1, 2));
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
        $hard = static::getGenerator()->hardReference(new ResourceClasses\X(1, 2));
        $weak = $hard->cgGetWeakReference();
        $weak->cgSetBehaveLikeNullObject(true);
        unset($hard);
        gc_collect_cycles();

        $this->setExpectedException('ClassGenerator\Exceptions\Proxy');
        $weak->cgGetWeakReference();
    }

    public function testReferenceAddReleaseEvent()
    {
        $hard = static::getGenerator()->hardReference(new ResourceClasses\X(1, 2));
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
        $hard = static::getGenerator()->hardReference(new ResourceClasses\X(1, 2));
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

    public function testSerializeWithReference()
    {
        $origin = new ResourceClasses\Serialize(1, 2);
        $hard = static::getGenerator()->hardReference($origin);
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
}