<?php

namespace RebelCode\Modular\FuncTest\Locator;

use RebelCode\Modular\Locator\ModuleConfigurationInterface;
use Xpmock\TestCase;
use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;
use Aza\Components\PhpGen\PhpGen;
use Dhii\Validation\ValidatorInterface;
use RebelCode\Modular\Config\ConfigInterface as Cfg;

/**
 * Tests {@see \RebelCode\Modular\Locator\FileLocator}.
 *
 * @since [*next-version*]
 */
class FileLocatorTest extends TestCase
{
    /**
     * The name of the test subject.
     *
     * @since [*next-version*]
     */
    const TEST_SUBJECT_CLASSNAME  = 'RebelCode\\Modular\\Locator\\FileLocator';

    /**
     * The name of the file containing module configuration.
     *
     * @since [*next-version*]
     */
    const MODULE_FILE_NAME = 'Modulefile';

    /**
     * The configuration is in a PHP file, which returns an array with config data.
     *
     * @since [*next-version*]
     */
    const CONFIG_FORMAT_PHP = 'php';

    /**
     * The configuration is in a JSON file, which contains a JSON object with config data.
     *
     * @since [*next-version*]
     */
    const CONFIG_FORMAT_JSON = 'json';

    /**
     * Modules that are required to be present in the filesystem.
     *
     * @since [*next-version*]
     *
     * @var array
     */
    protected $requiredModules = array(
        'my-awesome-lib',
        'my-awesome-feature',
        'atlassian-cool-lib',
        'atlassian-cool-feature'
    );

    /**
     * Creates a new instance of the test subject.
     *
     * @since [*next-version*]
     *
     * @return \RebelCode\Modular\Locator\FileLocator
     */
    public function createInstance($sources = array(), $validator = null)
    {
        $mock = $this->mock(static::TEST_SUBJECT_CLASSNAME)
            ->_getConfigValidator($validator)
            ->new();

        $reflection = $this->reflect($mock);
        $reflection->_setSources($sources);


        return $mock;
    }

    /**
     * Creates a new instance of a validator.
     *
     * @since [*next-version*]
     *
     * @return ValidatorInterface The new validator.
     */
    public function _createValidator()
    {
        $mock = $this->mock('Dhii\\Validation\\ValidatorInterface')
                ->validate()
                ->new();

        return $mock;
    }

    /**
     * Tests whether a correct instance of the test subject can be created.
     *
     * @since [*next-version*]
     */
    public function testCanBeCreated()
    {
        $subject = $this->createInstance();

        $this->assertInstanceOf(static::TEST_SUBJECT_CLASSNAME, $subject, 'Could not create an correct instance of the test subject');
    }

    /**
     * Tests whether the locator can locate modules from a given list of file sources.
     *
     * @since [*next-version*]
     */
    public function testCanLocateModules()
    {
        $fs = $this->_createFilesystem();
        $rootPath = $fs->url();
        $moduleFileName = static::MODULE_FILE_NAME;
        $fJson = self::CONFIG_FORMAT_JSON;
        $fPhp = self::CONFIG_FORMAT_PHP;
        $paths = array(
            sprintf('%3$s/rebelcode/awesome-library-module/%1$s.%2$s', $moduleFileName, $fJson, $rootPath),
            sprintf('%3$s/rebelcode/awesome-feature-module/%1$s.%2$s', $moduleFileName, $fPhp, $rootPath),
            sprintf('%3$s/atlassian/awesome-library-module/%1$s.%2$s', $moduleFileName, $fJson, $rootPath),
            sprintf('%3$s/atlassian/awesome-feature-module/%1$s.%2$s', $moduleFileName, $fPhp, $rootPath),
        );
        $validator = $this->_createValidator();
        $subject = $this->createInstance($paths, $validator);

        $reflection = $this->reflect($subject);
        $configs = $reflection->locate();
        $this->assertCount(4, $configs, 'Wrong number of files found');
    }
    
    /**
     * Creates a new virtual filesystem.
     *
     * @since [*next-version*]
     * 
     * @return vfsStreamDirectory
     */
    protected function _createFilesystem()
    {
        $fs = vfsStream::setup('vendor');

        vfsStream::create($this->_getFilesystemStructure(), $fs);

        // Uncomment below line to print directory structure.
//        vfsStream::inspect(new vfsStreamPrintVisitor(), $fs);

        return $fs;
    }

