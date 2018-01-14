<?php

namespace GinoPane\PHPolyglot;

use GinoPane\PHPolyglot\API\Supplemental\TTS\TtsAudioFormat;

/**
 * Corresponding class to test TtsAudioFormat class
 *
 * @author Sergey <Gino Pane> Karavay
 */
class TtsAudioFormatTest extends PHPolyglotTestCase
{
    /**
     * Just check if the Language object can be created
     */
    public function testIfRootObjectCanBeCreated()
    {
        $object = new TtsAudioFormat();

        $this->assertTrue($object instanceof TtsAudioFormat);
    }

    /**
     * @dataProvider getFormats
     *
     * @param string    $format
     * @param string    $expectedFormat
     */
    public function testIfFormatCheckWorksCorrectly(
        string $format,
        string $expectedFormat
    ) {
        $this->assertEquals($expectedFormat, (new TtsAudioFormat($format))->getFormat());
    }

    /**
     * @dataProvider getExtensions
     *
     * @param string    $format
     * @param string    $expectedExtension
     */
    public function testIfExtensionCheckWorksCorrectly(
        string $format,
        string $expectedExtension
    ) {
        $this->assertEquals($expectedExtension, (new TtsAudioFormat($format))->getFileExtension());
    }

    /**
     * @return array
     */
    public function getFormats(): array
    {
        return [
            ['Basic', TtsAudioFormat::AUDIO_BASIC],
            ['ogg', TtsAudioFormat::AUDIO_OGG],
            ['mpeG', TtsAudioFormat::AUDIO_MPEG],
            ['webm', TtsAudioFormat::AUDIO_WEBM],
            ['some name', TtsAudioFormat::AUDIO_MP3]
        ];
    }

    /**
     * @return array
     */
    public function getExtensions(): array
    {
        return [
            ['Basic', 'au'],
            ['ogg', 'ogg'],
            ['mpeG', 'mp3'],
            ['webm', 'webm'],
            ['some name', 'mp3']
        ];
    }
}
