<?php

namespace GinoPane\PHPolyglot;

use GinoPane\PHPolyglot\API\Supplemental\TTS\TtsAudioFormat;
use GinoPane\PHPolyglot\API\Supplemental\TTS\TtsVoiceFormat;
use GinoPane\PHPolyglot\Exception\InvalidGenderCodeException;
use GinoPane\PHPolyglot\Supplemental\Language\Language;

/**
 * Corresponding class to test TtsVoiceFormat class
 *
 * @author Sergey <Gino Pane> Karavay
 */
class TtsVoiceFormatTest extends PHPolyglotTestCase
{
    /**
     * Just check if the TtsVoiceFormat object can be created
     */
    public function testIfRootObjectCanBeCreated()
    {
        $object = new TtsVoiceFormat(new Language('en'), TtsVoiceFormat::GENDER_FEMALE);

        $this->assertTrue($object instanceof TtsVoiceFormat);
        $this->assertEquals(TtsVoiceFormat::GENDER_FEMALE, $object->getGender());
        $this->assertEquals('en', $object->getLanguage()->getCode());
    }

    public function testIfTtsVoiceFormatThrowsExceptionsForUnknownGender()
    {
        $this->expectException(InvalidGenderCodeException::class);

        $object = new TtsVoiceFormat(new Language('en'), 'n');

        $this->assertNull($object);
    }
}
