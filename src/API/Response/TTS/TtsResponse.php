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
    /**
     * @param TtsAudioFormat $format
     */
    public function setAudioFormat(TtsAudioFormat $format): void
    {
        $this->format = $format;
    }

    /**
     * @param string $content
     */
    public function setAudioContent(string $content): void
    {
        $this->setData($content);
    }
}
