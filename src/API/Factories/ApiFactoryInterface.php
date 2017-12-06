<?php

namespace GinoPane\PHPolyglot\API\Factories;

/**
 * Interface ApiFactoryInterface
 *
 * Interface that provides a method to get the necessary API object
 */
interface ApiFactoryInterface
{
    /**
     * Gets necessary API object
     *
     * @throws
     *
     * @return mixed
     */
    public function getApi();
}
