<?php

namespace RebelCode\Modular\Locator;

use Dhii\Modular\Locator\AbstractFileLocator as BaseLocator;
use Dhii\Modular\Locator\ModuleLocatorInterface as BaseLocatorInterface;
use Dhii\Modular\Locator\ModuleLocatorExceptionInterface;
use Exception;

/**
 * Common functionality for module locators that get configuration from files.
 *
 * @since [*next-version*]
 */
abstract class AbstractFileLocator extends BaseLocator
{
    /**
     * Creates a new instance of a module locator exception.
     *
     * @since [*next-version*]
     *
     * @param string               $message        The exception message, if any.
     * @param BaseLocatorInterface $locator        The locator, if any.
     * @param Exception            $innerException The inner exception, if any.
     *
     * @return ModuleLocatorExceptionInterface The new exception.
     */
    protected function _createModuleLocatorException($message = null, BaseLocatorInterface $locator = null, Exception $innerException = null)
    {
        return new ModuleLocatorException($message, 0, $innerException, $locator);
    }

    /**
     * Creates a new instance of a "could not read source" exception.
     *
     * @since [*next-version*]
     *
     * @param string               $message        The exception message, if any.
     * @param BaseLocatorInterface $locator        The locator, if any.
     * @param mixed                $source         The config source, if any.
     * @param Exception            $innerException The inner exception, if any.
     *
     * @return ModuleLocatorExceptionInterface The new exception.
     */
    protected function _createCouldNotReadSourceException($message = null, BaseLocatorInterface $locator = null, $source = null, Exception $innerException = null)
    {
        return new CouldNotReadSourceException($message, 0, $innerException, $locator, $source);
    }
}
