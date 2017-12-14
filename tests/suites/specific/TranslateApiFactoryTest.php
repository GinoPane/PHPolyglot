<?php

namespace GinoPane\PHPolyglot;

use Exception;
use GinoPane\PHPolyglot\API\Factory\Translate\TranslateApiFactory;
use GinoPane\PHPolyglot\API\Implementation\Translate\TranslateApiInterface;

/**
*  Corresponding class to test TranslateApiFactory class
*
*  @author Sergey <Gino Pane> Karavay
*/
class TranslateApiFactoryTest extends PHPolyglotTestCase
{
    public function testIfTranslateApiFactoryThrowsExceptionOnWrongConfigFile()
    {
        $this->expectException(Exception::class);

        $stub = $this->getMockBuilder(TranslateApiFactory::class)
            ->setMethods(array('getConfigFileName', 'getEnvFileName', 'getRootDirectory'))
            ->disableOriginalConstructor()
            ->getMock();

        $stub->method('getRootDirectory')->willReturn(TEST_ROOT);
        $stub->method('getConfigFileName')->willReturn('test');
        $stub->method('getEnvFileName')->willReturn('test');

        $stub->__construct();
    }

    public function testIfTranslateApiFactoryThrowsExceptionOnWrongEnvFile()
    {
        $this->expectException(Exception::class);

        $stub = $this->getMockBuilder(TranslateApiFactory::class)
            ->setMethods(array('getConfigFileName', 'getEnvFileName', 'getRootDirectory'))
            ->disableOriginalConstructor()
            ->getMock();

        $stub->method('getRootDirectory')->willReturn(TEST_ROOT);
        $stub->method('getConfigFileName')->willReturn('test.config.php');
        $stub->method('getEnvFileName')->willReturn('test');

        $stub->__construct();
    }

    public function testIfTranslateApiFactoryObjectCanBeCreated()
    {
        $this->getTranslateApiFactory();

        $this->assertArrayHasKey('YANDEX_TRANSLATE_API_KEY', $_ENV);
        $this->assertEquals('YANDEX_TRANSLATE_TEST_KEY', $_ENV['YANDEX_TRANSLATE_API_KEY']);
        $this->assertEquals('YANDEX_TRANSLATE_TEST_KEY', getenv('YANDEX_TRANSLATE_API_KEY'));
    }

    public function testIfTranslateApiCanBeCreatedByFactory()
    {
        $translateApi = $this->getTranslateApiFactory()->getApi();

        $this->assertTrue($translateApi instanceof TranslateApiInterface);
    }

    /**
     * Get stubbed version of TranslateApiFactory
     *
     * @return TranslateApiFactory|\PHPUnit\Framework\MockObject\MockObject
     */
    private function getTranslateApiFactory()
    {
        $stub = $this->getMockBuilder(TranslateApiFactory::class)
            ->setMethods(array('getConfigFileName', 'getEnvFileName', 'getRootDirectory'))
            ->disableOriginalConstructor()
            ->getMock();

        $stub->method('getRootDirectory')->willReturn(TEST_ROOT);
        $stub->method('getConfigFileName')->willReturn('test.config.php');
        $stub->method('getEnvFileName')->willReturn('test.env');

        $stub->__construct();

        return $stub;
    }
}
