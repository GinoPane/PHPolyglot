<?php

namespace GinoPane\PHPolyglot\API\Implementation\Dictionary;

use GinoPane\NanoRest\Exceptions\ResponseContextException;
use GinoPane\NanoRest\Exceptions\TransportException;
use GinoPane\PHPolyglot\API\Implementation\ApiAbstract;
use GinoPane\PHPolyglot\API\Response\Dictionary\DictionaryResponse;
use GinoPane\PHPolyglot\Exception\BadResponseContextException;
use GinoPane\PHPolyglot\Exception\MethodDoesNotExistException;

/**
 * Interface DictionaryApiInterface
 *
 * @author Sergey <Gino Pane> Karavay
 */
abstract class DictionaryApiAbstract  extends ApiAbstract implements DictionaryApiInterface
{
    /**
     * Gets text alternatives either in the same language (possible text forms)
     * or in different language (alternative translations)
     *
     * @param string $text
     * @param string $languageFrom
     * @param string $languageTo
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
        string $languageFrom,
        string $languageTo = ''
    ): DictionaryResponse {
        /** @var DictionaryResponse $response */
        $response = $this->callApi(__FUNCTION__, [$text, $languageTo, $languageFrom]);

        return $response;
    }
}
