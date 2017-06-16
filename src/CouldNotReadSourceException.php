<?php

namespace RebelCode\Modular\Locator;

use Dhii\Modular\Locator\AbstractCouldNotReadSourceException;
use Dhii\Modular\Locator\CouldNotReadSourceExceptionInterface;
use Dhii\Modular\Locator\ModuleLocatorInterface;
use Exception;

/**
 * An exception that occurs when a config source could not be read.
 *
 * @since [*next-version*]
 */
class CouldNotReadSourceException extends AbstractCouldNotReadSourceException implements CouldNotReadSourceExceptionInterface
{
    /**
     * @since [*next-version*]
     * @see Exception::__construct()
     *
     * @param ModuleLocatorInterface $locator      The locator to associate with this instance, if any.
     * @param mixed                  $configSource The configuration source to associate with this instance, if any.
     */
    public function __construct($message = '', $code = 0, Exception $previous = null, ModuleLocatorInterface $locator = null, $configSource = null)
    {
        parent::__construct($message, $code, $previous);
        $this->_setModuleLocator($locator);
        $this->_setConfigSource($configSource);
    }

    /**
     * {@inheritdoc}
     *
     * @since [*next-version*]
     */
    public function getConfigSource()
    {
        return $this->_getConfigSource();
    }

    /**
     * {@inheritdoc}
     *
     * @since [*next-version*]
     */
    public function getModuleLocator()
    {
        return $this->_getModuleLocator();
    }
}
