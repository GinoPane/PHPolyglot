<?php

namespace GinoPane\PHPolyglot\API\Supplemental\TTS;

/**
 * Interface TtsAudioFormats
 *
 * The interface provides constants for popular basic audio formats. They are completely optional, you can use
 * every other identifier for audio format, just be sure that your chosen API supports that key and can correctly
 * process it
 *
 * @author Sergey <Gino Pane> Karavay
 */
interface TtsAudioFormats
{
    const AUDIO_BASIC = "basic";
    const AUDIO_FLAC = "flac";
    const AUDIO_L16 = "l16";
    const AUDIO_MP3 = "mp3";
    const AUDIO_MPEG = "mpeg";
    const AUDIO_MULAW = "mulaw";
    const AUDIO_OGG = "ogg";
    const AUDIO_OGG_OPUS = "ogg_opus";
    const AUDIO_OGG_VORBIS = "ogg_vorbis";
    const AUDIO_WAV = "wav";
    const AUDIO_WEBM = "webm";
    const AUDIO_WEBM_OPUS = "webm_opus";
    const AUDIO_WEBM_VORBIS = "webm_vorbis";
}
