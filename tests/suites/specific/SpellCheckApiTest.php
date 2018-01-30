<?php

namespace GinoPane\PHPolyglot;

use GinoPane\NanoRest\NanoRest;
use GinoPane\NanoRest\Request\RequestContext;
use GinoPane\NanoRest\Response\DummyResponseContext;
use GinoPane\NanoRest\Response\ResponseContext;
use GinoPane\NanoRest\Response\JsonResponseContext;
use GinoPane\PHPolyglot\API\Factory\SpellCheck\SpellCheckApiFactory;
use GinoPane\PHPolyglot\API\Factory\TTS\TtsApiFactory;
use GinoPane\PHPolyglot\API\Implementation\SpellCheck\SpellCheckApiInterface;
use GinoPane\PHPolyglot\API\Implementation\TTS\IbmWatson\IbmWatsonTtsApi;
use GinoPane\PHPolyglot\API\Response\SpellCheck\SpellCheckResponse;
use GinoPane\PHPolyglot\API\Response\TTS\TtsResponse;
use GinoPane\PHPolyglot\API\Supplemental\TTS\TtsAudioFormat;
use GinoPane\PHPolyglot\Exception\InvalidAudioFormatCodeException;
use GinoPane\PHPolyglot\Exception\InvalidAudioFormatParameterException;
use GinoPane\PHPolyglot\Exception\InvalidIoException;
use GinoPane\PHPolyglot\Exception\InvalidPathException;
use GinoPane\PHPolyglot\Exception\InvalidPropertyException;
use GinoPane\PHPolyglot\Exception\InvalidEnvironmentException;
use GinoPane\PHPolyglot\Exception\InvalidVoiceCodeException;
use GinoPane\PHPolyglot\Exception\InvalidVoiceParametersException;
use GinoPane\PHPolyglot\Supplemental\Language\Language;

/**
*  Corresponding class to test SpellCheckApi class
*
*  @author Sergey <Gino Pane> Karavay
*/
class SpellCheckApiTest extends PHPolyglotTestCase
{
    public function testIfSpellCheckApiCanBeCreatedByFactory()
    {
        $ttsApi = $this->getSpellCheckApiFactory()->getApi();

        $this->assertTrue($ttsApi instanceof SpellCheckApiInterface);
    }

    public function testIfValidRequestContextIsCreated()
    {
        $spellCheckApi = $this->getSpellCheckApiFactory()->getApi();

        $createRequestMethod = $this->getInternalMethod($spellCheckApi, 'createCheckTextsContext');

        $textStrings = ['Hello', 'World'];
        /** @var RequestContext $context */
        $context = $createRequestMethod->invoke(
            $spellCheckApi,
            $textStrings,
            new Language('en')
        );

        $this->assertTrue($context instanceof RequestContext);
        $this->assertEquals(
            'http://speller.yandex.net/services/spellservice.json/checkTexts?lang=en',
            $context->getRequestUrl()
        );
        $this->assertEquals('text=Hello&text=World', $context->getRequestData());
    }

    public function testIfValidResponseCanBeProcessed()
    {
        $nanoRest = $this->getMockBuilder(NanoRest::class)
            ->setMethods(array('sendRequest'))
            ->getMock();

        $nanoRest->method('sendRequest')->willReturn($this->getResponseContext());

        $spellCheckApi = $this->getSpellCheckApiFactory()->getApi();

        $this->setInternalProperty($spellCheckApi, 'httpClient', $nanoRest);

        /** @var SpellCheckResponse $response */
        $response = $spellCheckApi->checkTexts([], new Language(''));

        $this->assertTrue($response instanceof SpellCheckResponse);
        $this->assertEquals([
                [
                    'Helo' =>
                        [
                            'Hello',
                        ],
                ],
                [
                    'Thanxs' =>
                        [
                            'Thanks',
                        ],
                    'api' =>
                        [
                            'API',
                        ],
                ],
        ], $response->getCorrections());
    }

    private function getResponseContext(): ResponseContext
    {
        $context = new JsonResponseContext();

        $context->setContent('[
            [
                {
                    "code": 1,
                    "pos": 0,
                    "row": 0,
                    "col": 0,
                    "len": 4,
                    "word": "Helo",
                    "s": [
                        "Hello"
                    ]
                },
                {
                    "code": 1,
                    "pos": 5,
                    "row": 0,
                    "col": 5,
                    "len": 5,
                    "word": "werld",
                    "s": []
                }
            ],
            [
                {
                    "code": 1,
                    "pos": 0,
                    "row": 0,
                    "col": 0,
                    "len": 6,
                    "word": "Thanxs",
                    "s": [
                        "Thanks"
                    ]
                },
                {
                    "code": 1,
                    "pos": 11,
                    "row": 0,
                    "col": 11,
                    "len": 7,
                    "s": [
                        "using"
                    ]
                },
                {
                    "code": 3,
                    "pos": 24,
                    "row": 0,
                    "col": 24,
                    "len": 3,
                    "word": "api",
                    "s": [
                        "API"
                    ]
                }
            ]
        ]');
        $context->setHttpStatusCode(200);

        return $context;
    }

    /**
     * Get stubbed version of SpellCheckApiFactory
     *
     * @return SpellCheckApiFactory|\PHPUnit\Framework\MockObject\MockObject
     */
    private function getSpellCheckApiFactory()
    {
        $this->setInternalProperty(SpellCheckApiFactory::class, 'config', null);

        $stub = $this->getMockBuilder(SpellCheckApiFactory::class)
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
