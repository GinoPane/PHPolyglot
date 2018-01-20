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
                new Language(Language::CODE_ENGLISH),
                TtsVoiceFormat::GENDER_MALE
            ),
            IbmWatsonVoicesInterface::VOICE_BIRGIT => new TtsVoiceFormat(
                new Language(Language::CODE_GERMAN),
                TtsVoiceFormat::GENDER_FEMALE
            ),
            IbmWatsonVoicesInterface::VOICE_DIETER => new TtsVoiceFormat(
                new Language(Language::CODE_GERMAN),
                TtsVoiceFormat::GENDER_MALE
            ),
            IbmWatsonVoicesInterface::VOICE_EMI => new TtsVoiceFormat(
                new Language(Language::CODE_JAPANESE),
                TtsVoiceFormat::GENDER_FEMALE
            ),
            IbmWatsonVoicesInterface::VOICE_ENRIQUE => new TtsVoiceFormat(
                new Language(Language::CODE_SPANISH),
                TtsVoiceFormat::GENDER_MALE
            ),
            IbmWatsonVoicesInterface::VOICE_FRANCESCA => new TtsVoiceFormat(
                new Language(Language::CODE_ITALIAN),
                TtsVoiceFormat::GENDER_FEMALE
            ),
            IbmWatsonVoicesInterface::VOICE_ISABELA => new TtsVoiceFormat(
                new Language(Language::CODE_PORTUGUESE),
                TtsVoiceFormat::GENDER_FEMALE
            ),
            IbmWatsonVoicesInterface::VOICE_KATE => new TtsVoiceFormat(
                new Language(Language::CODE_ENGLISH),
                TtsVoiceFormat::GENDER_FEMALE
            ),
            IbmWatsonVoicesInterface::VOICE_LAURA => new TtsVoiceFormat(
                new Language(Language::CODE_SPANISH),
                TtsVoiceFormat::GENDER_FEMALE
            ),
            IbmWatsonVoicesInterface::VOICE_LISA => new TtsVoiceFormat(
                new Language(Language::CODE_ENGLISH),
                TtsVoiceFormat::GENDER_FEMALE
            ),
            IbmWatsonVoicesInterface::VOICE_MICHAEL => new TtsVoiceFormat(
                new Language(Language::CODE_ENGLISH),
                TtsVoiceFormat::GENDER_MALE
            ),
            IbmWatsonVoicesInterface::VOICE_RENEE => new TtsVoiceFormat(
                new Language(Language::CODE_FRENCH),
                TtsVoiceFormat::GENDER_FEMALE
            ),
            IbmWatsonVoicesInterface::VOICE_SOFIA_LA => new TtsVoiceFormat(
                new Language(Language::CODE_SPANISH),
                TtsVoiceFormat::GENDER_FEMALE
            ),
            IbmWatsonVoicesInterface::VOICE_SOFIA_US => new TtsVoiceFormat(
                new Language(Language::CODE_SPANISH),
                TtsVoiceFormat::GENDER_FEMALE
            ),
        ];
    }
}
