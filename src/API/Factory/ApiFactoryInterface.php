<?php

namespace GinoPane\PHPolyglot\API\Factory;

/**
 * Interface ApiFactoryInterface
 *
 * Interface that provides a method to get the necessary API object
 *
 * @author Sergey <Gino Pane> Karavay
 */
interface ApiFactoryInterface
{
    /**
     * Gets necessary API object
     *
     * @return mixed
     */
    public function getApi();
}
