<?php

namespace GinoPane\PHPolyglot\API\Implementation\TTS\IbmWatson\AudioFormat;

use GinoPane\PHPolyglot\API\Supplemental\TTS\TtsAudioFormat;
use GinoPane\PHPolyglot\Exception\InvalidAudioFormatCodeException;
use GinoPane\PHPolyglot\Exception\InvalidAudioFormatParameterException;

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
        TtsAudioFormat::AUDIO_BASIC => IbmWatsonAudioFormatsInterface::AUDIO_BASIC,
        TtsAudioFormat::AUDIO_FLAC  => IbmWatsonAudioFormatsInterface::AUDIO_FLAC,
        TtsAudioFormat::AUDIO_L16   => IbmWatsonAudioFormatsInterface::AUDIO_L16,
        TtsAudioFormat::AUDIO_MP3   => IbmWatsonAudioFormatsInterface::AUDIO_MP3,
        TtsAudioFormat::AUDIO_MPEG  => IbmWatsonAudioFormatsInterface::AUDIO_MPEG,
        TtsAudioFormat::AUDIO_MULAW => IbmWatsonAudioFormatsInterface::AUDIO_MULAW,
        TtsAudioFormat::AUDIO_OGG   => IbmWatsonAudioFormatsInterface::AUDIO_OGG,
        TtsAudioFormat::AUDIO_WAV   => IbmWatsonAudioFormatsInterface::AUDIO_WAV,
        TtsAudioFormat::AUDIO_WEBM  => IbmWatsonAudioFormatsInterface::AUDIO_WEBM
    ];

    /**
     * Returns the string containing the accept parameter required for TTS.
     * It specifies audio format, sample rate and additional params if any
     *
     * @param TtsAudioFormat $format
     * @param array          $additionalData
     *
     * @throws InvalidAudioFormatCodeException
     * @throws InvalidAudioFormatParameterException
     *
     * @return string
     */
    public function getAcceptParameter(TtsAudioFormat $format, array $additionalData = []): string
    {
        $audioFormat = self::$formatMapping[$format->getFormat()] ?? '';

        if (empty($audioFormat)) {
            throw new InvalidAudioFormatCodeException($format->getFormat());
        }

        $accept = array_merge([$audioFormat], $this->processAdditionalParameters($format, $additionalData));

        return implode(";", $accept);
    }

    /**
     * @param TtsAudioFormat $format
     * @param array          $additionalData
     *
     * @throws InvalidAudioFormatParameterException
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
            case TtsAudioFormat::AUDIO_WAV:
                $additional[] = $this->extractRate($additionalData);

                break;
            case TtsAudioFormat::AUDIO_OGG:
                $additional[] = $this->extractRate($additionalData);
                $additional[] = $this->extractCodec($additionalData);

                break;
            case TtsAudioFormat::AUDIO_L16:
            case TtsAudioFormat::AUDIO_MULAW:
                $additional[] = $this->extractRate($additionalData, true);

                break;
            case TtsAudioFormat::AUDIO_WEBM:
                $codec = $this->extractCodec($additionalData);

                if ($codec == ("codecs=" . IbmWatsonAudioFormatsInterface::CODEC_VORBIS)) {
                    $additional[] = $this->extractRate($additionalData);
                }

                $additional[] = $codec;

                break;
        }

        return array_filter($additional);
    }

    /**
     * @param string $codec
     *
     * @return bool
     */
    private function codecIsValid(string $codec): bool
    {
        return in_array(
            $codec,
            [
                IbmWatsonAudioFormatsInterface::CODEC_OPUS,
                IbmWatsonAudioFormatsInterface::CODEC_VORBIS
            ]
        );
    }

    /**
     * @param array $additionalData
     *
     * @return mixed
     * @throws InvalidAudioFormatParameterException
     */
    private function extractCodec(array $additionalData)
    {
        $codec = $this->extractOptionalParameter('codec', $additionalData);

        if ($codec && !$this->codecIsValid($codec)) {
            throw new InvalidAudioFormatParameterException(
                sprintf("Specified codec \"%s\" is invalid", $codec)
            );
        }

        return "codecs=$codec";
    }

    /**
     * @param array $additionalData
     * @param bool  $required
     *
     * @throws InvalidAudioFormatParameterException
     *
     * @return mixed
     */
    private function extractRate(array $additionalData, bool $required = false)
    {
        $rateString = '';

        if ($required) {
            $rate = (int)$this->extractRequiredParameter('rate', $additionalData);
        } else {
            $rate = (int)$this->extractOptionalParameter('rate', $additionalData);
        }

        if ($rate) {
            $rateString = "rate=$rate";
        }

        return $rateString;
    }

    /**
     * @param string $parameter
     * @param array  $data
     *
     * @throws InvalidAudioFormatParameterException
     *
     * @return mixed
     */
    private function extractRequiredParameter(string $parameter, array $data)
    {
        if (!isset($data[$parameter])) {
            throw new InvalidAudioFormatParameterException(
                sprintf("Parameter \"%s\" is required", $parameter)
            );
        }

        return $data[$parameter];
    }

    /**
     * @param string $parameter
     * @param array  $data
     *
     * @return mixed|null
     */
    private function extractOptionalParameter(string $parameter, array $data)
    {
        if (isset($data[$parameter])) {
            return $data[$parameter];
        }

        return null;
    }
}
