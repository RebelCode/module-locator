<?php

namespace RebelCode\Modular\Locator\FuncTest;

use Dhii\I18n\StringTranslatorInterface;
use Dhii\Validation\ValidatorInterface;
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
        $subject = new RecursiveIteratorLocator($this->createIterator([]), $this->createValidator());

        $this->assertInstanceOf(
            'Dhii\\Modular\\Locator\\ModuleLocatorInterface', $subject,
            'Subject is not a valid instance'
        );
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
        $locator = new RecursiveIteratorLocator($iterator, $this->createValidator());

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
