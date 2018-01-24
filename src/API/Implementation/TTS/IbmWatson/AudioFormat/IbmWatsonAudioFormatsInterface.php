<?php

namespace GinoPane\PHPolyglot\API\Implementation\TTS\IbmWatson\AudioFormat;

/**
 * Interface IbmWatsonAudioFormatsInterface
 *
 * @link https://console.bluemix.net/docs/services/text-to-speech/http.html#format
 *
 * @author Sergey <Gino Pane> Karavay
 */
interface IbmWatsonAudioFormatsInterface
{
    const CODEC_OPUS    = 'opus';
    const CODEC_VORBIS  = 'vorbis';

    const AUDIO_BASIC = 'audio/basic';
    const AUDIO_FLAC  = 'audio/flac';
    const AUDIO_L16   = 'audio/l16';
    const AUDIO_MP3   = 'audio/mp3';
    const AUDIO_MPEG  = 'audio/mpeg';
    const AUDIO_MULAW = 'audio/mulaw';
    const AUDIO_OGG   = 'audio/ogg';
    const AUDIO_WAV   = 'audio/wav';
    const AUDIO_WEBM  = 'audio/webm';
}
