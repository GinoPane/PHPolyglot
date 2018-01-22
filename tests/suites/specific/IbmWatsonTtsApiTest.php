<?php

namespace GinoPane\PHPolyglot;

use GinoPane\NanoRest\NanoRest;
use GinoPane\NanoRest\Request\RequestContext;
use GinoPane\NanoRest\Response\ResponseContext;
use GinoPane\NanoRest\Response\JsonResponseContext;
use GinoPane\PHPolyglot\API\Factory\TTS\TtsApiFactory;
use GinoPane\PHPolyglot\API\Implementation\TTS\IbmWatson\IbmWatsonTtsApi;
use GinoPane\PHPolyglot\API\Implementation\TTS\TtsApiInterface;
use GinoPane\PHPolyglot\API\Supplemental\TTS\TtsAudioFormat;
use GinoPane\PHPolyglot\Exception\InvalidConfigException;
use GinoPane\PHPolyglot\Exception\InvalidPropertyException;
use GinoPane\PHPolyglot\Exception\InvalidEnvironmentException;
use GinoPane\PHPolyglot\API\Factory\Translate\TranslateApiFactory;
use GinoPane\PHPolyglot\API\Response\Translate\TranslateResponse;
use GinoPane\PHPolyglot\Supplemental\Language\Language;

/**
*  Corresponding class to test IbmWatsonTtsApi class
*
*  @author Sergey <Gino Pane> Karavay
*/
class IbmWatsonTtsApiTest extends PHPolyglotTestCase
{
    public function testIfTtsApiCanBeCreatedByFactory()
    {
        $this->setInternalProperty(TranslateApiFactory::class, 'config', null);

        $translateApi = $this->getTtsApiFactory()->getApi();

        $this->assertTrue($translateApi instanceof TtsApiInterface);
    }

