<?php

namespace GinoPane\PHPolyglot;

use GinoPane\PHPolyglot\API\Response\Translate\TranslateApiResponse;
use GinoPane\PHPolyglot\Exception\InvalidPropertyException;
use GinoPane\PHPolyglot\Exception\BadResponseClassException;
use GinoPane\PHPolyglot\Exception\InvalidEnvironmentException;
use GinoPane\PHPolyglot\API\Factory\Translate\TranslateApiFactory;
use GinoPane\PHPolyglot\API\Implementation\Translate\TranslateApiInterface;
use GinoPane\PHPolyglot\API\Implementation\Translate\Yandex\YandexTranslateApi;

/**
*  Corresponding class to test YandexTranslateApiTest class
*
*  @author Sergey <Gino Pane> Karavay
*/
class YandexTranslateApiTest extends PHPolyglotTestCase
{
    public function testIfTranslateApiCanBeCreatedByFactory()
    {
        $translateApi = $this->getTranslateApiFactory()->getApi();

        $this->assertTrue($translateApi instanceof TranslateApiInterface);
    }

    public function testIfTranslateApiThrowsExceptionWhenPropertyDoesNotExist()
    {
        $this->expectException(InvalidPropertyException::class);

        $stub = $this->getMockBuilder(YandexTranslateApi::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->setInternalProperty($stub, 'envProperties', ['apiKeys' => 'YANDEX_TRANSLATE_API_KEY']);

        $stub->__construct();
    }

    public function testIfTranslateApiThrowsExceptionWhenEnvVariableDoesNotExist()
    {
        $this->expectException(InvalidEnvironmentException::class);

        $stub = $this->getMockBuilder(YandexTranslateApi::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->setInternalProperty($stub, 'envProperties', ['apiKey' => 'WRONG_VARIABLE']);

        $stub->__construct();
    }

    public function testIfTranslateApiThrowsExceptioOnBadResponseClass()
    {
        $this->expectException(BadResponseClassException::class);

        $stub = $this->getMockBuilder(YandexTranslateApi::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->setInternalProperty($stub, 'responseClassName', YandexTranslateApi::class);

        $stub->__construct();
    }

    public function testIfTranslateApiThrowsExceptionForNonExistentMethod()
    {
        $translateApi = $this->getTranslateApiFactory()->getApi();

        $callApiMethod = $this->getInternalMethod($translateApi, 'callApi');

        /** @var TranslateApiResponse $response */
        $response = $callApiMethod->invoke($translateApi, 'wrongMethod', []);

        $this->assertTrue($response instanceof TranslateApiResponse);
        $this->assertFalse($response->isSuccess());
        $this->assertEquals(
            'Specified method "createWrongMethodContext" does not exist',
            $response->getErrorMessage()
        );
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
