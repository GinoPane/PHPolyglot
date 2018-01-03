<?php

namespace GinoPane\PHPolyglot\API\Implementation\Dictionary;

use GinoPane\NanoRest\Request\RequestContext;
use GinoPane\NanoRest\Response\ResponseContext;
use GinoPane\PHPolyglot\API\Implementation\ApiAbstract;
use GinoPane\NanoRest\Exceptions\TransportException;
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
     * @param string $text
     * @param string $language
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
        string $language
    ): DictionaryResponse {
        /** @var DictionaryResponse $response */
        $response = $this->callApi(__FUNCTION__, func_get_args());

        return $response;
    }

    /**
     * Gets text translate alternatives
     *
     * @param string $text
     * @param string $languageTo
     * @param string $languageFrom
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
        string $languageTo,
        string $languageFrom
    ): DictionaryResponse {
        /** @var DictionaryResponse $response */
        $response = $this->callApi(__FUNCTION__, func_get_args());

        return $response;
    }

    /**
     * Create request context for get-text-alternatives request
     *
     * @param string $text
     * @param string $languageTo
     * @param string $languageFrom
     *
     * @return RequestContext
     */
    abstract protected function createGetTextAlternativesContext(
        string $text,
        string $languageTo,
        string $languageFrom
    ): RequestContext;

    /**
     * Process response of get-text-alternatives request and prepare valid response
     *
     * @param ResponseContext $context
     *
     * @return DictionaryResponse
     */
    abstract protected function prepareGetTextAlternativesResponse(ResponseContext $context): DictionaryResponse;

    /**
     * Create request context for get-translate-alternatives request
     *
     * @param array $texts
     * @param string $languageTo
     * @param string $languageFrom
     *
     * @return RequestContext
     */
    abstract protected function createGetTranslateAlternativesContext(
        array $texts,
        string $languageTo,
        string $languageFrom
    ): RequestContext;

    /**
     * Process response of get-translate-alternatives request and prepare valid response
     *
     * @param ResponseContext $context
     *
     * @return DictionaryResponse
     */
    abstract protected function prepareGetTranslateAlternativesResponse(ResponseContext $context): DictionaryResponse;
}
