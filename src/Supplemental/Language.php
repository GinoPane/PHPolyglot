<?php

namespace GinoPane\PHPolyglot\Supplemental;

use ReflectionClass;

/**
 * Class Language
 *
 * @author: Sergey <Gino Pane> Karavay
 */
class Language implements LanguageInterface
{
    /**
     * Used to store an array of constants
     *
     * @var array
     */
    private static $constants = [];

    /**
     * Checks if code is valid
     *
     * @param string $code
     *
     * @return bool
     */
    public function codeIsValid(string $code): bool
    {
        return array_search($code, $this->getConstants()) !== false;
    }

    /**
     * Fills constants array if it is empty and returns it
     *
     * @return array
     */
    private function getConstants(): array
    {
        if (empty(self::$constants)) {
            self::$constants = (new ReflectionClass($this))->getConstants();
        }

        return self::$constants;
    }

}
