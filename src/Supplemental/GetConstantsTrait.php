<?php

namespace GinoPane\PHPolyglot\Supplemental;

use ReflectionClass;

/**
 * Trait GetConstantsTrait
 *
 * Get all object's constants as associative array
 *
 * @author Sergey <Gino Pane> Karavay
 */
trait GetConstantsTrait
{
    /**
     * Used to store an array of constants
     *
     * @var array
     */
    private static $constants = [];

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