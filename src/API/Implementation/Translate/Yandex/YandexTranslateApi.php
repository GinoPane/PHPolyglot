<?php

namespace GinoPane\PHPolyglot\API\Implementation\Translate\Yandex;

use GinoPane\NanoRest\Request\RequestContext;
use GinoPane\NanoRest\Response\ResponseContext;
use GinoPane\PHPolyglot\API\Response\Translate\TranslateApiResponse;
use GinoPane\PHPolyglot\API\Implementation\Translate\TranslateApiAbstract;

/**
 * Class YandexTranslateApi
 *
 * Yandex Translate API implementation.
 *
 * @version
 */
class YandexTranslateApi extends TranslateApiAbstract
{
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
     * @param string $text
     * @param string $languageTo
     * @param string $languageFrom
     *
     * @return RequestContext
     */
    protected function createTranslateContext(
        string $text,
        string $languageTo,
        string $languageFrom
    ): RequestContext
    {
        // TODO: Implement createTranslateContext() method.
    }

    /**
     * @param ResponseContext $context
     *
     * @return TranslateApiResponse
     */
    protected function prepareTranslateResponse(ResponseContext $context): TranslateApiResponse
    {
        // TODO: Implement prepareTranslateResponse() method.
    }

    /**
     * @param string $text
     * @param string $languageTo
     * @param string $languageFrom
     *
     * @return RequestContext
     */
    protected function createTranslateBulkContext(
        string $text,
        string $languageTo,
        string $languageFrom
    ): RequestContext
    {
        // TODO: Implement createTranslateBulkContext() method.
    }

    /**
     * @param ResponseContext $context
     *
     * @return TranslateApiResponse
     */
    protected function prepareTranslateBulkResponse(ResponseContext $context): TranslateApiResponse
    {
        // TODO: Implement prepareTranslateBulkResponse() method.
    }
}
