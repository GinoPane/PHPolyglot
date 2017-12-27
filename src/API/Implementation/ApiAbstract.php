<?php

namespace GinoPane\PHPolyglot\API\Implementation;

use GinoPane\NanoRest\Exceptions\ResponseContextException;
use GinoPane\NanoRest\Exceptions\TransportException;
use GinoPane\NanoRest\NanoRest;
use GinoPane\NanoRest\Request\RequestContext;
use GinoPane\NanoRest\Response\ResponseContext;
use GinoPane\PHPolyglot\API\Response\ApiResponseInterface;
use GinoPane\PHPolyglot\Exception\InvalidPropertyException;
use GinoPane\PHPolyglot\Exception\BadResponseContextException;
use GinoPane\PHPolyglot\Exception\InvalidEnvironmentException;
use GinoPane\PHPolyglot\Exception\MethodDoesNotExistException;

/**
 * Class ApiAbstract
 *
 * @author Sergey <Gino Pane> Karavay
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
    protected $envProperties = [];

    /**
     * ApiAbstract constructor
     *
     * @throws InvalidPropertyException
     * @throws InvalidEnvironmentException
     */
    public function __construct()
    {
        $this->httpClient = new NanoRest();

        $this->initPropertiesFromEnvironment();
    }

    /**
     * Call API method by creating RequestContext, sending it, filtering the result and preparing the response
     *
     * @param string $apiClassMethod
     * @param array  $arguments Arguments that need to be passed to API-related methods
     *
     * @throws TransportException
     * @throws ResponseContextException
     * @throws BadResponseContextException
     * @throws MethodDoesNotExistException
     *
     * @return ApiResponseInterface
     */
    protected function callApi(string $apiClassMethod, array $arguments = []): ApiResponseInterface
    {
        $requestContext = $this->getApiRequestContext($apiClassMethod, $arguments);

        $responseContext = $this->getApiResponseContext($requestContext);

        $apiResponse = $this->prepareApiResponse($responseContext, $apiClassMethod);

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
            throw new BadResponseContextException(
                $responseContext->getHttpStatusMessage(),
                $responseContext->getHttpStatusCode()
            );
        }
    }

    /**
     * Fills specified properties using environment variables
     *
     * @throws InvalidPropertyException
     * @throws InvalidEnvironmentException
     */
    protected function initPropertiesFromEnvironment(): void
    {
        foreach ($this->envProperties as $property => $env) {
            if (!property_exists($this, $property)) {
                throw new InvalidPropertyException(
                    sprintf("Property \"%s\" does not exist within the class \"%s\"", $property, get_class($this))
                );
            }

            if (false === ($envSetting = getenv($env))) {
                throw new InvalidEnvironmentException(
                    sprintf("Required environment variable \"%s\" is not set", $env)
                );
            }

            $this->{$property} = $envSetting;
        }
    }

    /**
     * Accepts RequestContext and sends it to API to get ResponseContext
     *
     * @param RequestContext $requestContext
     *
     * @throws TransportException
     * @throws ResponseContextException
     * @throws BadResponseContextException
     *
     * @return ResponseContext
     */
    private function getApiResponseContext(RequestContext $requestContext): ResponseContext
    {
        $responseContext = $this->httpClient->sendRequest(
            $requestContext
        );

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
     *
     * @throws MethodDoesNotExistException
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
        $prepareApiResponse = sprintf("prepare%sResponse", ucfirst($apiClassMethod));

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
            throw new MethodDoesNotExistException(
                sprintf("Specified method \"%s\" does not exist", $method)
            );
        }
    }
}
