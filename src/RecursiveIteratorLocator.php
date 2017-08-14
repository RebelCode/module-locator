<?php

namespace RebelCode\Modular\Locator;

use Dhii\I18n\StringTranslatorAwareTrait;
use Dhii\I18n\StringTranslatorConsumingTrait;
use Dhii\I18n\StringTranslatorInterface;
use Dhii\Modular\Locator\ModuleLocatorExceptionInterface;
use Dhii\Modular\Locator\ModuleLocatorInterface;
use Dhii\Validation\Exception\ValidationFailedExceptionInterface;
use Dhii\Validation\ValidatorInterface;
use Exception;
use RebelCode\Modular\Config\ConfigInterface as Cfg;
use RecursiveIterator;

/**
 * A module locator implementation that retrieves module configuration data from a recursive iterator or an iterator
 * of iterators.
 *
 * @since [*next-version*]
 */
class RecursiveIteratorLocator extends AbstractRecursiveIteratorLocator implements ModuleLocatorInterface
{
    /*
     * @since [*next-version*]
     */
    use StringTranslatorAwareTrait;

    /*
     * @since [*next-version*]
     */
    use StringTranslatorConsumingTrait;

    /**
     * The validator used to validate configuration.
     *
     * @since [*next-version*]
     *
     * @var ValidatorInterface
     */
    protected $configValidator;

    /**
     * Constructor.
     *
     * @since [*next-version*]
     *
     * @param RecursiveIterator         $iterator   The recursive iterable containing the module config iterators.
     * @param ValidatorInterface        $validator  The validator to use for config validation.
     * @param StringTranslatorInterface $translator The translator to use for validation messages and logging.
     */
    public function __construct(
        RecursiveIterator $iterator,
        ValidatorInterface $validator,
        StringTranslatorInterface $translator = null
    ) {
        $this->_setConfigValidator($validator)
             ->_setIterator($iterator);

        if ($translator !== null) {
            $this->_setTranslator($translator);
        }

        $this->_construct();
    }

    /**
     * {@inheritdoc}
     *
     * @since [*next-version*]
     */
    public function locate()
    {
        return $this->_locate(); // shoulder
    }

    /**
     * {@inheritdoc}
     *
     * @since [*next-version*]
     */
    protected function _normalizeConfigArray(array $config)
    {
        $defaults   = $this->_getConfigDefaults();
        $normalized = array_merge($defaults, $config);

        return $normalized;
    }

    /**
     * Retrieves the validator used to validate configuration.
     *
     * @since [*next-version*]
     *
     * @return ValidatorInterface The validator.
     */
    protected function _getConfigValidator()
    {
        return $this->configValidator;
    }

    /**
     * Assigns the validator used to validate configuration.
     *
     * @since [*next-version*]
     *
     * @param ValidatorInterface $validator The validator.
     *
     * @return $this
     */
    protected function _setConfigValidator($validator)
    {
        $this->configValidator = $validator;

        return $this;
    }

    /**
     * Validates a module configuration.
     *
     * @since [*next-version*]
     *
     * @param array $config The configuration to validate.
     *
     * @throws ValidationFailedExceptionInterface If config is invalid.
     *
     * @return $this
     */
    protected function _validateConfig(array $config)
    {
        $this->_getConfigValidator()->validate($config);

        return $this;
    }

    /**
     * Retrieves configuration defaults.
     *
     * @since [*next-version*]
     *
     * @return array The defaults.
     */
    protected function _getConfigDefaults()
    {
        return array(
            Cfg::K_DEPENDENCIES => array(),
            Cfg::K_ON_LOAD      => null,
            Cfg::K_ON_LOAD_ALL  => null,
        );
    }

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
    protected function _createModuleLocatorException($message = null, Exception $innerException = null)
    {
        return new ModuleLocatorException($message, 0, $innerException, $this);
    }

    /**
     * Creates a new instance of a "could not read source" exception.
     *
     * @since [*next-version*]
     *
     * @param string    $message        The exception message, if any.
     * @param mixed     $source         The config source, if any.
     * @param Exception $innerException The inner exception, if any.
     *
     * @return ModuleLocatorExceptionInterface The new exception.
     */
    protected function _createCouldNotReadSourceException(
        $message = null,
        $source = null,
        Exception $innerException = null
    ) {
        return new CouldNotReadSourceException($message, 0, $innerException, $this, $source);
    }
}
