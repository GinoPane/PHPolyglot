<?php

namespace GinoPane\PHPolyglot\API\Implementation\TTS\IbmWatson\Voice;

use GinoPane\PHPolyglot\Exception\InvalidVoiceParametersException;
use GinoPane\PHPolyglot\Exception\InvalidVoiceCodeException;
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
     * @return TtsVoiceFormat[]
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

    /**
     * @param Language $language
     * @param array    $additionalData
     *
     * @throws InvalidVoiceCodeException
     * @throws InvalidVoiceParametersException
     *
     * @return string
     */
    public function getVoiceParameter(Language $language, array $additionalData = []): string
    {
        $voice = '';
        $voiceConstraints = $this->getVoiceConstraints();

        if (isset($additionalData['voice']) && $voice = $additionalData['voice']) {
            if (empty($voiceConstraints[$voice])) {
                throw new InvalidVoiceCodeException($voice);
            }

            $voiceConstraint = $voiceConstraints[$voice];
        }

        if (!empty($voiceConstraint)) {
            if ($voiceConstraint->getLanguage()->getCode() == $language->getCode()) {
                return $voice;
            }

            throw new InvalidVoiceParametersException(
                sprintf(
                    "The requested language \"%s\" is not compatible with the requested voice \"%s\"",
                    $language,
                    $voice
                )
            );
        }

        $languageCode = $language->getCode();
        $genderCode = $additionalData['gender'] ?? null;

        $voiceConstraints = array_filter(
            $voiceConstraints,
            function (TtsVoiceFormat $item) use ($languageCode, $genderCode) {
                return
                    ($item->getLanguage()->getCode() == $languageCode) &&
                    (!empty($genderCode) ? $item->getGender() == $genderCode : true);
            }
        );

        if (empty($voiceConstraints)) {
            throw new InvalidVoiceParametersException(
                sprintf(
                    "Couldn't find the voice for requested language \"%s\" and gender \"%s\"",
                    $language,
                    $genderCode ?? 'no gender'
                )
            );
        }

        $voices = array_keys($voiceConstraints);

        return array_shift($voices);
    }
}
