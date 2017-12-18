<?php

namespace GinoPane\PHPolyglot\API\Implementation\Translate\Yandex;

use GinoPane\NanoRest\Request\RequestContext;
use GinoPane\NanoRest\Response\ResponseContext;
use GinoPane\NanoRest\Response\JsonResponseContext;
use GinoPane\NanoRest\Exceptions\RequestContextException;
use GinoPane\PHPolyglot\Exception\InvalidResponseContent;
use GinoPane\PHPolyglot\Exception\BadResponseContextException;
use GinoPane\PHPolyglot\API\Response\Translate\TranslateApiResponse;
use GinoPane\PHPolyglot\API\Implementation\Translate\TranslateApiAbstract;

/**
 * Class YandexTranslateApi
 *
 * Yandex Translate API implementation
 *
 * API version 1.5
 *
 * @link https://tech.yandex.com/translate/doc/dg/concepts/api-overview-docpage/
 */
class YandexTranslateApi extends TranslateApiAbstract
{
    const LANGUAGE_UNDETECTED = 'no';
    /**
     * URL path for translate action
     */
    const TRANSLATE_API_PATH = 'translate';

    const STATUS_SUCCESS                        = 200;
    const STATUS_INVALID_API_KEY                = 401;
    const STATUS_BLOCKED_API_KEY                = 402;
    const STATUS_AMOUNT_LIMIT_EXCEEDED          = 404;
    const STATUS_TEXT_SIZE_LIMIT_EXCEEDED       = 413;
    const STATUS_TEXT_CANNOT_BE_TRANSLATED      = 422;
    const STATUS_TRANSLATION_DIRECTION_INVALID  = 501;

    /**
     * Custom status messages for error statuses
     *
     * @var array
     */
    private static $customStatusMessages = [
        self::STATUS_INVALID_API_KEY                => "Invalid API key",
        self::STATUS_BLOCKED_API_KEY                => "Blocked API key",
        self::STATUS_AMOUNT_LIMIT_EXCEEDED          => "Exceeded the daily limit on the amount of translated text",
        self::STATUS_TEXT_SIZE_LIMIT_EXCEEDED       => "Exceeded the maximum text size",
        self::STATUS_TEXT_CANNOT_BE_TRANSLATED      => "The text cannot be translated",
        self::STATUS_TRANSLATION_DIRECTION_INVALID  => "The specified translation direction is not supported"
    ];

    /**
     * Main API endpoint
     *
     * @var string
     */
    protected $apiEndpoint = 'https://translate.yandex.net/api/v1.5/tr.json';

    /**
     * API key required for calls
     *
     * @var string
     */
    protected $apiKey = '';

    /**
     * Response class name defined the class which instance must be returned by API calls
     *
     * @var string
     */
    protected $responseClassName = TranslateApiResponse::class;

    /**
     * Mapping of properties to environment variables which must supply these properties
     *
     * @var array
     */
    protected $envProperties = [
        'apiKey' => 'YANDEX_TRANSLATE_API_KEY'
    ];

    /**
     * Create request context for translate request
     *
     * @param string $text
     * @param string $languageTo
     * @param string $languageFrom
     *
     * @throws RequestContextException
     *
     * @return RequestContext
     */
    protected function createTranslateContext(
        string $text,
        string $languageTo,
        string $languageFrom
    ): RequestContext {
        $requestContext = (new RequestContext(sprintf("%s/%s", $this->apiEndpoint, self::TRANSLATE_API_PATH)))
            ->setRequestParameters(
                [
                    'lang'  => sprintf("%s-%s", $languageFrom, $languageTo)
                ] + $this->getAuthData()
            )
            ->setData(['text'  => $text])
            ->setMethod(RequestContext::METHOD_POST);

        return $this->fillGeneralRequestSettings($requestContext);
    }

    /**
     * Process response of translate request and prepare valid response
     *
     * @param ResponseContext $context
     *
     * @return TranslateApiResponse
     */
    protected function prepareTranslateResponse(ResponseContext $context): TranslateApiResponse
    {
        return $this->processTranslateResponse($context);
    }

    /**
     * Create request context for bulk translate request
     *
     * @param array  $texts
     * @param string $languageTo
     * @param string $languageFrom
     *
     * @throws RequestContextException
     *
     * @return RequestContext
     */
    protected function createTranslateBulkContext(
        array $texts,
        string $languageTo,
        string $languageFrom
    ): RequestContext {
        $requestContext = (new RequestContext(sprintf("%s/%s", $this->apiEndpoint, self::TRANSLATE_API_PATH)))
            ->setRequestParameters(
                [
                    'lang'  => sprintf("%s-%s", $languageFrom, $languageTo)
                ] + $this->getAuthData()
            )
            ->setData(['text'  => $texts])
            ->setMethod(RequestContext::METHOD_POST)
            ->setEncodeArraysUsingDuplication(true);

        return $this->fillGeneralRequestSettings($requestContext);
    }

    /**
     * Process response of bulk translate request and prepare valid response
     *
     * @param ResponseContext $context
     *
     * @return TranslateApiResponse
     */
    protected function prepareTranslateBulkResponse(ResponseContext $context): TranslateApiResponse
    {
        return $this->processTranslateResponse($context);
    }

    /**
     * Filters ResponseContext from common HTTP errors
     *
     * @param ResponseContext $responseContext
     *
     * @throws BadResponseContextException
     * @throws InvalidResponseContent
     */
    protected function processApiResponseContextErrors(ResponseContext $responseContext): void
    {
        $responseArray = $responseContext->getArray();

        if (!isset($responseArray['code'])) {
            throw new BadResponseContextException('Response status undefined');
        }

        if (($responseArray['code'] !== self::STATUS_SUCCESS) &&
            isset(self::$customStatusMessages[$responseArray['code']])
        ) {
            $errorMessage = $responseArray['message']
                ?? self::$customStatusMessages[$responseArray['code']];

            throw new BadResponseContextException($errorMessage, $responseArray['code']);
        }

        parent::processApiResponseContextErrors($responseContext);

        if (empty($responseArray['text'])) {
            throw new InvalidResponseContent(sprintf('There is no required field "%s" in response', 'text'));
        }
    }

    /**
     * @param RequestContext $requestContext
     *
     * @throws RequestContextException
     *
     * @return RequestContext
     */
    private function fillGeneralRequestSettings(RequestContext $requestContext): RequestContext
    {
        $requestContext
            ->setContentType(RequestContext::CONTENT_TYPE_FORM_URLENCODED)
            ->setResponseContextClass(JsonResponseContext::class);

        return $requestContext;
    }

    /**
     * Get auth part of the request data
     *
     * @return array
     */
    private function getAuthData(): array
    {
        return ['key' => $this->apiKey];
    }

    /**
     * @param ResponseContext $context
     *
     * @return TranslateApiResponse
     */
    private function processTranslateResponse(ResponseContext $context): TranslateApiResponse
    {
        $responseArray = $context->getArray();

        $response = new TranslateApiResponse();

        $response->setSuccess(true);
        $response->setTranslations((array)$responseArray['text']);

        if (isset($responseArray['lang'])) {
            list($fromLanguage, $toLanguage) = explode("-", $responseArray['lang']);

            if ($fromLanguage !== self::LANGUAGE_UNDETECTED) {
                $response->setLanguageFrom($fromLanguage);
            }

            $response->setLanguageTo($toLanguage);
        }

        return $response;
    }
}
