<?php

namespace RebelCode\Modular\Locator\FuncTest;

use ArrayIterator;
use Xpmock\TestCase;
use RebelCode\Modular\Locator\AbstractRecursiveIterator;

/**
 * Tests {@see RebelCode\Modular\Locator\AbstractRecursiveIterator}.
 *
 * @since [*next-version*]
 */
class AbstractRecursiveIteratorLocatorTest extends TestCase
{
    /**
     * The class name of the test subject.
     *
     * @since [*next-version*]
     */
    const TEST_SUBJECT_CLASSNAME = 'RebelCode\\Modular\\Locator\\AbstractRecursiveIteratorLocator';

    /**
     * Creates a new instance of the test subject.
     *
     * @since [*next-version*]
     *
     * @return AbstractRecursiveIterator
     */
    public function createInstance()
    {
        $mock = $this->mock(static::TEST_SUBJECT_CLASSNAME)
            ->_normalizeConfigArray(function($config) {
                return $config;
            })
            ->__()
            ->_createModuleLocatorException()
            ->_createCouldNotReadSourceException()
            ->new();

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
     * Tests the key generation method to ensure that it generates a valid string and that it gets invoked during
     * module location.
     *
     * @since [*next-version*]
     */
    public function testGenerateKeyFromSource()
    {
        $subject  = $this->createInstance();
        $reflect  = $this->reflect($subject);
        $key      = $reflect->_generateKeyFromSource([1, '2', 'k' => 3]);

        // Ensure has is a non-empty string
        $this->assertInternalType('string', $key, 'Generated hash is not a string');
        $this->assertGreaterThan(0, count($key), 'Generated hash is empty');

        // Ensure it gets called during locating
        $subject->mock()
                ->_generateKeyFromSource([$this->anything()], null, $this->exactly(3));

        $reflect->_setIterator(new ArrayIterator([
            '1' => ['a'],
            '2' => ['b', 'c'],
            ['3', 'four']
        ]));
        $reflect->_locate();
    }
}
