<?php

namespace GinoPane\PHPolyglot\API\Implementation\Dictionary\Yandex;

use GinoPane\NanoRest\Request\RequestContext;
use GinoPane\NanoRest\Response\ResponseContext;
use GinoPane\PHPolyglot\API\Response\Dictionary\DictionaryResponse;
use GinoPane\PHPolyglot\API\Supplemental\Yandex\YandexApiErrorsTrait;
use GinoPane\PHPolyglot\API\Implementation\Dictionary\DictionaryApiAbstract;

/**
 * Class YandexDictionaryApi
 *
 * Yandex Dictionary API implementation
 *
 * API version 1
 *
 * @link https://tech.yandex.com/dictionary/doc/dg/concepts/api-overview-docpage/
 *
 * @author Sergey <Gino Pane> Karavay
 */
class YandexDictionaryApi extends DictionaryApiAbstract
{
    /**
     * Main API endpoint
     *
     * @var string
     */
    protected $apiEndpoint = 'https://dictionary.yandex.net/api/v1/dicservice.json';

    /**
     * API key required for calls
     *
     * @var string
     */
    protected $apiKey = '';

    /**
     * Mapping of properties to environment variables which must supply these properties
     *
     * @var array
     */
    protected $envProperties = [
        'apiKey' => 'YANDEX_DICTIONARY_API_KEY'
    ];

    use YandexApiErrorsTrait;

    /**
     * Create request context for get-text-alternatives request
     *
     * @param string $text
     * @param string $languageTo
     * @param string $languageFrom
     *
     * @return RequestContext
     */
    protected function createGetTextAlternativesContext(
        string $text,
        string $languageTo,
        string $languageFrom
    ): RequestContext {
        // TODO: Implement createGetTextAlternativesContext() method.
    }

    /**
     * Process response of get-text-alternatives request and prepare valid response
     *
     * @param ResponseContext $context
     *
     * @return DictionaryResponse
     */
    protected function prepareGetTextAlternativesResponse(ResponseContext $context): DictionaryResponse
    {
        // TODO: Implement prepareGetTextAlternativesResponse() method.
    }

    /**
     * Create request context for get-translate-alternatives request
     *
     * @param array  $texts
     * @param string $languageTo
     * @param string $languageFrom
     *
     * @return RequestContext
     */
    protected function createGetTranslateAlternativesContext(
        array $texts,
        string $languageTo,
        string $languageFrom
    ): RequestContext {
        // TODO: Implement createGetTranslateAlternativesContext() method.
    }

    /**
     * Process response of get-translate-alternatives request and prepare valid response
     *
     * @param ResponseContext $context
     *
     * @return DictionaryResponse
     */
    protected function prepareGetTranslateAlternativesResponse(ResponseContext $context): DictionaryResponse
    {
        // TODO: Implement prepareGetTranslateAlternativesResponse() method.
    }
}
