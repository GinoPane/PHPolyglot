<?php

namespace GinoPane\PHPolyglot\API\Implementation\Dictionary;

use GinoPane\NanoRest\Request\RequestContext;
use GinoPane\NanoRest\Exceptions\TransportException;
use GinoPane\PHPolyglot\API\Implementation\ApiAbstract;
use GinoPane\NanoRest\Response\ResponseContextAbstract;
use GinoPane\PHPolyglot\Supplemental\Language\Language;
use GinoPane\NanoRest\Exceptions\ResponseContextException;
use GinoPane\PHPolyglot\Exception\BadResponseContextException;
use GinoPane\PHPolyglot\Exception\MethodDoesNotExistException;
use GinoPane\PHPolyglot\API\Response\Dictionary\DictionaryResponse;

/**
 * Interface DictionaryApiInterface
 *
 * @author Sergey <Gino Pane> Karavay
 */
abstract class DictionaryApiAbstract extends ApiAbstract implements DictionaryApiInterface
{
    /**
     * Gets text alternatives
     *
     * @param string   $text
     * @param Language $language
     *
     * @throws TransportException
     * @throws ResponseContextException
     * @throws BadResponseContextException
     * @throws MethodDoesNotExistException
     *
     * @return DictionaryResponse
     */
    public function getTextAlternatives(
        string $text,
        Language $language
    ): DictionaryResponse {
        /** @var DictionaryResponse $response */
        $response = $this->callApi(__FUNCTION__, func_get_args());

        return $response;
    }

    /**
     * Gets text translate alternatives
     *
     * @param string   $text
     * @param Language $languageTo
     * @param Language $languageFrom
     *
     * @throws TransportException
     * @throws ResponseContextException
     * @throws BadResponseContextException
     * @throws MethodDoesNotExistException
     *
     * @return DictionaryResponse
     */
    public function getTranslateAlternatives(
        string $text,
        Language $languageTo,
        Language $languageFrom
    ): DictionaryResponse {
        /** @var DictionaryResponse $response */
        $response = $this->callApi(__FUNCTION__, func_get_args());

        return $response;
    }

    /**
     * Create request context for get-text-alternatives request
     *
     * @param string   $text
     * @param Language $language
     *
     * @return RequestContext
     */
    abstract protected function createGetTextAlternativesContext(
        string $text,
        Language $language
    ): RequestContext;

    /**
     * Process response of get-text-alternatives request and prepare valid response
     *
     * @param ResponseContextAbstract $context
     *
     * @return DictionaryResponse
     */
    abstract protected function prepareGetTextAlternativesResponse(ResponseContextAbstract $context): DictionaryResponse;

    /**
     * Create request context for get-translate-alternatives request
     *
     * @param string   $text
     * @param Language $languageTo
     * @param Language $languageFrom
     *
     * @return RequestContext
     */
    abstract protected function createGetTranslateAlternativesContext(
        string $text,
        Language $languageTo,
        Language $languageFrom
    ): RequestContext;

    /**
     * Process response of get-translate-alternatives request and prepare valid response
     *
     * @param ResponseContextAbstract $context
     *
     * @return DictionaryResponse
     */
    abstract protected function prepareGetTranslateAlternativesResponse(ResponseContextAbstract $context): DictionaryResponse;
}
