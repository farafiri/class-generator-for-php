<?php

/** @noinspection PhpUndefinedNamespaceInspection */
use ClassGenerator\tests\ResourceClasses;

class SubjectTest extends BaseTest
{
    /**
     * @dataProvider withProvider
     * @_testWith ('ClassGenerator\tests\ResourceClasses\SubjectX')
     *            ('ClassGenerator\tests\ResourceClasses\SubjectX7', 'minPhp' => '7.0')
     *            ('ClassGenerator\tests\ResourceClasses\SubjectXVoid', 'minPhp' => '7.1')
     */
    public function testSubjectIsInstanceofSplSubject($testedClass)
    {
        $x = new $testedClass();
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
}
