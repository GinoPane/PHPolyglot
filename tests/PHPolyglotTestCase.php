<?php

namespace GinoPane\PHPolyglot;

use ReflectionClass;
use ReflectionMethod;
use ReflectionProperty;
use PHPUnit\Framework\TestCase;

/**
 * Class PHPolyglotTestCase
 */
class PHPolyglotTestCase extends TestCase
{
    /**
     * @param $object
     * @param $property
     * @return ReflectionProperty
     */
    protected function getReflectionProperty($object, $property)
    {
        $reflection         = new ReflectionClass($object);
        $reflectionProperty = $reflection->getProperty($property);
        $reflectionProperty->setAccessible(true);
        return $reflectionProperty;
    }

    /**
     * This method modifies the protected properties of any object.
     * @param object $object   The object to modify.
     * @param string $property The name of the property to modify.
     * @param mixed  $value    The value to set.
     */
    public function setInternalProperty(&$object, string $property, $value)
    {
        $reflectionProperty = $this->getReflectionProperty($object, $property);
        $reflectionProperty->setValue($object, $value);
    }

    /**
     * @param $object
     * @param $property
     * @return mixed
     */
    public function getInternalProperty($object, string $property)
    {
        $reflectionProperty = $this->getReflectionProperty($object, $property);
        return $reflectionProperty->getValue();
    }

    /**
     * @param $object
     * @param string $name
     * @return ReflectionMethod
     */
    public function getInternalMethod($object, string $name)
    {
        $class = new ReflectionClass($object);
        $method = $class->getMethod($name);
        $method->setAccessible(true);
        return $method;
    }
}
