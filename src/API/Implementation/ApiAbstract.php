<?php

namespace GinoPane\PHPolyglot\API\Implementation;

use Exception;
use GinoPane\NanoRest\NanoRest;
use GinoPane\PHPolyglot\API\Response\ApiResponseInterface;
use GinoPane\PHPolyglot\Exception\MethodDoesNotExistException;

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

    /**
     * ApiAbstract constructor
     */
    public function __construct()
    {
        $this->httpClient = new NanoRest();
    }

    protected function callApi(string $apiClassMethod): ApiResponseInterface
    {
        try {
            $getRequestContext = sprintf("create%sContext", ucfirst($apiClassMethod));

            if (!method_exists($this, $getRequestContext)) {
                throw new MethodDoesNotExistException();
            }

            $requestContext = $this->{$getRequestContext}();

            $responseContext = $this->httpClient->sendRequest($requestContext);

            $this->filterHttpResponseContext($responseContext);

            $prepareApiResponse = sprintf("prepare%sApiResponse", ucfirst($apiClassMethod));

            if (!method_exists($this, $prepareApiResponse)) {
                throw new MethodDoesNotExistException();
            }

            $apiResponse = $this->{$prepareApiResponse}($responseContext);
        } catch (Exception $exception) {
            $apiResponse = $this->setResponseErrorFromException($exception);
        }

        return $apiResponse;
    }

    abstract protected function setResponseErrorFromException(Exception $exception): ApiResponseInterface;
}
