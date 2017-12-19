<?php

namespace GinoPane\PHPolyglot\API\Response;

/**
 * Class ApiResponseInterface
 *
 * @package GinoPane\PHPolyglot\API\Response
 */
interface ApiResponseInterface
{
    /**
     * Returns response value
     *
     * @return mixed
     */
    public function getData();
}
