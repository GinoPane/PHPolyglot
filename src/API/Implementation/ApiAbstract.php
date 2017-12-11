<?php

namespace GinoPane\PHPolyglot\API\Implementation;

use Exception;
use GinoPane\NanoRest\NanoRest;
use GinoPane\NanoRest\Response\ResponseContext;
use GinoPane\PHPolyglot\API\Response\ApiResponseInterface;
use GinoPane\PHPolyglot\Exception\BadResponseClassException;
use GinoPane\PHPolyglot\Exception\BadResponseContextException;
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
     * Response class name
     *
     * @var string
     */
    protected $responseClassName = '';

    /**
     * ApiAbstract constructor
     */
    public function __construct()
    {
        $this->httpClient = new NanoRest();
    }

    /**
     * Sets response class name
     *
     * @param string $responseClassName
     *
     * @throws BadResponseClassException
     *
     * @return void
     */
    protected function setResponseClassName(string $responseClassName): void
    {
        if (!in_array(ApiResponseInterface::class, class_implements($responseClassName, true))) {
            throw new BadResponseClassException($responseClassName);
        }

        $this->responseClassName = $responseClassName;
    }

    /**
     * Call API method by creating RequestContext, sending it, filtering the result and preparing the response
     *
     * @param string $apiClassMethod
     *
     * @return ApiResponseInterface
     */
    protected function callApi(string $apiClassMethod): ApiResponseInterface
    {
        try {
            $responseContext = $this->getApiResponseContext($apiClassMethod);

            $this->filterApiResponseContext($responseContext);

            $apiResponse = $this->prepareApiResponse($responseContext, $apiClassMethod);
        } catch (Exception $exception) {
            $apiResponse = $this->setResponseErrorFromException($exception);
        }

        return $apiResponse;
    }

    /**
     * Filters ResponseContext from common HTTP errors
     *
     * @param ResponseContext $responseContext
     *
     * @throws BadResponseContextException
     *
     * @return void
     */
    protected function filterApiResponseContext(ResponseContext $responseContext): void
    {
        if ($responseContext->hasHttpError()) {
            throw new BadResponseContextException("HTTP error happened", $responseContext->getHttpStatusCode());
        }
    }

    /**
     * Sets error response from exception
     *
     * @param Exception $exception
     *
     * @return ApiResponseInterface
     */
    protected function setResponseErrorFromException(Exception $exception): ApiResponseInterface
    {
        /** @var ApiResponseInterface $response */
        $response = new $this->responseClassName();

        $response->setSuccess(false);
        $response->setErrorCode((int)$exception->getCode());
        $response->setErrorMessage($exception->getMessage());

        return $response;
    }

    /**
     * Gets RequestContext and sends it to API to get ResponseContext
     *
     * @param string $apiClassMethod
     *
     * @throws MethodDoesNotExistException
     *
     * @return ResponseContext
     */
    private function getApiResponseContext(string $apiClassMethod): ResponseContext
    {
        $getRequestContext = sprintf("create%sContext", ucfirst($apiClassMethod));

        $this->assertMethodExists($getRequestContext);

        $requestContext = $this->{$getRequestContext}();

        $responseContext = $this->httpClient->sendRequest($requestContext);

        return $responseContext;
    }

    /**
     * Prepares API response by processing ResponseContext
     *
     * @param string $apiClassMethod
     * @param ResponseContext $responseContext
     *
     * @throws MethodDoesNotExistException
     *
     * @return ApiResponseInterface
     */
    private function prepareApiResponse(ResponseContext $responseContext, string $apiClassMethod): ApiResponseInterface
    {
        $prepareApiResponse = sprintf("prepare%sApiResponse", ucfirst($apiClassMethod));

        $this->assertMethodExists($prepareApiResponse);

        $apiResponse = $this->{$prepareApiResponse}($responseContext);

        return $apiResponse;
    }

    /**
     * Throws exception if method does not exist
     *
     * @param string $method
     *
     * @throws MethodDoesNotExistException
     *
     * @return void
     */
    private function assertMethodExists(string $method): void
    {
        if (!method_exists($this, $method)) {
            throw new MethodDoesNotExistException();
        }
    }
}
