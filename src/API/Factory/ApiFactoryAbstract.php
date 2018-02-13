<?php

namespace GinoPane\PHPolyglot\API\Factory;

use Dotenv\Dotenv;
use GinoPane\PHPolyglot\Exception\InvalidPathException;
use GinoPane\PHPolyglot\Exception\InvalidConfigException;
use GinoPane\PHPolyglot\Exception\InvalidApiClassException;

/**
 * Interface ApiFactoryAbstract
 * Abstract class that provides a method to get the necessary API object
 *
 * @author Sergey <Gino Pane> Karavay
 */
abstract class ApiFactoryAbstract implements ApiFactoryInterface
{
    /**
     * Environment file name
     */
    const ENV_FILE_NAME = ".env";

    /**
     * Config file name
     */
    const CONFIG_FILE_NAME = "config.php";

    /**
     * Config section name that is being checked for existence. API-specific properties must
     * be located under that section
     *
     * @var string
     */
    protected $configSectionName = "";

    /**
     * @var array|null
     */
    protected static $config = null;

    /**
     * Boolean configuration flag which indicates whether environment variables were set or not
     *
     * @var array|null
     */
    protected static $envIsSet = false;

    /**
     * Config properties that must exist for valid config
     *
     * @var array
     */
    protected $configProperties = [
        'default'
    ];

    /**
     * API interface that must be implemented by API class
     *
     * @var string
     */
    protected $apiInterface = "";

    /**
     * ApiFactoryAbstract constructor
     *
     * @throws InvalidPathException
     * @throws InvalidConfigException
     * @throws InvalidApiClassException
     */
    public function __construct()
    {
        if (is_null(self::$config)) {
            $this->initConfig();
        }

        if (!self::$envIsSet) {
            $this->initEnvironment();
        }

        $this->assertConfigIsValid();
    }

    /**
     * Gets necessary API object
     *
     * @param array $parameters
     *
     * @return mixed
     */
    public function getApi(array $parameters = [])
    {
        $apiClass = $this->getFactorySpecificConfig()['default'];
        
        return new $apiClass($parameters);
    }

    /**
     * @param array $config
     */
    public static function setConfig(array $config = [])
    {
        self::$config = $config;
    }

    /**
     * Sets environment variables using $env array. Existing variables will not be overwritten
     *
     * @param array $env
     */
    public static function setEnv(array $env = [])
    {
        self::$envIsSet = true;

        foreach ($env as $variable => $value) {
            self::setEnvironmentVariable($variable, $value);
        }
    }

    /**
     * @param   $name
     * @param   $value
     */
    private static function setEnvironmentVariable($name, $value = null)
    {
        if (self::environmentVariableExists($name)) {
            return;
        }

        if (function_exists('putenv')) {
            putenv("$name=$value");
        }

        $_ENV[$name] = $value;
        $_SERVER[$name] = $value;
    }

    /**
     * @param $name
     *
     * @return mixed
     */
    private static function environmentVariableExists($name)
    {
        switch (true) {
            case array_key_exists($name, $_ENV):
                return true;
            case array_key_exists($name, $_SERVER):
                return true;
            default:
                $value = getenv($name);
                return $value !== false;
        }
    }

    /**
     * Returns config section specific for current factory. Returns an empty array for invalid section name in case of
     * improper method call
     *
     * @return array
     */
    protected function getFactorySpecificConfig(): array
    {
        return self::$config[$this->configSectionName] ?: [];
    }

    /**
     * Performs basic validation of config structure. This method is to be overridden by custom implementations if
     * required
     *
     * @throws InvalidConfigException
     * @throws InvalidApiClassException
     */
    protected function assertConfigIsValid(): void
    {
        foreach ($this->configProperties as $property) {
            if (empty(self::$config[$this->configSectionName][$property])) {
                throw new InvalidConfigException(
                    sprintf(
                        "Config section does not exist or is not filled properly: %s (\"%s\" is missing)",
                        $this->configSectionName,
                        $property
                    )
                );
            }
        }

        $this->assertApiClassImplementsInterface($this->apiInterface);
    }

    /**
     * @param string $interface
     *
     * @throws InvalidApiClassException
     */
    protected function assertApiClassImplementsInterface(string $interface): void
    {
        $apiClass = $this->getFactorySpecificConfig()['default'];

        if (false === ($interfaces = @class_implements($apiClass, true))) {
            throw new InvalidApiClassException(
                sprintf("Class %s is invalid", $apiClass)
            );
        }

        if (!in_array($interface, $interfaces)) {
            throw new InvalidApiClassException(
                sprintf("Class %s must implement %s interface", $apiClass, $interface)
            );
        }
    }

    /**
     * Initialize environment variables
     *
     * @throws InvalidPathException
     */
    protected function initEnvironment(): void
    {
        $envFile = $this->getRootRelatedPath($this->getEnvFileName());

        $this->assertFileIsReadable($envFile);

        self::$envIsSet = (bool)(new Dotenv($this->getRootDirectory(), $this->getEnvFileName()))->load();
    }

    /**
     * Initialize config variables
     *
     * @throws InvalidPathException
     */
    protected function initConfig(): void
    {
        $configFile = $this->getRootRelatedPath($this->getConfigFileName());

        $this->assertFileIsReadable($configFile);

        self::$config = (array)(include $configFile);
    }

    /**
     * @param string $filePath
     *
     * @return string
     */
    protected function getRootRelatedPath(string $filePath): string
    {
        return $this->getRootDirectory() . DIRECTORY_SEPARATOR . trim($filePath, DIRECTORY_SEPARATOR);
    }

    /**
     * Returns package root directory
     *
     * @return string
     *
     * @codeCoverageIgnore
     */
    protected function getRootDirectory(): string
    {
        return dirname(\GinoPane\PHPolyglot\ROOT_DIRECTORY);
    }

    /**
     * Returns environment file name
     *
     * @return string
     *
     * @codeCoverageIgnore
     */
    protected function getEnvFileName(): string
    {
        return self::ENV_FILE_NAME;
    }

    /**
     * Returns config file name
     *
     * @return string
     *
     * @codeCoverageIgnore
     */
    protected function getConfigFileName(): string
    {
        return self::CONFIG_FILE_NAME;
    }

    /**
     * A simple check that file exists and is readable
     *
     * @param string $fileName
     *
     * @throws InvalidPathException
     */
    protected function assertFileIsReadable(string $fileName): void
    {
        if (!is_file($fileName) || !is_readable($fileName)) {
            throw new InvalidPathException(sprintf('Unable to read the file at %s', $fileName));
        }
    }

    /**
     * A simple check that file exists and is readable
     *
     * @param string $directoryName
     *
     * @throws InvalidPathException
     */
    protected function assertDirectoryIsWriteable(string $directoryName): void
    {
        if (!is_dir($directoryName) || !is_writable($directoryName)) {
            throw new InvalidPathException(sprintf('Unable to write to the directory at "%s"', $directoryName));
        }
    }
}
