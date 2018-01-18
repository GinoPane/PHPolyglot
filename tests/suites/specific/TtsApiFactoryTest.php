<?php

namespace GinoPane\PHPolyglot;

use GinoPane\PHPolyglot\API\Factory\TTS\TtsApiFactory;
use GinoPane\PHPolyglot\Exception\InvalidApiClassException;
use GinoPane\PHPolyglot\API\Factory\Dictionary\DictionaryApiFactory;

/**
*  Corresponding class to test DictionaryApiFactoryTest class
*
*  @author Sergey <Gino Pane> Karavay
*/
class TtsApiFactoryTest extends PHPolyglotTestCase
{
    public function testIfDictionaryApiFactoryObjectCanBeCreated()
    {
        $this->setInternalProperty(TtsApiFactory::class, 'config', null);

        $this->getTtsApiFactory();

        $this->assertArrayHasKey('IBM_WATSON_TTS_API_USERNAME', $_ENV);
        $this->assertArrayHasKey('IBM_WATSON_TTS_API_PASSWORD', $_ENV);
        $this->assertEquals('IBM_WATSON_TTS_API_TEST_USERNAME', $_ENV['IBM_WATSON_TTS_API_USERNAME']);
        $this->assertEquals('IBM_WATSON_TTS_API_TEST_PASSWORD', getenv('IBM_WATSON_TTS_API_PASSWORD'));
    }

    public function testIfTtsApiFactoryThrowsExceptionOnInvalidClassInConfigFile()
    {
        $this->expectException(InvalidApiClassException::class);

        $this->setInternalProperty(TtsApiFactory::class, 'config', null);

        $stub = $this->getMockBuilder(TtsApiFactory::class)
            ->setMethods(array('getConfigFileName', 'getEnvFileName', 'getRootDirectory'))
            ->disableOriginalConstructor()
            ->getMock();

        $stub->method('getRootDirectory')->willReturn(TEST_ROOT . DIRECTORY_SEPARATOR . 'configs');
        $stub->method('getConfigFileName')->willReturn('invalid2.config.php');
        $stub->method('getEnvFileName')->willReturn('test.env');

        $stub->__construct();
    }

    /**
     * Get stubbed version of DictionaryApiFactory
     *
     * @return TtsApiFactory|\PHPUnit\Framework\MockObject\MockObject
     */
    private function getTtsApiFactory()
    {
        $stub = $this->getMockBuilder(TtsApiFactory::class)
            ->setMethods(array('getConfigFileName', 'getEnvFileName', 'getRootDirectory'))
            ->disableOriginalConstructor()
            ->getMock();

        $stub->method('getRootDirectory')->willReturn(TEST_ROOT . DIRECTORY_SEPARATOR . 'configs');
        $stub->method('getConfigFileName')->willReturn('test.config.php');
        $stub->method('getEnvFileName')->willReturn('test.env');

        $stub->__construct();

        return $stub;
    }
}
