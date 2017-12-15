<?php

namespace GinoPane\PHPolyglot\API\Implementation;

use Exception;
use GinoPane\NanoRest\NanoRest;
use GinoPane\NanoRest\Request\RequestContext;
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
     * Mapping of properties to environment variables which must supply these properties, like this:
     *
     *      [
     *          'apiKey' => 'ENVIRONMENT_API_KEY'
     *      ]
     *
     * These properties and corresponding environment variables will be validated
     *
     * @var array
     */
    protected $environmentProperties = [];

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
            throw new BadResponseClassException(
                sprintf("Class %s must implement %s", $responseClassName, ApiResponseInterface::class)
            );
        }

        $this->responseClassName = $responseClassName;
    }

    /**
     * Call API method by creating RequestContext, sending it, filtering the result and preparing the response
     *
     * @param string $apiClassMethod
     * @param array $arguments Arguments that need to be passed to API-related methods
     *
     * @return ApiResponseInterface
     */
    protected function callApi(string $apiClassMethod, array $arguments = []): ApiResponseInterface
    {
        try {
            $requestContext = $this->getApiRequestContext($apiClassMethod, $arguments);

            $responseContext = $this->getApiResponseContext($requestContext);

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
    protected function processApiResponseContextErrors(ResponseContext $responseContext): void
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
     * Accepts RequestContext and sends it to API to get ResponseContext
     *
     * @param RequestContext $requestContext
     *
     * @return ResponseContext
     */
    private function getApiResponseContext(RequestContext $requestContext): ResponseContext
    {
        $responseContext = $this->httpClient->sendRequest($requestContext);

        $this->processApiResponseContextErrors($responseContext);

        return $responseContext;
    }

    /**
     * Gets RequestContext for sending
     *
     * @param string $apiClassMethod
     * @param array $arguments Arguments that need to be passed to API-related methods
     *
     * @return RequestContext
     */
    private function getApiRequestContext(string $apiClassMethod, array $arguments = []): RequestContext
    {
        $getRequestContext = sprintf("create%sContext", ucfirst($apiClassMethod));

        $this->assertMethodExists($getRequestContext);

        $requestContext = $this->{$getRequestContext}(...$arguments);

        return $requestContext;
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
