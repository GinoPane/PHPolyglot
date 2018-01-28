<?php

namespace GinoPane\PHPolyglot\API\Factory;

use Dotenv\Dotenv;
use GinoPane\PHPolyglot\Exception\InvalidPathException;
use GinoPane\PHPolyglot\Exception\InvalidConfigException;

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
     * @var array|null
     */
    protected static $env = null;

    /**
     * Config properties that must exist for valid config
     *
     * @var array
     */
    protected $configProperties = [
        'default'
    ];

    /**
     * ApiFactoryAbstract constructor
     *
     * @throws InvalidPathException
     * @throws InvalidConfigException
     */
    public function __construct()
    {
        if (is_null(self::$config) || is_null(self::$env)) {
            $this->initConfig();
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
     */
    protected function assertConfigIsValid(): void
    {
        foreach ($this->configProperties as $property) {
            if (empty(self::$config[$this->configSectionName][$property])) {
                throw new InvalidConfigException(
                    sprintf(
                        "Config section does not exist or is not filled properly: %s (required parameter \"%s\" is missing)",
                        $this->configSectionName,
                        $property
                    )
                );
            }
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

        self::$env = (new Dotenv($this->getRootDirectory(), $this->getEnvFileName()))->load();
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
