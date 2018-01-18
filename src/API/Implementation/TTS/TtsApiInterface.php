<?php

namespace GinoPane\PHPolyglot\API\Implementation\TTS;

use GinoPane\PHPolyglot\API\Response\TTS\TtsResponse;
use GinoPane\PHPolyglot\API\Supplemental\TTS\TtsAudioFormat;
use GinoPane\PHPolyglot\Supplemental\Language\Language;

/**
 * Interface TtsApiInterface
 *
 * @author Sergey <Gino Pane> Karavay
 */
interface TtsApiInterface
{
    /**
     * Gets TTS raw data, that can be saved afterwards
     *
     * @param string         $text
     * @param Language       $language
     * @param TtsAudioFormat $format
     * @param array          $additionalData
     *
     * @return TtsResponse
     */
    public function textToSpeech(
        string $text,
        Language $language,
        TtsAudioFormat $format,
        array $additionalData = []
    ): TtsResponse;
}
