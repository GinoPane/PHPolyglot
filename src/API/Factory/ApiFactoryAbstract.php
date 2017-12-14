<?php

namespace GinoPane\PHPolyglot\API\Factory;

use Dotenv\Dotenv;
use GinoPane\PHPolyglot\Exception\InvalidPathException;

/**
 * Interface ApiFactoryAbstract
 *
 * Abstract class that provides a method to get the necessary API object
 */
abstract class ApiFactoryAbstract implements ApiFactoryInterface
{
    /**
     * @var array|null
     */
    private static $config = null;

    /**
     * @var array|null
     */
    private static $env = null;

    public function __construct()
    {
        if (is_null(self::$config) || is_null(self::$env)) {
            $this->initConfig();
            $this->initEnvironment();
        }
    }

    protected function initEnvironment(): void
    {
        $envFile = $this->getRootDirectory() . DIRECTORY_SEPARATOR . $this->getEnvFileName();

        $this->assertFileIsReadable($envFile);

        self::$env = (new Dotenv($this->getRootDirectory(), $this->getEnvFileName()))->load();
    }

    protected function initConfig(): void
    {
        $configFile = $this->getRootDirectory() . DIRECTORY_SEPARATOR . $this->getConfigFileName();

        $this->assertFileIsReadable($configFile);

        self::$config = include $configFile;
    }

    protected function getRootDirectory(): string
    {
        return dirname(\GinoPane\PHPolyglot\ROOT_DIRECTORY);
    }

    protected function getEnvFileName(): string
    {
        return ".env";
    }

    protected function getConfigFileName(): string
    {
        return "config.php";
    }

    private function assertFileIsReadable(string $fileName)
    {
        if (!is_file($fileName) || !is_readable($fileName)) {
            throw new InvalidPathException(sprintf('Unable to read the file at %s', $fileName));
        }
    }
}
