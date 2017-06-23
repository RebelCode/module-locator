<?php

namespace RebelCode\Modular\Locator;

use Dhii\Modular\Locator\AbstractModuleLocatorException;
use Dhii\Modular\Locator\ModuleLocatorExceptionInterface;
use Dhii\Modular\Locator\ModuleLocatorInterface;
use Exception;

/**
 * An exception which occurs when something goes wrong related to module location.
 *
 * @since [*next-version*]
 */
class ModuleLocatorException extends AbstractModuleLocatorException implements ModuleLocatorExceptionInterface
{
    /**
     * @since [*next-version*]
     *
     * @param ModuleLocatorInterface The locator to associate with this instance.
     */
    public function __construct($message = '', $code = 0, Exception $previous = null, ModuleLocatorInterface $locator = null)
    {
        parent::__construct($message, $code, $previous);
        $this->_setModuleLocator($locator);
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
