<?php

namespace GinoPane\PHPolyglot\API\Implementation\SpellCheck;

use GinoPane\NanoRest\Request\RequestContext;
use GinoPane\NanoRest\Exceptions\TransportException;
use GinoPane\NanoRest\Response\ResponseContextAbstract;
use GinoPane\PHPolyglot\API\Implementation\ApiAbstract;
use GinoPane\PHPolyglot\Supplemental\Language\Language;
use GinoPane\NanoRest\Exceptions\ResponseContextException;
use GinoPane\PHPolyglot\Exception\BadResponseContextException;
use GinoPane\PHPolyglot\Exception\MethodDoesNotExistException;
use GinoPane\PHPolyglot\API\Response\SpellCheck\SpellCheckResponse;

/**
 * Interface SpellCheckApiAbstract
 *
 * @author Sergey <Gino Pane> Karavay
 */
abstract class SpellCheckApiAbstract extends ApiAbstract implements SpellCheckApiInterface
{
    /**
     * Check spelling for multiple text strings
     *
     * @param array    $texts
     * @param Language $languageFrom
     *
     * @throws TransportException
     * @throws ResponseContextException
     * @throws BadResponseContextException
     * @throws MethodDoesNotExistException
     *
     * @return SpellCheckResponse
     */
    public function checkTexts(array $texts, Language $languageFrom): SpellCheckResponse
    {
        /** @var SpellCheckResponse $response */
        $response = $this->callApi(__FUNCTION__, func_get_args());

        return $response;
    }

    /**
     * Create request context for spell-check request
     *
     * @param array          $texts
     * @param Language       $languageFrom
     *
     * @return RequestContext
     */
    abstract protected function createCheckTextsContext(
        array $texts,
        Language $languageFrom
    ): RequestContext;

    /**
     * Process response of spell-check request and prepare valid response
     *
     * @param ResponseContextAbstract $context
     *
     * @return SpellCheckResponse
     */
    abstract protected function prepareCheckTextsResponse(ResponseContextAbstract $context): SpellCheckResponse;
}
