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
    /**
     * TTS audio format codes to IBM-Watson-specific codes mapping
     *
     * @var array
     */
    private static $formatMapping = [
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
     * Returns the string containing the accept parameter required for TTS.
     * It specifies audio format, sample rate and additional params if any
     *
     * @param TtsAudioFormat $format
     * @param array          $additionalData
     *
     * @throws InvalidAudioFormatCodeException
     *
     * @return string
     */
    public function getAcceptParameter(TtsAudioFormat $format, array $additionalData = []): string
    {
        $accept[] = self::$formatMapping[$format->getFormat()] ?? '';

        if (empty($accept)) {
            throw new InvalidAudioFormatCodeException($format->getFormat());
        }

        $accept = array_merge($accept, $this->processAdditionalParameters($format, $additionalData));

        return implode(";", $accept);
    }

    /**
     * @param TtsAudioFormat $format
     * @param array          $additionalData
     *
     * @return array
     */
    private function processAdditionalParameters(TtsAudioFormat $format, array $additionalData = []): array
    {
        $additional = [];

        switch ($format->getFormat()) {
            case TtsAudioFormat::AUDIO_FLAC:
            case TtsAudioFormat::AUDIO_MP3:
            case TtsAudioFormat::AUDIO_MPEG:
            case TtsAudioFormat::AUDIO_OGG:
            case TtsAudioFormat::AUDIO_WAV:
                $additional[] = $this->processOptionalParameter('rate', $additionalData);
                break;
            case TtsAudioFormat::AUDIO_L16:
                $additional[] = $this->processRequiredParameter('rate', $additionalData);
                break;
            case TtsAudioFormat::AUDIO_MULAW:
                break;
            case TtsAudioFormat::AUDIO_WEBM:
                break;
        }

        return $additional;
    }
}
