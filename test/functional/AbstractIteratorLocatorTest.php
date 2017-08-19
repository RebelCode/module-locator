<?php

namespace RebelCode\Modular\Locator\FuncTest;

use ArrayIterator;
use Xpmock\TestCase;
use RebelCode\Modular\Locator\AbstractIteratorLocator;

/**
 * Tests {@see RebelCode\Modular\Locator\AbstractIteratorLocator}.
 *
 * @since [*next-version*]
 */
class AbstractIteratorLocatorTest extends TestCase
{
    /**
     * The class name of the test subject.
     *
     * @since [*next-version*]
     */
    const TEST_SUBJECT_CLASSNAME = 'RebelCode\\Modular\\Locator\\AbstractIteratorLocator';

    /**
     * Creates a new instance of the test subject.
     *
     * @since [*next-version*]
     *
     * @return AbstractIteratorLocator
     */
    public function createInstance()
    {
        $mock = $this->mock(static::TEST_SUBJECT_CLASSNAME)
            ->_generateKeyFromSource()
            ->_read()
            ->new()
        ;

        return $mock;
    }

    /**
     * Tests whether a valid instance of the test subject can be created.
     *
     * @since [*next-version*]
     */
    public function testCanBeCreated()
    {
        $subject = $this->createInstance();

        $this->assertInstanceOf(
            static::TEST_SUBJECT_CLASSNAME, $subject,
            'Subject is not a valid instance'
        );
    }

    /**
     * Tests the iterator getter and setter methods to ensure correct assignment and retrieval.
     *
     * @since [*next-version*]
     */
    public function testGetSetIterator()
    {
        $subject  = $this->createInstance();
        $reflect  = $this->reflect($subject);
        $iterator = new ArrayIterator([1, '2', 'three', 'key' => 4]);

        $reflect->_setIterator($iterator);

        $this->assertSame(
            $iterator, $reflect->_getIterator(),
            'Retrieved iterator instance is not identical to previously given instance.'
        );
    }

    /**
     * Tests the sources getter to ensure that the iterator is retrieved.
     *
     * @since [*next-version*]
     */
    public function testGetSources()
    {
        $subject  = $this->createInstance();
        $reflect  = $this->reflect($subject);
        $iterator = new ArrayIterator([1, '2', 'three', 'key' => 4]);

        $reflect->_setIterator($iterator);

        $this->assertSame(
            $iterator, $reflect->_getSources(),
            'Retrieved sources iterator is not identical to previously given iterator.'
        );
    }
}
