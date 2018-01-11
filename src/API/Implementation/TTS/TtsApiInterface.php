<?php

namespace GinoPane\PHPolyglot\API\Implementation;

use GinoPane\PHPolyglot\API\Response\TTS\TtsResponse;

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
     * @param string $text
     * @param string $language
     *
     * @return TtsResponse
     */
    public function textToSpeech(
        string $text,
        string $language
    ): TtsResponse;
}
