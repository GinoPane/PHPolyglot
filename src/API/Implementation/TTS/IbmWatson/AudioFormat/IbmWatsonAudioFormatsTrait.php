<?php

namespace GinoPane\PHPolyglot\API\Implementation\TTS\IbmWatson\Voice;

use GinoPane\PHPolyglot\API\Supplemental\TTS\TtsAudioFormat;

/**
 * Trait IbmWatsonAudioFormatsTrait
 *
 * @link https://console.bluemix.net/docs/services/text-to-speech/http.html#voices
 *
 * @author Sergey <Gino Pane> Karavay
 */
trait IbmWatsonAudioFormatsTrait
{
    private $formatToAcceptMapping = [
        TtsAudioFormat::AUDIO_BASIC => '',
        TtsAudioFormat::AUDIO_FLAC  => '',
        TtsAudioFormat::AUDIO_L16   => '',
        TtsAudioFormat::AUDIO_MP3   => '',
        TtsAudioFormat::AUDIO_MPEG  => '',
        TtsAudioFormat::AUDIO_MULAW => '',
        TtsAudioFormat::AUDIO_OGG   => '',
        TtsAudioFormat::AUDIO_WAV   => '',
        TtsAudioFormat::AUDIO_WEBM  => ''
    ];
}
