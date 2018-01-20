<?php

namespace GinoPane\PHPolyglot\API\Supplemental\TTS;

use GinoPane\PHPolyglot\Supplemental\GetConstantsTrait;

/**
 * Interface TtsAudioFormats
 *
 * The interface provides constants for popular basic audio formats. They are completely optional, you can use
 * every other identifier for audio format, just be sure that your chosen API supports that key and can correctly
 * process it
 *
 * @author Sergey <Gino Pane> Karavay
 */
class TtsAudioFormat
{
    const AUDIO_BASIC       = 'basic';
    const AUDIO_FLAC        = 'flac';
    const AUDIO_L16         = 'l16';
    const AUDIO_MP3         = 'mp3';
    const AUDIO_MPEG        = 'mpeg';
    const AUDIO_MULAW       = 'mulaw';
    const AUDIO_OGG         = 'ogg';
    const AUDIO_WAV         = 'wav';
    const AUDIO_WEBM        = 'webm';

    /**
     * @link https://console.bluemix.net/docs/services/speech-to-text/audio-formats.html
     *
     * @var string[]
     */
    private static $defaultFileExtensions = [
        self::AUDIO_BASIC       => 'au',
        self::AUDIO_FLAC        => 'flac',
        self::AUDIO_L16         => 'l16',
        self::AUDIO_MP3         => 'mp3',
        self::AUDIO_MPEG        => 'mp3',
        self::AUDIO_MULAW       => 'ulaw',
        self::AUDIO_OGG         => 'ogg',
        self::AUDIO_WAV         => 'wav',
        self::AUDIO_WEBM        => 'webm'
    ];

    /**
     * Stored audio format. Set to mp3 format by default
     *
     * @var string
     */
    private $format = self::AUDIO_MP3;

    use GetConstantsTrait;

    /**
     * TtsAudioFormat constructor
     *
     * @param string $format
     */
    public function __construct(string $format = self::AUDIO_MP3)
    {
        $format = strtolower($format);

        if ($this->constantValueExists($format)) {
            $this->format = $format;
        } else {
            $this->format = self::AUDIO_MP3;
        }
    }

    /**
     * @return string
     */
    public function getFormat(): string
    {
        return $this->format;
    }

    /**
     * @return string
     */
    public function getFileExtension(): string
    {
        return self::$defaultFileExtensions[$this->format];
    }
}
