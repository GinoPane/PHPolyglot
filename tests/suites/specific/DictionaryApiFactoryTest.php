<?php

namespace GinoPane\PHPolyglot;

use GinoPane\PHPolyglot\Exception\InvalidApiClassException;
use GinoPane\PHPolyglot\API\Factory\Dictionary\DictionaryApiFactory;

/**
*  Corresponding class to test DictionaryApiFactoryTest class
*
*  @author Sergey <Gino Pane> Karavay
*/
class DictionaryApiFactoryTest extends PHPolyglotTestCase
{
    public function testIfDictionaryApiFactoryObjectCanBeCreated()
    {
        $this->getDictionaryApiFactory();

        $this->assertArrayHasKey('YANDEX_DICTIONARY_API_KEY', $_ENV);
        $this->assertEquals('YANDEX_DICTIONARY_TEST_KEY', $_ENV['YANDEX_DICTIONARY_API_KEY']);
        $this->assertEquals('YANDEX_DICTIONARY_TEST_KEY', getenv('YANDEX_DICTIONARY_API_KEY'));
    }

    public function testIfDictionaryApiFactoryThrowsExceptionOnInvalidClassInConfigFile()
    {
        $this->expectException(InvalidApiClassException::class);

        $this->setInternalProperty(DictionaryApiFactory::class, 'config', null);

        $stub = $this->getMockBuilder(DictionaryApiFactory::class)
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
     * @return DictionaryApiFactory|\PHPUnit\Framework\MockObject\MockObject
     */
    private function getDictionaryApiFactory()
    {
        $stub = $this->getMockBuilder(DictionaryApiFactory::class)
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
