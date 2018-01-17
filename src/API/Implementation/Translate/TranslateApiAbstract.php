<?php

namespace GinoPane\PHPolyglot\API\Implementation\Translate;

use GinoPane\NanoRest\Request\RequestContext;
use GinoPane\NanoRest\Response\ResponseContext;
use GinoPane\NanoRest\Exceptions\TransportException;
use GinoPane\PHPolyglot\API\Implementation\ApiAbstract;
use GinoPane\PHPolyglot\Supplemental\Language\Language;
use GinoPane\NanoRest\Exceptions\ResponseContextException;
use GinoPane\PHPolyglot\Exception\BadResponseContextException;
use GinoPane\PHPolyglot\Exception\MethodDoesNotExistException;
use GinoPane\PHPolyglot\API\Response\Translate\TranslateResponse;

/**
 * Class TranslateApiAbstract
 *
 * @author Sergey <Gino Pane> Karavay
 */
abstract class TranslateApiAbstract extends ApiAbstract implements TranslateApiInterface
{
    /**
     * @param string   $text
     * @param Language $languageTo
     * @param Language $languageFrom
     *
     * @throws TransportException
     * @throws ResponseContextException
     * @throws BadResponseContextException
     * @throws MethodDoesNotExistException
     *
     * @return TranslateResponse
     */
    public function translate(string $text, Language $languageTo, Language $languageFrom): TranslateResponse
    {
        /** @var TranslateResponse $response */
        $response = $this->callApi(__FUNCTION__, func_get_args());

        return $response;
    }

    /**
     * @param array    $text
     * @param Language $languageTo
     * @param Language $languageFrom
     *
     * @throws TransportException
     * @throws ResponseContextException
     * @throws BadResponseContextException
     * @throws MethodDoesNotExistException
     *
     * @return TranslateResponse
     */
    public function translateBulk(array $text, Language $languageTo, Language $languageFrom): TranslateResponse
    {
        /** @var TranslateResponse $response */
        $response = $this->callApi(__FUNCTION__, func_get_args());

        return $response;
    }

    /**
     * Create request context for translate request
     *
     * @param string   $text
     * @param Language $languageTo
     * @param Language $languageFrom
     *
     * @return RequestContext
     */
    abstract protected function createTranslateContext(
        string $text,
        Language $languageTo,
        Language $languageFrom
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
     * @param array    $texts
     * @param Language $languageTo
     * @param Language $languageFrom
     *
     * @return RequestContext
     */
    abstract protected function createTranslateBulkContext(
        array $texts,
        Language $languageTo,
        Language $languageFrom
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
