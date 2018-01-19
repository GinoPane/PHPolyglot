<?php

namespace GinoPane\PHPolyglot\API\Supplemental\TTS;

use GinoPane\PHPolyglot\Supplemental\Language\Language;
use GinoPane\PHPolyglot\Exception\InvalidGenderCodeException;

/**
 * Class TtsVoiceFormat
 *
 * @author Sergey <Gino Pane> Karavay
 */
class TtsVoiceFormat
{
    const GENDER_MALE = 'm';
    const GENDER_FEMALE = 'f';

    /**
     * Language to which the voice corresponds
     *
     * @var Language
     */
    private $language = null;

    /**
     * Gender to which the voice corresponds
     *
     * @var string
     */
    private $gender = '';

    /**
     * TtsVoiceFormat constructor
     *
     * @param Language $language
     * @param string   $gender
     *
     * @throws InvalidGenderCodeException
     */
    public function __construct(Language $language, string $gender)
    {
        $this->language = $language;

        $this->setGender($gender);
    }

    /**
     * @param string $gender
     *
     * @throws InvalidGenderCodeException
     */
    private function setGender(string $gender)
    {
        $gender = strtolower($gender);

        if (!in_array($gender, [self::GENDER_MALE, self::GENDER_FEMALE])) {
            throw new InvalidGenderCodeException(
                sprintf("Gender code \"%s\" is invalid", $gender)
            );
        }

        $this->gender = $gender;
    }
}
