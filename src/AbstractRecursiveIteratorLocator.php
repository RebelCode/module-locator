<?php

namespace RebelCode\Modular\Locator;

use Dhii\Modular\Locator\ModuleLocatorExceptionInterface;
use Exception;
use Iterator;

/**
 * Abstract common functionality for locators that locate module configuration from recursive iterators or
 * iterator iterators.
 *
 * @since [*next-version*]
 */
abstract class AbstractRecursiveIteratorLocator extends AbstractIteratorLocator
{
    /**
     * Reads a config source into a standard config.
     *
     * @since [*next-version*]
     *
     * @throws ModuleLocatorException      If an error occurred while reading the config.
     * @throws CouldNotReadSourceException If the locator failed to read the config source.
     *
     * @return array The standard config.
     */
    protected function _read($config)
    {
        if (!($config instanceof Iterator) && !is_array($config)) {
            throw $this->_createModuleLocatorException(
                $this->__('Argument must be an array or iterator.'), null
            );
        }

        $array = is_array($config)
            ? $config
            : iterator_to_array($config);

        return $this->_normalizeConfigArray($array);
    }

    /**
     * Determines a config key based on its source.
     *
     * @since [*next-version*]
     *
     * @param mixed $configSource The config source.
     *
     * @return string The key that identifies the source.
     */
    protected function _generateKeyFromSource($configSource)
    {
        return md5(json_encode($configSource));
    }

    /**
     * Normalizes the given module config array into a standard config.
     *
     * @since [*next-version*]
     *
     * @param array $config The config data array.
     *
     * @throws ModuleLocatorException      If an error occurred while reading the config.
     * @throws CouldNotReadSourceException If the locator failed to read the config source.
     *
     * @return array The standard config.
     */
    abstract protected function _normalizeConfigArray(array $config);

    /**
     * Translates a string, and replaces placeholders.
     *
     * @since [*next-version*]
     *
     * @param string $string  The format string to translate.
     * @param array  $args    Placeholder values to replace in the string.
     * @param mixed  $context The context for translation.
     *
     * @return string The translated string.
     */
    abstract protected function __($string, $args = array(), $context = null);

    /**
     * Creates a new instance of a module locator exception.
     *
     * @since [*next-version*]
     *
     * @param string    $message        The exception message, if any.
     * @param Exception $innerException The inner exception, if any.
     *
     * @return ModuleLocatorExceptionInterface The new exception.
     */
    abstract protected function _createModuleLocatorException($message = null, Exception $innerException = null);
}
