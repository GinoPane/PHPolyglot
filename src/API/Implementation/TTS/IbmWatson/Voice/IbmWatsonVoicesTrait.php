<?php

namespace GinoPane\PHPolyglot\API\Implementation\TTS\IbmWatson\Voice;

use GinoPane\PHPolyglot\Supplemental\Language\Language;
use GinoPane\PHPolyglot\API\Supplemental\TTS\TtsVoiceFormat;

/**
 * Trait IbmWatsonVoicesTrait
 *
 * @link https://console.bluemix.net/docs/services/text-to-speech/http.html#voices
 *
 * @author Sergey <Gino Pane> Karavay
 */
trait IbmWatsonVoicesTrait
{
    /**
     * @return array
     */
    private function getVoiceConstraints(): array
    {
        return [
            IbmWatsonVoicesInterface::VOICE_ALLISON => new TtsVoiceFormat(
                new Language(Language::CODE_EN),
                TtsVoiceFormat::GENDER_MALE
            )
        ];
    }
}
