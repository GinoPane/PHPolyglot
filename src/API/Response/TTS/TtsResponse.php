<?php

namespace GinoPane\PHPolyglot\API\Response\TTS;

use GinoPane\PHPolyglot\API\Response\ApiResponseAbstract;
use GinoPane\PHPolyglot\API\Supplemental\TTS\TtsAudioFormat;

/**
 * Class TtsResponse
 *
 * @author Sergey <Gino Pane> Karavay
 */
class TtsResponse extends ApiResponseAbstract
{
    public function setAudioFormat(TtsAudioFormat $format): void
    {
        $this->format = $format;
    }

    public function setAudioContent(string $content): void
    {

    }
}
