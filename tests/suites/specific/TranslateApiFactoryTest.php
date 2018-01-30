<?php

namespace GinoPane\PHPolyglot;

use GinoPane\PHPolyglot\Exception\InvalidPathException;
use GinoPane\PHPolyglot\Exception\InvalidConfigException;
use GinoPane\PHPolyglot\Exception\InvalidApiClassException;
use GinoPane\PHPolyglot\API\Factory\Translate\TranslateApiFactory;

/**
*  Corresponding class to test TranslateApiFactory class
*
*  @author Sergey <Gino Pane> Karavay
*/
class TranslateApiFactoryTest extends PHPolyglotTestCase
{
    public function testIfTranslateApiFactoryThrowsExceptionOnWrongConfigFile()
    {
        $this->setInternalProperty(TranslateApiFactory::class, 'config', null);

        $this->expectException(InvalidPathException::class);

        $stub = $this->getMockBuilder(TranslateApiFactory::class)
            ->setMethods(array('getConfigFileName', 'getEnvFileName', 'getRootDirectory'))
            ->disableOriginalConstructor()
            ->getMock();

        $stub->method('getRootDirectory')->willReturn(TEST_ROOT . DIRECTORY_SEPARATOR . 'configs');
        $stub->method('getConfigFileName')->willReturn('test');
        $stub->method('getEnvFileName')->willReturn('test');

        $stub->__construct();
    }

    public function testIfTranslateApiFactoryThrowsExceptionOnWrongEnvFile()
    {
        $this->expectException(InvalidPathException::class);

        $stub = $this->getMockBuilder(TranslateApiFactory::class)
            ->setMethods(array('getConfigFileName', 'getEnvFileName', 'getRootDirectory'))
            ->disableOriginalConstructor()
            ->getMock();

        $stub->method('getRootDirectory')->willReturn(TEST_ROOT . DIRECTORY_SEPARATOR . 'configs');
        $stub->method('getConfigFileName')->willReturn('test.config.php');
        $stub->method('getEnvFileName')->willReturn('test');

        $stub->__construct();
    }

    public function testIfTranslateApiFactoryObjectCanBeCreated()
    {
        $this->setInternalProperty(TranslateApiFactory::class, 'config', null);

        $this->getTranslateApiFactory();

        $this->assertArrayHasKey('YANDEX_TRANSLATE_API_KEY', $_ENV);
        $this->assertEquals('YANDEX_TRANSLATE_TEST_KEY', $_ENV['YANDEX_TRANSLATE_API_KEY']);
        $this->assertEquals('YANDEX_TRANSLATE_TEST_KEY', getenv('YANDEX_TRANSLATE_API_KEY'));
    }

    public function testIfTranslateApiFactoryThrowsExceptionOnInvalidConfigFile()
    {
        $this->expectException(InvalidConfigException::class);

        $this->setInternalProperty(TranslateApiFactory::class, 'config', null);

        $stub = $this->getMockBuilder(TranslateApiFactory::class)
            ->setMethods(array('getConfigFileName', 'getEnvFileName', 'getRootDirectory'))
            ->disableOriginalConstructor()
            ->getMock();

        $stub->method('getRootDirectory')->willReturn(TEST_ROOT . DIRECTORY_SEPARATOR . 'configs');
        $stub->method('getConfigFileName')->willReturn('invalid1.config.php');
        $stub->method('getEnvFileName')->willReturn('test.env');

        $stub->__construct();
    }

    public function testIfTranslateApiFactoryThrowsExceptionOnInvalidClassInConfigFile()
    {
        $this->expectException(InvalidApiClassException::class);

        $this->setInternalProperty(TranslateApiFactory::class, 'config', null);

        $stub = $this->getMockBuilder(TranslateApiFactory::class)
            ->setMethods(array('getConfigFileName', 'getEnvFileName', 'getRootDirectory'))
            ->disableOriginalConstructor()
            ->getMock();

        $stub->method('getRootDirectory')->willReturn(TEST_ROOT . DIRECTORY_SEPARATOR . 'configs');
        $stub->method('getConfigFileName')->willReturn('invalid2.config.php');
        $stub->method('getEnvFileName')->willReturn('test.env');

        $stub->__construct();
    }

    public function testIfTranslateApiFactoryThrowsExceptionOnNonExistingClassInConfigFile()
    {
        $this->expectException(InvalidApiClassException::class);

        $this->setInternalProperty(TranslateApiFactory::class, 'config', null);

        $stub = $this->getMockBuilder(TranslateApiFactory::class)
            ->setMethods(array('getConfigFileName', 'getEnvFileName', 'getRootDirectory'))
            ->disableOriginalConstructor()
            ->getMock();

        $stub->method('getRootDirectory')->willReturn(TEST_ROOT . DIRECTORY_SEPARATOR . 'configs');
        $stub->method('getConfigFileName')->willReturn('invalid3.config.php');
        $stub->method('getEnvFileName')->willReturn('test.env');

        $stub->__construct();
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

        $stub->method('getRootDirectory')->willReturn(TEST_ROOT . DIRECTORY_SEPARATOR . 'configs');
        $stub->method('getConfigFileName')->willReturn('test.config.php');
        $stub->method('getEnvFileName')->willReturn('test.env');

        $stub->__construct();

        return $stub;
    }
}
