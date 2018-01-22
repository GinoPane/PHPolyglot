<?php

namespace GinoPane\PHPolyglot\API\Implementation\TTS\IbmWatson\AudioFormat;

use GinoPane\PHPolyglot\API\Supplemental\TTS\TtsAudioFormat;

/**
 * Trait IbmWatsonAudioFormatsTrait
 *
 * @link https://console.bluemix.net/docs/services/text-to-speech/http.html#format
 *
 * @author Sergey <Gino Pane> Karavay
 */
trait IbmWatsonAudioFormatsTrait
{
    private $formatToAcceptMapping = [
        TtsAudioFormat::AUDIO_BASIC => 'audio/basic',
        TtsAudioFormat::AUDIO_FLAC  => 'audio/flac',
        TtsAudioFormat::AUDIO_L16   => 'audio/l16',
        TtsAudioFormat::AUDIO_MP3   => 'audio/mp3',
        TtsAudioFormat::AUDIO_MPEG  => 'audio/mpeg',
        TtsAudioFormat::AUDIO_MULAW => 'audio/mulaw',
        TtsAudioFormat::AUDIO_OGG   => 'audio/ogg',
        TtsAudioFormat::AUDIO_WAV   => 'audio/wav',
        TtsAudioFormat::AUDIO_WEBM  => 'audio/webm'
    ];

    /**
     * @param TtsAudioFormat $format
     * @param array          $additionalData
     *
     * @return string
     */
    public function getAcceptParameter(TtsAudioFormat $format, array $additionalData = []): string
    {
        $accept = '';

        return $accept;
    }
}
