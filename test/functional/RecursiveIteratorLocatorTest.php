<?php

namespace RebelCode\Modular\Locator\FuncTest;

use Dhii\I18n\StringTranslatorInterface;
use Dhii\Validation\ValidatorInterface;
use Iterator;
use RebelCode\Modular\Locator\RecursiveIteratorLocator;
use RecursiveArrayIterator;
use Xpmock\TestCase;

/**
 * Tests {@see RebelCode\Modular\Locator\IteratorLocator}.
 *
 * @since [*next-version*]
 */
class RecursiveIteratorLocatorTest extends TestCase
{
    /**
     * The class name of the test subject.
     *
     * @since [*next-version*]
     */
    const TEST_SUBJECT_CLASSNAME = 'RebelCode\Modular\Locator\RecursiveIteratorLocator';

    /**
     * Creates a new instance of the test subject.
     *
     * @since [*next-version*]
     *
     * @param bool                      $constructor True to use the original constructor, false to not.
     * @param Iterator                  $iterator    The iterator of modules.
     * @param ValidatorInterface        $validator   The config validator.
     * @param StringTranslatorInterface $translator  The string translator.
     *
     * @return RecursiveIteratorLocator The created instance.
     */
    public function createInstance($constructor = false, $iterator = null, $validator = null, $translator = null)
    {
        $mock = $this->mock(static::TEST_SUBJECT_CLASSNAME);

        if (!$constructor) {
            return $mock->_getIterator($iterator)
                        ->_getConfigValidator($validator)
                        ->_getTranslator($translator)
                        ->new();
        }

        return $mock->new($iterator, $validator, $translator);
    }

    /**
     * Creates a new iterator instance for a given set of config data.
     *
     * @since [*next-version*]
     *
     * @param array $data
     *
     * @return RecursiveArrayIterator
     */
    public function createIterator($data = [])
    {
        return new RecursiveArrayIterator($data);
    }

    /**
     * Creates a new instance of a validator.
     *
     * @since [*next-version*]
     *
     * @return ValidatorInterface The new validator.
     */
    public function createValidator()
    {
        $mock = $this->mock('Dhii\\Validation\\ValidatorInterface')
            ->validate()
            ->new();

        return $mock;
    }

    /**
     * Creates a new instance of a translator.
     *
     * @since [*next-version*]
     *
     * @return StringTranslatorInterface The new translator.
     */
    public function createTranslator()
    {
        $mock = $this->mock('Dhii\\I18n\\StringTranslatorInterface')
            ->translate()
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
            'Dhii\\Modular\\Locator\\ModuleLocatorInterface', $subject,
            'Subject is not a valid instance'
        );
    }

    /**
     * Tests the constructor to determine whether the properties are being set correctly.
     *
     * @since [*next-version*]
     */
    public function testConstructor()
    {
        $iterator   = $this->createIterator([]);
        $validator  = $this->createValidator();
        $translator = $this->createTranslator();

        $subject    = $this->createInstance(true, $iterator, $validator, $translator);
        $reflect    = $this->reflect($subject);

        $this->assertSame($iterator,   $reflect->iterator, 'The iterator was not properly set.');
        $this->assertSame($validator,  $reflect->configValidator, 'The validator was not properly set.');
        $this->assertSame($translator, $reflect->translator, 'The translator was not properly set.');
    }

    /**
     * Tests whether the subject can correctly locate modules.
     *
     * @since [*next-version*]
     */
    public function testCanLocateModules()
    {
        $iterator = $this->createIterator([
            [
                'name' => 'my-module',
            ],
            [
                'name'         => 'other-module',
                'dependencies' => [
                    'my-module',
                ],
            ],
            [
                'name'        => 'third-module',
                'on-load'     => __CLASS__ . '::onModuleLoad',
                'on-load-all' => function() {
                    phpinfo();
                },
            ],
        ]);
        $locator = $this->createInstance(false, $iterator, $this->createValidator());

        $config = $locator->locate();

        $this->assertCount(3, $config);
    }

    /**
     * Dummy load callback for a module.
     *
     * @since [*next-version*]
     *
     * @param string $modName Name of the module.
     */
    public static function onModuleLoad($modName)
    {
        echo sprintf('Hello! My name is "%1$s"', $modName);
    }
}
