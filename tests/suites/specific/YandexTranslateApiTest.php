<?php

namespace GinoPane\PHPolyglot;

use GinoPane\NanoRest\NanoRest;
use GinoPane\NanoRest\Request\RequestContext;
use GinoPane\NanoRest\Response\ResponseContext;
use GinoPane\NanoRest\Response\JsonResponseContext;
use GinoPane\PHPolyglot\Exception\InvalidConfigException;
use GinoPane\PHPolyglot\Exception\InvalidPropertyException;
use GinoPane\PHPolyglot\Exception\InvalidEnvironmentException;
use GinoPane\PHPolyglot\Exception\MethodDoesNotExistException;
use GinoPane\PHPolyglot\API\Factory\Translate\TranslateApiFactory;
use GinoPane\PHPolyglot\API\Response\Translate\TranslateResponse;
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
        $this->setInternalProperty(TranslateApiFactory::class, 'config', null);

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

    public function testIfTranslateApiThrowsExceptionForNonExistentMethod()
    {
        $this->expectException(MethodDoesNotExistException::class);
        $this->expectExceptionMessage('Specified method "createWrongMethodContext" does not exist');

        $translateApi = $this->getTranslateApiFactory()->getApi();

        $callApiMethod = $this->getInternalMethod($translateApi, 'callApi');

        $callApiMethod->invoke($translateApi, 'wrongMethod', []);
    }

    public function testIfTranslateApiCreatesValidTranslateRequestContext()
    {
        $translateApi = $this->getTranslateApiFactory()->getApi();

        $createRequestMethod = $this->getInternalMethod($translateApi, 'createTranslateContext');

        $translateString = 'Hello World!';
        /** @var RequestContext $context */
        $context = $createRequestMethod->invoke($translateApi, $translateString, 'en', 'ru');

        $this->assertTrue($context instanceof RequestContext);
        $this->assertEquals(
            'https://translate.yandex.net/api/v1.5/tr.json/translate?lang=ru-en&key=YANDEX_TRANSLATE_TEST_KEY',
            $context->getRequestUrl()
        );
        $this->assertEquals('text=' . urlencode($translateString), $context->getRequestData());
    }

    public function testIfTranslateApiCreatesValidTranslateRequestContextWithOneLanguageOnly()
    {
        $translateApi = $this->getTranslateApiFactory()->getApi();

        $createRequestMethod = $this->getInternalMethod($translateApi, 'createTranslateContext');

        $translateString = 'Hello World!';
        /** @var RequestContext $context */
        $context = $createRequestMethod->invoke($translateApi, $translateString, 'en', '');

        $this->assertTrue($context instanceof RequestContext);
        $this->assertEquals(
            'https://translate.yandex.net/api/v1.5/tr.json/translate?lang=en&key=YANDEX_TRANSLATE_TEST_KEY',
            $context->getRequestUrl()
        );
        $this->assertEquals('text=' . urlencode($translateString), $context->getRequestData());
    }

    public function testIfTranslateApiCreatesValidBulkTranslateRequestContext()
    {
        $translateApi = $this->getTranslateApiFactory()->getApi();

        $createRequestMethod = $this->getInternalMethod($translateApi, 'createTranslateBulkContext');

        $translateStrings = [
            'Hello',
            'world'
        ];
        /** @var RequestContext $context */
        $context = $createRequestMethod->invoke($translateApi, $translateStrings, 'en', 'ru');

        $this->assertTrue($context instanceof RequestContext);
        $this->assertEquals(
            'https://translate.yandex.net/api/v1.5/tr.json/translate?lang=ru-en&key=YANDEX_TRANSLATE_TEST_KEY',
            $context->getRequestUrl()
        );
        $this->assertEquals('text=Hello&text=world', $context->getRequestData());
    }

    /**
     * @dataProvider getErroneousResponsesForErrorProcessing
     *
     * @param ResponseContext $context
     * @param string          $expectedError
     * @param int             $expectedErrorCode
     *
     * @throws InvalidConfigException
     */
    public function testIfProcessApiErrorsWorksCorrectly(ResponseContext $context, string $expectedError, int $expectedErrorCode = 0)
    {
        $this->expectExceptionCode($expectedErrorCode);
        $this->expectExceptionMessage($expectedError);

        $nanoRest = $this->getMockBuilder(NanoRest::class)
            ->setMethods(array('sendRequest'))
            ->getMock();

        $nanoRest->method('sendRequest')->willReturn($context);

        $translateApi = $this->getTranslateApiFactory()->getApi();

        $this->setInternalProperty($translateApi, 'httpClient', $nanoRest);

        $callApiMethod = $this->getInternalMethod($translateApi, 'callApi');

        $callApiMethod->invoke($translateApi, 'translate', ['','','']);
    }

    /**
     * @dataProvider getValidResponsesForResponseProcessing
     *
     * @param ResponseContext $context
     *
     * @param array           $translations
     * @param string          $languageFrom
     * @param string          $languageTo
     *
     * @throws InvalidConfigException
     */
    public function testIfValidResponseCanBeProcessed(
        ResponseContext $context,
        array $translations,
        string $languageFrom,
        string $languageTo
    ) {
        $nanoRest = $this->getMockBuilder(NanoRest::class)
            ->setMethods(array('sendRequest'))
            ->getMock();

        $nanoRest->method('sendRequest')->willReturn($context);

        $translateApi = $this->getTranslateApiFactory()->getApi();

        $this->setInternalProperty($translateApi, 'httpClient', $nanoRest);

        /** @var TranslateResponse $response */
        $response = $translateApi->translate('', '', '');

        $this->assertTrue($response instanceof TranslateResponse);
        $this->assertEquals($languageTo, $response->getLanguageTo());
        $this->assertEquals($languageFrom, $response->getLanguageFrom());
        $this->assertEquals($translations, $response->getTranslations());
        $this->assertEquals($translations[0], (string)$response);
    }

    /**
     * @dataProvider getValidResponsesForBulkResponseProcessing
     *
     * @param ResponseContext $context
     *
     * @param array           $translations
     * @param string          $languageFrom
     * @param string          $languageTo
     *
     * @throws InvalidConfigException
     */
    public function testIfValidBulkResponseCanBeProcessed(
        ResponseContext $context,
        array $translations,
        string $languageFrom,
        string $languageTo
    ) {
        $nanoRest = $this->getMockBuilder(NanoRest::class)
            ->setMethods(array('sendRequest'))
            ->getMock();

        $nanoRest->method('sendRequest')->willReturn($context);

        $translateApi = $this->getTranslateApiFactory()->getApi();

        $this->setInternalProperty($translateApi, 'httpClient', $nanoRest);

        /** @var TranslateResponse $response */
        $response = $translateApi->translateBulk([], '', '');

        $this->assertTrue($response instanceof TranslateResponse);
        $this->assertEquals($languageTo, $response->getLanguageTo());
        $this->assertEquals($languageFrom, $response->getLanguageFrom());
        $this->assertEquals($translations, $response->getTranslations());
        $this->assertEquals(implode(PHP_EOL, $translations), (string)$response);
    }

    /**
     * Get stubbed version of TranslateApiFactory
     *
     * @param array $methods
     *
     * @return TranslateApiFactory|\PHPUnit\Framework\MockObject\MockObject
     */
    private function getTranslateApiFactory(array $methods = [])
    {
        $stub = $this->getMockBuilder(TranslateApiFactory::class)
            ->setMethods(array('getConfigFileName', 'getEnvFileName', 'getRootDirectory') + $methods)
            ->disableOriginalConstructor()
            ->getMock();

        $stub->method('getRootDirectory')->willReturn(TEST_ROOT . DIRECTORY_SEPARATOR . 'configs');
        $stub->method('getConfigFileName')->willReturn('test.config.php');
        $stub->method('getEnvFileName')->willReturn('test.env');

        $stub->__construct();

        return $stub;
    }

    /**
     * @return array
     */
    public function getErroneousResponsesForErrorProcessing(): array
    {
        return [
            [
                new JsonResponseContext(),
                'Response status undefined',
                0
            ],
            [
                new JsonResponseContext('{
                    "code": 501,
                    "message": "The specified translation direction is not supported"
                }'),
                'The specified translation direction is not supported',
                501
            ],
            [
                new JsonResponseContext('{
                    "code": 401
                }'),
                'Invalid API key',
                401
            ],
            [
                (new JsonResponseContext('{
                    "code": 405
                }'))->setHttpStatusCode(405),
                'Method Not Allowed',
                405
            ],
            [
                (new JsonResponseContext('{
                    "code": 200
                }'))->setHttpStatusCode(200),
                'There is no required field "text" in response',
                0
            ],
        ];
    }

    /**
     * @return array
     */
    public function getValidResponsesForResponseProcessing(): array
    {
        return [
            [
                (
                    new JsonResponseContext('{
                        "code": 200,
                        "lang": "ru-en",
                        "text": [
                            "Hello World!"
                        ]
                    }')
                )->setHttpStatusCode(200),
                ['Hello World!'],
                'ru',
                'en'
            ],
            [
                (
                new JsonResponseContext('{
                        "code": 200,
                        "lang": "no-en",
                        "text": [
                            "Hello World!"
                        ]
                    }')
                )->setHttpStatusCode(200),
                ['Hello World!'],
                '',
                'en'
            ]
        ];
    }

    /**
     * @return array
     */
    public function getValidResponsesForBulkResponseProcessing(): array
    {
        return [
            [
                (
                new JsonResponseContext('{
                        "code": 200,
                        "lang": "ru-en",
                        "text": [
                            "Hello",
                            "World"
                        ]
                    }')
                )->setHttpStatusCode(200),
                ['Hello', 'World'],
                'ru',
                'en'
            ],
            [
                (
                new JsonResponseContext('{
                        "code": 200,
                        "lang": "no-en",
                        "text": [
                            "Hello",
                            "World"
                        ]
                    }')
                )->setHttpStatusCode(200),
                ['Hello', 'World'],
                '',
                'en'
            ]
        ];
    }
}
