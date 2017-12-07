<?php

namespace GinoPane\PHPolyglot\API\Implementation;

use GinoPane\NanoRest\NanoRest;

/**
 * Class ApiAbstract
 */
abstract class ApiAbstract
{
    /**
     * Instance of HTTP client to handle requests
     *
     * @var NanoRest
     */
    protected $httpClient;

    /**
     * Main API endpoint
     *
     * @var string
     */
    protected $apiEndpoint = '';

    public function __construct()
    {
        $this->httpClient = new NanoRest();
    }

    protected function callApiMethod()
    {

    }
}
