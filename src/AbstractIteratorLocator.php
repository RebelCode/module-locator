<?php

namespace RebelCode\Modular\Locator;

use Dhii\Modular\Locator\AbstractModuleLocator;
use Traversable;

/**
 * Abstract common functionality for locators that locate module configuration from an iterator.
 *
 * @since [*next-version*]
 */
abstract class AbstractIteratorLocator extends AbstractModuleLocator
{
    /**
     * The iterator.
     *
     * @since [*next-version*]
     *
     * @var Traversable
     */
    protected $iterator;

    /**
     * Retrieves the iterator.
     *
     * @since [*next-version*]
     *
     * @return Traversable
     */
    protected function _getIterator()
    {
        return $this->iterator;
    }

    /**
     * Sets the iterator.
     *
     * @since [*next-version*]
     *
     * @param Traversable $iterator The iterator instance.
     *
     * @return $this
     */
    protected function _setIterator(Traversable $iterator)
    {
        $this->iterator = $iterator;

        return $this;
    }

    /**
     * Retrieves configuration sources.
     *
     * @since [*next-version*]
     *
     * @return Traversable Configuration sources.
     */
    protected function _getSources()
    {
        return $this->_getIterator();
    }
}
