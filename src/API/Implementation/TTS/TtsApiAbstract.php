<?php

namespace GinoPane\PHPolyglot\API\Implementation\TTS;

use GinoPane\NanoRest\Request\RequestContext;
use GinoPane\NanoRest\Response\ResponseContext;
use GinoPane\NanoRest\Exceptions\TransportException;
use GinoPane\PHPolyglot\API\Response\TTS\TtsResponse;
use GinoPane\PHPolyglot\API\Implementation\ApiAbstract;
use GinoPane\PHPolyglot\Supplemental\Language\Language;
use GinoPane\NanoRest\Exceptions\ResponseContextException;
use GinoPane\PHPolyglot\API\Supplemental\TTS\TtsAudioFormat;
use GinoPane\PHPolyglot\Exception\BadResponseContextException;
use GinoPane\PHPolyglot\Exception\MethodDoesNotExistException;

/**
 * Interface TtsApiInterface
 *
 * @author Sergey <Gino Pane> Karavay
 */
abstract class TtsApiAbstract extends ApiAbstract implements TtsApiInterface
{
    /**
     * Gets TTS raw data, that can be saved afterwards
     *
     * @param string         $text
     * @param Language       $language
     * @param TtsAudioFormat $format
     * @param array          $additionalData
     *
     * @throws TransportException
     * @throws ResponseContextException
     * @throws BadResponseContextException
     * @throws MethodDoesNotExistException
     *
     * @return TtsResponse
     */
    public function textToSpeech(
        string $text,
        Language $language,
        TtsAudioFormat $format,
        array $additionalData = []
    ): TtsResponse {
        /** @var TtsResponse $response */
        $response = $this->callApi(__FUNCTION__, func_get_args());

        return $response;
    }

    /**
     * Create request context for text-to-speech request
     *
     * @param string         $text
     * @param Language       $language
     * @param TtsAudioFormat $format
     * @param array          $additionalData
     *
     * @return RequestContext
     */
    abstract protected function createTextToSpeechContext(
        string $text,
        Language $language,
        TtsAudioFormat $format,
        array $additionalData = []
    ): RequestContext;

    /**
     * Process response of text-to-speech request and prepare valid response
     *
     * @param ResponseContext $context
     *
     * @return TtsResponse
     */
    abstract protected function prepareTextToSpeechResponse(ResponseContext $context): TtsResponse;
}
