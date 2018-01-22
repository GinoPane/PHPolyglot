<?php

namespace GinoPane\PHPolyglot\API\Implementation\TTS\IbmWatson\AudioFormat;

use GinoPane\PHPolyglot\API\Supplemental\TTS\TtsAudioFormat;
use GinoPane\PHPolyglot\Exception\InvalidAudioFormatCodeException;

/**
 * Trait IbmWatsonAudioFormatsTrait
 *
 * @link https://console.bluemix.net/docs/services/text-to-speech/http.html#format
 *
 * @author Sergey <Gino Pane> Karavay
 */
trait IbmWatsonAudioFormatsTrait
{
    private $formatMapping = [
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
     * @throws InvalidAudioFormatCodeException
     *
     * @return string
     */
    public function getAcceptParameter(TtsAudioFormat $format, array $additionalData = []): string
    {
        $accept = $formatMapping[$format] ?? '';

        if (empty($accept)) {
            throw new InvalidAudioFormatCodeException($format->getFormat());
        }

        return $accept;
    }
}