    public function testIfTtsApiThrowsExceptionWhenPropertyDoesNotExist()
    {
        $this->expectException(InvalidPropertyException::class);

        $stub = $this->getMockBuilder(IbmWatsonTtsApi::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->setInternalProperty($stub, 'envProperties', ['user_name' => 'IBM_WATSON_TTS_API_USERNAME']);

        $stub->__construct();
    }

    public function testIfTtsApiThrowsExceptionWhenEnvVariableDoesNotExist()
    {
        $this->expectException(InvalidEnvironmentException::class);

        $stub = $this->getMockBuilder(IbmWatsonTtsApi::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->setInternalProperty($stub, 'envProperties', ['username' => 'WRONG_VARIABLE']);

        $stub->__construct();
    }

    /**
     * @dataProvider getDataForTtsContext
     *
     * @param string $language
     * @param string $audio
     * @param array  $additional
     * @param string $expected
     */
    public function testIfTtsApiCreatesValidTextToSpeechRequestContext(
        string $language,
        string $audio,
        array $additional,
        $expected
    ) {
        $ttsApi = $this->getTtsApiFactory()->getApi();

        $createRequestMethod = $this->getInternalMethod($ttsApi, 'createTextToSpeechContext');

        $textString = 'Hello World!';
        /** @var RequestContext $context */
        $context = $createRequestMethod->invoke(
            $ttsApi,
            $textString,
            new Language($language),
            new TtsAudioFormat($audio),
            $additional
        );

        $this->assertTrue($context instanceof RequestContext);
        $this->assertEquals(
            $expected,
            $context->getRequestUrl()
        );
        $this->assertEquals(json_encode(['text' => $textString]), $context->getRequestData());
        $this->assertEquals($context->getCurlOptions()[CURLOPT_USERPWD], 'IBM_WATSON_TTS_API_TEST_USERNAME:IBM_WATSON_TTS_API_TEST_PASSWORD');
    }

    public function testIfTranslateApiCreatesValidTranslateRequestContextWithOneLanguageOnly()
    {
        $this->markTestSkipped();

        $translateApi = $this->getTranslateApiFactory()->getApi();

        $createRequestMethod = $this->getInternalMethod($translateApi, 'createTranslateContext');

        $translateString = 'Hello World!';
        /** @var RequestContext $context */
        $context = $createRequestMethod->invoke($translateApi, $translateString, new Language('en'), new Language());

        $this->assertTrue($context instanceof RequestContext);
        $this->assertEquals(
            'https://translate.yandex.net/api/v1.5/tr.json/translate?lang=en&key=YANDEX_TRANSLATE_TEST_KEY',
            $context->getRequestUrl()
        );
        $this->assertEquals('text=' . urlencode($translateString), $context->getRequestData());
    }

    public function testIfTranslateApiCreatesValidBulkTranslateRequestContext()
    {
        $this->markTestSkipped();

        $translateApi = $this->getTranslateApiFactory()->getApi();

        $createRequestMethod = $this->getInternalMethod($translateApi, 'createTranslateBulkContext');

        $translateStrings = [
            'Hello',
            'world'
        ];
        /** @var RequestContext $context */
        $context = $createRequestMethod->invoke($translateApi, $translateStrings, new Language('en'), new Language('ru'));

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
     */
    public function testIfProcessApiErrorsWorksCorrectly(ResponseContext $context, string $expectedError, int $expectedErrorCode = 0)
    {
        $this->markTestSkipped();

        $this->expectExceptionCode($expectedErrorCode);
        $this->expectExceptionMessage($expectedError);

        $nanoRest = $this->getMockBuilder(NanoRest::class)
            ->setMethods(array('sendRequest'))
            ->getMock();

        $nanoRest->method('sendRequest')->willReturn($context);

        $translateApi = $this->getTranslateApiFactory()->getApi();

        $this->setInternalProperty($translateApi, 'httpClient', $nanoRest);

        $callApiMethod = $this->getInternalMethod($translateApi, 'callApi');

        $callApiMethod->invoke($translateApi, 'translate', ['', new Language(), new Language()]);
    }

    /**
     * @dataProvider getValidResponsesForResponseProcessing
     *
     * @param ResponseContext $context
     *
     * @param array           $translations
     * @param string          $languageFrom
     * @param string          $languageTo
     */
    public function testIfValidResponseCanBeProcessed(
        ResponseContext $context,
        array $translations,
        string $languageFrom,
        string $languageTo
    ) {
        $this->markTestSkipped();

        $nanoRest = $this->getMockBuilder(NanoRest::class)
            ->setMethods(array('sendRequest'))
            ->getMock();

        $nanoRest->method('sendRequest')->willReturn($context);

        $translateApi = $this->getTranslateApiFactory()->getApi();

        $this->setInternalProperty($translateApi, 'httpClient', $nanoRest);

        /** @var TranslateResponse $response */
        $response = $translateApi->translate('', new Language(''), new Language(''));

        $this->assertTrue($response instanceof TranslateResponse);
        $this->assertEquals($languageTo, $response->getLanguageTo());
        $this->assertEquals($languageFrom, $response->getLanguageFrom());
        $this->assertEquals($translations, $response->getTranslations());
        $this->assertEquals($translations[0], (string)$response);
    }

    /**
     * @return array
     */
    public function getDataForTtsContext(): array
    {
        return [
            [
                'en',
                '',
                [],
                'https://stream.watsonplatform.net/text-to-speech/api/v1/synthesize?voice=en-US_AllisonVoice'
            ],
            [
                'en',
                '',
                ['voice' => 'en-US_LisaVoice'],
                'https://stream.watsonplatform.net/text-to-speech/api/v1/synthesize?voice=en-US_LisaVoice'
            ],
            [
                'de',
                '',
                ['gender' => 'm'],
                'https://stream.watsonplatform.net/text-to-speech/api/v1/synthesize?voice=de-DE_DieterVoice'
            ],
            [
                'de',
                '',
                ['gender' => 'f'],
                'https://stream.watsonplatform.net/text-to-speech/api/v1/synthesize?voice=de-DE_BirgitVoice'
            ]
        ];
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

    /**
     * @return array
     */
    public function getErroneousResponsesForErrorProcessing(): array
    {
        return [
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