    /**
     * Retrieves the structure of the mock filesystem.
     *
     * @since [*next-version*]
     *
     * @return array The structure
     */
    protected function _getFilesystemStructure()
    {
        $phpGen = PhpGen::instance();
        $testContentPrefix = 'test-content-';
        $moduleFileName = self::MODULE_FILE_NAME;
        $fJson = self::CONFIG_FORMAT_JSON;
        $fPhp = self::CONFIG_FORMAT_PHP;
        $mods = $this->requiredModules;

        return array(
            'rebelcode'         => array(
                'awesome-library-module'        => array(
                    sprintf('%1$s.php', uniqid('some-file-'))                   => uniqid($testContentPrefix),
                    sprintf('%1$s.php', uniqid('some-file-'))                   => uniqid($testContentPrefix),
                    sprintf('%1$s.%2$s', $moduleFileName, $fJson)               => $this->_generateModuleFile($mods[0], $fJson),
                ),
                'awesome-feature-module'        => array(
                    sprintf('%1$s.php', uniqid('some-file-'))                   => uniqid($testContentPrefix),
                    sprintf('%1$s.php', uniqid('some-file-'))                   => uniqid($testContentPrefix),
                    sprintf('%1$s.%2$s', $moduleFileName, $fPhp)                => $this->_generateModuleFile($mods[0], $fPhp),
                ),
                'non-module-lib'                => array(
                    sprintf('%1$s.php', uniqid('some-file-'))                   => uniqid($testContentPrefix),
                    sprintf('%1$s.php', uniqid('some-file-'))                   => uniqid($testContentPrefix),
                )
            ),
            'atlassian'         => array(
                'awesome-library-module'        => array(
                    sprintf('%1$s.php', uniqid('some-file-'))                   => uniqid($testContentPrefix),
                    sprintf('%1$s.php', uniqid('some-file-'))                   => uniqid($testContentPrefix),
                    sprintf('%1$s.%2$s', $moduleFileName, $fJson)               => $this->_generateModuleFile($mods[0], $fJson),
                ),
                'non-module-lib'                => array(
                    sprintf('%1$s.php', uniqid('some-file-'))                   => uniqid($testContentPrefix),
                    sprintf('%1$s.php', uniqid('some-file-'))                   => uniqid($testContentPrefix),
                ),
                'awesome-feature-module'        => array(
                    sprintf('%1$s.php', uniqid('some-file-'))                   => uniqid($testContentPrefix),
                    sprintf('%1$s.php', uniqid('some-file-'))                   => uniqid($testContentPrefix),
                    sprintf('%1$s.%2$s', $moduleFileName, $fPhp)                => $this->_generateModuleFile($mods[0], $fPhp),
                ),
            ),
        );
    }

    /**
     * Generates contents of a configuration file.
     *
     * @since [*next-version*]
     *
     * @param string $modName Name of the module to generate the config for.
     * @param string $configFormat Type of the config file format.
     *  See the CONFIG_FORMAT_* constants.
     * @return string Contents of a module file.
     */
    protected function _generateModuleFile($modName, $configFormat = self::CONFIG_FORMAT_PHP)
    {
        $config = $this->_generateModuleConfig($modName);
        $configContent = '';
        $phpGen = PhpGen::instance();

        switch ($configFormat) {
            case self::CONFIG_FORMAT_PHP:
                $configContent = sprintf('<?php return %1$s', $phpGen->getCode($config));
                break;

            case self::CONFIG_FORMAT_JSON:
                $configContent = json_encode($config);
                break;
        }


        return $configContent;
    }

    /**
     * Generates a dummy module configuration.
     *
     * @since [*next-version*]
     *
     * @param string $modName Name of the module.
     * @return array The module configuration.
     */
    protected function _generateModuleConfig($modName)
    {
        $title = str_replace(array('\\', '/', '-', '_'), ' ', $modName);
        $title = ucwords($title);

        return array(
            Cfg::K_KEY                                      => $modName,
            Cfg::K_ON_LOAD                                  => 'RebelCode\\Modular\\FuncTest\\Locator::onModuleLoad',
        );
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
