<?php

namespace GinoPane\PHPolyglot\API\Factory;

use Dotenv\Dotenv;

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
        $this->initConfig();
        $this->initEnvironment();
    }

    protected function initConfig(): void
    {
        if (!is_null(self::$env)) {
            return;
        }

        self::$env = (new Dotenv(dirname(ROOT_DIRECTORY)))->load();
    }

    protected function initEnvironment(): void
    {
        if (!is_null(self::$config)) {
            return;
        }

        self::$config = include ROOT_DIRECTORY . DIRECTORY_SEPARATOR . "config.php";
    }
}
