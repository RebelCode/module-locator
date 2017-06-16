<?php

namespace RebelCode\Modular\Locator;

use Dhii\Modular\Locator\ModuleLocatorInterface;
use Dhii\Modular\Locator\CouldNotReadSourceExceptionInterface;
use Dhii\Validation\ValidatorInterface;
use Dhii\Validation\Exception\ValidationFailedExceptionInterface;
use Dhii\I18n\StringTranslatorAwareTrait;
use Dhii\I18n\StringTranslatingTrait;
use RebelCode\Modular\Config\ConfigInterface as Cfg;
use Traversable;

/**
 * A module config locator which reads configuration from files.
 *
 * @since [*next-version*]
 */
class FileLocator extends AbstractFileLocator implements ModuleLocatorInterface
{
    /**
     * The default maximal depth of JSON files to read.
     *
     * @since [*next-version*]
     */
    const JSON_DEFAULT_MAX_DEPTH = 512;

    /*
     * @since [*next-version*]
     */
    use StringTranslatorAwareTrait;

    /*
     * @since [*next-version*]
     */
    use StringTranslatingTrait;

    /**
     * The validator used to validate configuration.
     *
     * @since [*next-version*]
     *
     * @var ValidatorInterface
     */
    protected $configValidator;

    /**
     * @since [*next-version*]
     *
     * @param array|Traversable  $sources   The list of config sources, each of which is a file path.
     * @param ValidatorInterface $validator The validator to use for config validation.
     */
    public function __construct($sources, ValidatorInterface $validator)
    {
        $this->_setSources($sources);
        $this->_setConfigValidator($validator);
        $this->_construct();
    }

    /**
     * {@inheritdoc}
     *
     * @since [*next-version*]
     */
    protected function _read($source)
    {
        $ext = $this->_getFileExtension($source);
        if ($ext === 'php') {
            $config = $this->_readFromPhpFile($source);
        } elseif ($ext === 'json') {
            $config = $this->_readFromJsonFile($source);
        } else {
            throw $this->_createCouldNotReadSourceException($this->__('Source format not recognized'), $this, $source);
        }

        $config = $this->_normalize($config);
        try {
            $this->_validateConfig($config);
        } catch (ValidationFailedExceptionInterface $e) {
            $this->_createCouldNotReadSourceException($this->__('Configuration is invalid'), $this, $source, $e);
        }

        return $config;
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
    protected function _validateConfig($config)
    {
        $this->_getConfigValidator()->validate($config);

        return $this;
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
     * Retrieves configuration from a PHP file.
     *
     * @since [*next-version*]
     *
     * @param string $source File path.
     *
     * @throws CouldNotReadSourceExceptionInterface If the configuration could not be read.
     *
     * @return array The configuration.
     */
    protected function _readFromPhpFile($source)
    {
        if (!file_exists($source)) {
            throw $this->_createCouldNotReadSourceException('The PHP configuration file does not exist', $this, $source);
        }
        if (!is_readable($source)) {
            throw $this->_createCouldNotReadSourceException('The PHP configuration file is not readable', $this, $source);
        }

        $config = include $source;

        return $config;
    }

    /**
     * Retrieves configuration from a JSON file.
     *
     * @since [*next-version*]
     *
     * @param string $source File path.
     *
     * @throws CouldNotReadSourceExceptionInterface If the configuration could not be read.
     *
     * @return array The configuration.
     */
    protected function _readFromJsonFile($source)
    {
        $maxDepth = static::JSON_DEFAULT_MAX_DEPTH;
        if (!file_exists($source)) {
            throw $this->_createCouldNotReadSourceException($this->__('The JSON configuration file does not exist'), $this, $source);
        }
        if (!is_readable($source)) {
            throw $this->_createCouldNotReadSourceException($this->__('The JSON configuration file is not readable'), $this, $source);
        }

        $content = file_get_contents($source);
        $config  = json_decode($content, true, $maxDepth);

        if (is_null($config)) {
            throw $this->_createCouldNotReadSourceException($this->__('The JSON configuration could not be read with maximal depth of "%1$s"', array($maxDepth)), $this, $source);
        }

        return $config;
    }

    /**
     * Normalizes module configuration.
     *
     * @since [*next-version*]
     *
     * @param array $config The normalized configuration.
     */
    protected function _normalize($config)
    {
        $defaults = $this->_getConfigDefaults();
        $config   = array_merge($defaults, $config);

        return $config;
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
     * Determines the extension of a file by its filename.
     *
     * Does not interface with the filesystem.
     *
     * @since [*next-version*]
     *
     * @param type $filename
     *
     * @return string The extension derived from the filename.
     *                Only the last part of the file is included. Trailing dot not included.
     */
    protected function _getFileExtension($filename)
    {
        $info = pathinfo($filename, PATHINFO_EXTENSION);

        return $info;
    }

    /**
     * {@inheritdoc}
     * 
     * @since [*next-version*]
     */
    public function locate()
    {
        return $this->_locate();
    }
}
