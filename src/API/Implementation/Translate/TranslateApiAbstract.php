<?php

namespace GinoPane\PHPolyglot\API\Implementation\Translate;

use GinoPane\NanoRest\Request\RequestContext;
use GinoPane\NanoRest\Response\ResponseContext;
use GinoPane\PHPolyglot\API\Implementation\ApiAbstract;
use GinoPane\PHPolyglot\API\Response\Translate\TranslateApiResponse;

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
     * @return TranslateApiResponse
     */
    public function translate(string $text, string $languageTo, string $languageFrom = ''): TranslateApiResponse
    {
        /** @var TranslateApiResponse $response */
        $response = $this->callApi(__FUNCTION__, [$text, $languageTo, $languageFrom]);

        return $response;
    }

    /**
     * @param array $text
     * @param string $languageTo
     * @param string $languageFrom
     *
     * @return TranslateApiResponse
     */
    public function translateBulk(array $text, string $languageTo, string $languageFrom = ''): TranslateApiResponse
    {
        /** @var TranslateApiResponse $response */
        $response = $this->callApi(__FUNCTION__, [$text, $languageTo, $languageFrom]);

        return $response;
    }

    /**
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
     * @param ResponseContext $context
     *
     * @return TranslateApiResponse
     */
    abstract protected function prepareTranslateResponse(ResponseContext $context): TranslateApiResponse;

    /**
     * @param string $text
     * @param string $languageTo
     * @param string $languageFrom
     *
     * @return RequestContext
     */
    abstract protected function createTranslateBulkContext(
        string $text,
        string $languageTo,
        string $languageFrom
    ): RequestContext;

    /**
     * @param ResponseContext $context
     *
     * @return TranslateApiResponse
     */
    abstract protected function prepareTranslateBulkResponse(ResponseContext $context): TranslateApiResponse;
}
