<?php

namespace GinoPane\PHPolyglot\API\Implementation\Translate;

use GinoPane\NanoRest\Request\RequestContext;
use GinoPane\NanoRest\Response\ResponseContext;
use GinoPane\PHPolyglot\API\Implementation\ApiAbstract;
use GinoPane\PHPolyglot\API\Response\Translate\TranslateResponse;

/**
 * Class TranslateApiAbstract
 */
abstract class TranslateApiAbstract extends ApiAbstract implements TranslateApiInterface
{
    /**
     * @param string $text
     * @param string $languageTo
     * @param string $languageFrom
     *
     * @return TranslateResponse
     */
    public function translate(string $text, string $languageTo, string $languageFrom = ''): TranslateResponse
    {
        /** @var TranslateResponse $response */
        $response = $this->callApi(__FUNCTION__, [$text, $languageTo, $languageFrom]);

        return $response;
    }

    /**
     * @param array $text
     * @param string $languageTo
     * @param string $languageFrom
     *
     * @return TranslateResponse
     */
    public function translateBulk(array $text, string $languageTo, string $languageFrom = ''): TranslateResponse
    {
        /** @var TranslateResponse $response */
        $response = $this->callApi(__FUNCTION__, [$text, $languageTo, $languageFrom]);

        return $response;
    }

    /**
     * Create request context for translate request
     *
     * @param string $text
     * @param string $languageTo
     * @param string $languageFrom
     *
     * @return RequestContext
     */
    abstract protected function createTranslateContext(
        string $text,
        string $languageTo,
        string $languageFrom
    ): RequestContext;

    /**
     * Process response of translate request and prepare valid response
     *
     * @param ResponseContext $context
     *
     * @return TranslateResponse
     */
    abstract protected function prepareTranslateResponse(ResponseContext $context): TranslateResponse;

    /**
     * Create request context for bulk translate request
     *
     * @param array $texts
     * @param string $languageTo
     * @param string $languageFrom
     *
     * @return RequestContext
     */
    abstract protected function createTranslateBulkContext(
        array $texts,
        string $languageTo,
        string $languageFrom
    ): RequestContext;

    /**
     * Process response of bulk translate request and prepare valid response
     *
     * @param ResponseContext $context
     *
     * @return TranslateResponse
     */
    abstract protected function prepareTranslateBulkResponse(ResponseContext $context): TranslateResponse;
}
