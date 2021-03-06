<?php

namespace GinoPane\PHPolyglot;

use GinoPane\NanoRest\NanoRest;
use GinoPane\NanoRest\Request\RequestContext;
use GinoPane\NanoRest\Response\DummyResponseContext;
use GinoPane\NanoRest\Response\JsonResponseContext;
use GinoPane\NanoRest\Response\ResponseContextAbstract;
use GinoPane\PHPolyglot\API\Factory\ApiFactoryAbstract;
use GinoPane\PHPolyglot\API\Factory\Dictionary\DictionaryApiFactory;
use GinoPane\PHPolyglot\API\Factory\SpellCheck\SpellCheckApiFactory;
use GinoPane\PHPolyglot\API\Factory\Translate\TranslateApiFactory;
use GinoPane\PHPolyglot\API\Factory\TTS\TtsApiFactory;
use GinoPane\PHPolyglot\API\Implementation\ApiAbstract;
use GinoPane\PHPolyglot\API\Implementation\Translate\TranslateApiInterface;
use GinoPane\PHPolyglot\API\Implementation\Translate\Yandex\YandexTranslateApi;
use GinoPane\PHPolyglot\API\Response\Dictionary\Entry\POS\DictionaryEntryPos;
use GinoPane\PHPolyglot\API\Response\SpellCheck\SpellCheckResponse;
use GinoPane\PHPolyglot\Exception\InvalidConfigException;
use GinoPane\PHPolyglot\Exception\InvalidEnvironmentException;
use GinoPane\PHPolyglot\Exception\InvalidLanguageCodeException;

/**
 * Corresponding class to test PHPolyglot class
 *
 * @author Sergey <Gino Pane> Karavay
 */
class PHPolyglotTest extends PHPolyglotTestCase
{
    /**
     * Just check if the PHPolyglot can be created
     */
    public function testIfRootObjectCanBeCreated()
    {
        $object = new PHPolyglot();

        $this->assertTrue(is_object($object));
    }

    public function testIfGetApiThrowsExceptionForBadEnv()
    {
        $this->expectException(InvalidEnvironmentException::class);

        unset($_SERVER['YANDEX_TRANSLATE_API_KEY']);
        unset($_ENV['YANDEX_TRANSLATE_API_KEY']);
        putenv('YANDEX_TRANSLATE_API_KEY');

        $this->setInternalProperty(ApiFactoryAbstract::class, 'config', null);
        $this->setInternalProperty(ApiFactoryAbstract::class, 'envIsSet', false);

        $object = new PHPolyglot(null, []);

        $this->assertTrue(is_object($object));

        $this->getTranslateApiFactory()->getApi();
    }

    public function testIfGetApiWorksForValidCustomEnv()
    {
        unset($_SERVER['YANDEX_TRANSLATE_API_KEY']);
        unset($_ENV['YANDEX_TRANSLATE_API_KEY']);
        putenv('YANDEX_TRANSLATE_API_KEY');
        $_SERVER['PHPOLYGLOT_TEST_SERVER_KEY'] = 'test';

        $this->setInternalProperty(ApiFactoryAbstract::class, 'config', null);
        $this->setInternalProperty(ApiFactoryAbstract::class, 'envIsSet', false);

        $object = new PHPolyglot(null, [
            'YANDEX_TRANSLATE_API_KEY' => 'YANDEX_TRANSLATE_API_TEST_KEY',
            'YANDEX_DICTIONARY_API_KEY' => 'overriden',
            'PHPOLYGLOT_TEST_SERVER_KEY' => 'overriden'
        ]);

        $this->assertTrue(is_object($object));

        $api = $this->getTranslateApiFactory()->getApi();
        $this->assertInstanceOf(TranslateApiInterface::class, $api);
        $this->assertEquals('YANDEX_TRANSLATE_API_TEST_KEY', getenv('YANDEX_TRANSLATE_API_KEY'));
        $this->assertEquals('YANDEX_DICTIONARY_TEST_KEY', getenv('YANDEX_DICTIONARY_API_KEY'));
        $this->assertEquals('test', $_SERVER['PHPOLYGLOT_TEST_SERVER_KEY']);
    }

    public function testIfGetApiThrowsExceptionForBadConfig()
    {
        $this->setInternalProperty(ApiFactoryAbstract::class, 'config', null);
        $this->setInternalProperty(ApiFactoryAbstract::class, 'envIsSet', false);

        $this->expectException(InvalidConfigException::class);

        $object = new PHPolyglot([]);

        $this->assertTrue(is_object($object));

        $this->getTranslateApiFactory()->getApi();
    }

    public function testIfGetApiWorksForValidCustomConfig()
    {
        $this->setInternalProperty(ApiFactoryAbstract::class, 'config', null);
        $this->setInternalProperty(ApiFactoryAbstract::class, 'envIsSet', false);

        $object = new PHPolyglot(
            [
                'translateApi' => [
                    'default' => YandexTranslateApi::class
                ]
            ]
        );

        $this->assertTrue(is_object($object));

        $api = $this->getTranslateApiFactory()->getApi();

        $this->assertInstanceOf(TranslateApiInterface::class, $api);
    }

    /**
     * @dataProvider getValidTranslateResponses
     *
     * @param ResponseContextAbstract $context
     * @param string          $translation
     */
    public function testIfTranslateWorksCorrectlyForValidInput(
        ResponseContextAbstract $context,
        string $translation
    ) {
        $this->setInternalProperty(ApiFactoryAbstract::class, 'config', null);
        $this->setInternalProperty(ApiFactoryAbstract::class, 'envIsSet', false);

        $translateApi = $this->getApiFactoryWithMockedHttpClient(
            $context,
            $this->getTranslateApiFactory()->getApi()
        );

        /** @var PHPolyglot $phpolyglot */
        $phpolyglot = $this->getMockedPhpolyglot('getTranslateApi', $translateApi);

        $response = $phpolyglot->translate('Прывітанне, Свет!', 'en');

        $this->assertEquals($translation, $response->getTranslations()[0]);
    }

    /**
     * @dataProvider getValidTranslateBulkResponses
     *
     * @param ResponseContextAbstract $context
     * @param array                   $translations
     */
    public function testIfTranslateBulkWorksCorrectlyForValidInput(
        ResponseContextAbstract $context,
        array $translations
    ) {
        $translateApi = $this->getApiFactoryWithMockedHttpClient(
            $context,
            $this->getTranslateApiFactory()->getApi()
        );

        /** @var PHPolyglot $phpolyglot */
        $phpolyglot = $this->getMockedPhpolyglot('getTranslateApi', $translateApi);

        $response = $phpolyglot->translateBulk(['Прывітанне', 'Свет'], 'en', 'be');

        $this->assertEquals($translations, $response->getTranslations());
    }

    public function testIfExceptionIsThrownForInvalidLanguages()
    {
        $this->expectException(InvalidLanguageCodeException::class);

        /** @var PHPolyglot $phpolyglot */
        $phpolyglot = new PHPolyglot();

        $phpolyglot->translateBulk(['Прывітанне', 'Свет'], 'eng', 'bel');
    }

    public function testIfTextAlternativesLookupWorksCorrectlyForValidInput()
    {
        $translateApi = $this->getApiFactoryWithMockedHttpClient(
            $this->getValidTextAlternativesResponse(),
            $this->getDictionaryApiFactory()->getApi()
        );

        /** @var PHPolyglot $phpolyglot */
        $phpolyglot = $this->getMockedPhpolyglot('getDictionaryApi', $translateApi);

        $response = $phpolyglot->lookup('World', 'en');

        $entries = $response->getEntries();

        $this->assertCount(3, $entries);
        $this->assertEquals('mankind', $entries[1]->getTextTo());
        $this->assertEquals('wɜːld', $entries[2]->getTranscription());
        $this->assertCount(4, $entries[0]->getSynonyms());
        $this->assertEquals(DictionaryEntryPos::POS_NOUN, $entries[0]->getPosFrom());
    }

    public function testIfTranslateAlternativesLookupWorksCorrectlyForValidInput()
    {
        $translateApi = $this->getApiFactoryWithMockedHttpClient(
            $this->getValidTranslateAlternativesResponse(),
            $this->getDictionaryApiFactory()->getApi()
        );

        /** @var PHPolyglot $phpolyglot */
        $phpolyglot = $this->getMockedPhpolyglot('getDictionaryApi', $translateApi);

        $response = $phpolyglot->lookup('World', 'en', 'ru');

        $entries = $response->getEntries();

        $this->assertCount(9, $entries);
        $this->assertEquals('здравствуйте', $entries[3]->getTextTo());
        $this->assertEquals('ˈheˈləʊ', $entries[0]->getTranscription());
        $this->assertCount(2, $entries[0]->getSynonyms());
        $this->assertCount(2, $entries[0]->getMeanings());
    }

    public function testIfSpeakWorksCorrectlyForValidInput()
    {
        $ttsApi = $this->getApiFactoryWithMockedHttpClient(
            $this->getValidTtsResponse(),
            $this->getTtsApiFactory()->getApi()
        );

        /** @var PHPolyglot $phpolyglot */
        $phpolyglot = $this->getMockedPhpolyglot('getTtsApi', $ttsApi);

        $response = $phpolyglot->speak('Hello world', 'en');

        $directory = TEST_ROOT . DIRECTORY_SEPARATOR . 'media_test';
        $file = $response->storeFile('', '', $directory);

        $this->assertEquals(md5('hello world').".ogg", $file);
        $this->assertEquals(
            file_get_contents(TEST_ROOT . DIRECTORY_SEPARATOR . 'configs' . DIRECTORY_SEPARATOR . 'audio.ogg'),
            file_get_contents($directory . DIRECTORY_SEPARATOR . $file)
        );
    }

    public function testIfSpellCheckWorksCorrectlyForValidInput()
    {
        $spellCheckApi = $this->getApiFactoryWithMockedHttpClient(
            $this->getValidSpellCheckResponse(),
            $this->getSpellCheckApiFactory()->getApi()
        );

        /** @var PHPolyglot $phpolyglot */
        $phpolyglot = $this->getMockedPhpolyglot('getSpellCheckApi', $spellCheckApi);

        $response = $phpolyglot->spellCheck('Hello world', 'en');

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

        $response = $phpolyglot->spellCheckBulk(['Hello world'], 'en');

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

    /**
     * @return array
     */
    public function getValidTranslateResponses(): array
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
                'Hello World!'
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
                'Hello World!'
            ]
        ];
    }

    /**
     * @return array
     */
    public function getValidTranslateBulkResponses(): array
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
                ['Hello', 'World']
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
                ['Hello', 'World']
            ]
        ];
    }

    /**
     * @return JsonResponseContext
     */
    public function getValidTextAlternativesResponse(): ResponseContextAbstract
    {
        return  (
            new JsonResponseContext('{
                "head": {},
                "def": [
                    {
                        "text": "world",
                        "pos": "noun",
                        "ts": "wɜːld",
                        "tr": [
                            {
                                "text": "globe",
                                "pos": "noun",
                                "syn": [
                                    {
                                        "text": "planet",
                                        "pos": "noun"
                                    },
                                    {
                                        "text": "earth",
                                        "pos": "noun"
                                    },
                                    {
                                        "text": "universe",
                                        "pos": "noun"
                                    },
                                    {
                                        "text": "light",
                                        "pos": "noun"
                                    }
                                ]
                            },
                            {
                                "text": "mankind",
                                "pos": "noun",
                                "syn": [
                                    {
                                        "text": "humanity",
                                        "pos": "noun"
                                    }
                                ]
                            },
                            {
                                "text": "peace",
                                "pos": "noun",
                                "syn": [
                                    {
                                        "text": "international",
                                        "pos": "noun"
                                    },
                                    {
                                        "text": "universal",
                                        "pos": "noun"
                                    },
                                    {
                                        "text": "pax",
                                        "pos": "noun"
                                    }
                                ]
                            }
                        ]
                    }
                ]
            }')
        )->setHttpStatusCode(200);
    }

    /**
     * @return ResponseContextAbstract
     */
    public function getValidTranslateAlternativesResponse(): ResponseContextAbstract
    {
        return  (
            new JsonResponseContext('{
                "head": {},
                "def": [
                    {
                        "text": "hello",
                        "pos": "noun",
                        "ts": "ˈheˈləʊ",
                        "tr": [
                            {
                                "text": "привет",
                                "pos": "noun",
                                "syn": [
                                    {
                                        "text": "добрый день",
                                        "pos": "noun"
                                    },
                                    {
                                        "text": "Здравствуй",
                                        "pos": "noun",
                                        "gen": "м"
                                    }
                                ],
                                "mean": [
                                    {
                                        "text": "hi"
                                    },
                                    {
                                        "text": "good afternoon"
                                    }
                                ],
                                "ex": [
                                    {
                                        "text": "big hello",
                                        "tr": [
                                            {
                                                "text": "большой привет"
                                            }
                                        ]
                                    }
                                ]
                            },
                            {
                                "text": "приветствие",
                                "pos": "noun",
                                "gen": "ср"
                            },
                            {
                                "text": "Хэлло",
                                "pos": "noun",
                                "gen": "ср"
                            }
                        ]
                    },
                    {
                        "text": "hello",
                        "pos": "verb",
                        "ts": "ˈheˈləʊ",
                        "tr": [
                            {
                                "text": "здравствуйте",
                                "pos": "verb",
                                "asp": "несов",
                                "mean": [
                                    {
                                        "text": "hi"
                                    }
                                ]
                            },
                            {
                                "text": "поздороваться",
                                "pos": "verb",
                                "asp": "сов",
                                "mean": [
                                    {
                                        "text": "greet"
                                    }
                                ]
                            },
                            {
                                "text": "приветствовать",
                                "pos": "verb",
                                "asp": "несов"
                            }
                        ]
                    },
                    {
                        "text": "hello",
                        "pos": "interjection",
                        "ts": "ˈheˈləʊ",
                        "tr": [
                            {
                                "text": "АЛЛО",
                                "pos": "interjection"
                            },
                            {
                                "text": "ау",
                                "pos": "interjection"
                            }
                        ]
                    },
                    {
                        "text": "hello",
                        "pos": "adverb",
                        "ts": "ˈheˈləʊ",
                        "tr": [
                            {
                                "text": "здорово",
                                "pos": "adverb",
                                "mean": [
                                    {
                                        "text": "hey"
                                    }
                                ]
                            }
                        ]
                    }
                ]
            }')
        )->setHttpStatusCode(200);
    }

    private function getValidSpellCheckResponse(): ResponseContextAbstract
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
     * @return ResponseContextAbstract
     */
    public function getValidTtsResponse(): ResponseContextAbstract
    {
        $requestContext = (new RequestContext())
            ->setData(json_encode(['text' => 'hello world']));

        $fileContents = file_get_contents(TEST_ROOT . DIRECTORY_SEPARATOR . 'configs' . DIRECTORY_SEPARATOR . 'audio.ogg');

        $responseContext = (new DummyResponseContext($fileContents))->setHttpStatusCode(200);
        $responseContext->headers()->setHeadersFromString(
            "
                Connection: Keep-Alive
                Content-Disposition: inline; filename=\"result.ogg\"
                Content-Type: audio/ogg; codecs=opus
                Date: Fri, 26 Jan 2018 18:32:09 GMT
                Server: -
                Session-Name: WESRPCEYYYLEEULR-en-US_MichaelVoice
                Strict-Transport-Security: max-age=31536000;
                Transfer-Encoding: chunked
                Via: 1.1 f72ecb6, 1.1 71c3449, HTTP/1.1 e82057a
                X-Backside-Transport: OK OK
                X-Content-Type-Options: nosniff
                X-DP-Watson-Tran-ID: stream01-665565077
                X-Global-Transaction-ID: f257b1145a6b742927abb795
                X-XSS-Protection: 1
            "
        );

        $responseContext->setRequestContext($requestContext);

        return $responseContext;
    }

    /**
     * @param string    $method
     * @param mixed     $returnValue
     *
     * @return \PHPUnit\Framework\MockObject\MockObject
     */
    private function getMockedPhpolyglot(string $method, $returnValue)
    {
        $phpolyglot = $this->getMockBuilder(PHPolyglot::class)
            ->setMethods(array($method))
            ->getMock();

        $phpolyglot->method($method)->willReturn($returnValue);

        return $phpolyglot;
    }

    /**
     * @param ResponseContextAbstract $context
     * @param                 $apiFactory
     *
     * @return mixed
     */
    private function getApiFactoryWithMockedHttpClient(
        ResponseContextAbstract $context,
        ApiAbstract $apiFactory
    ) {
        $nanoRest = $this->getMockBuilder(NanoRest::class)
            ->setMethods(array('sendRequest'))
            ->getMock();

        $nanoRest->method('sendRequest')->willReturn($context);

        $this->setInternalProperty($apiFactory, 'httpClient', $nanoRest);

        return $apiFactory;
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
     * Get stubbed version of DictionaryApiFactory
     *
     * @param array $methods
     *
     * @return DictionaryApiFactory|\PHPUnit\Framework\MockObject\MockObject
     */
    private function getDictionaryApiFactory(array $methods = [])
    {
        $stub = $this->getMockBuilder(DictionaryApiFactory::class)
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
     * Get stubbed version of TtsApiFactory
     *
     * @param array $methods
     *
     * @return TtsApiFactory|\PHPUnit\Framework\MockObject\MockObject
     */
    private function getTtsApiFactory(array $methods = [])
    {
        $stub = $this->getMockBuilder(TtsApiFactory::class)
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
     * Get stubbed version of SpellCheckApiFactory
     *
     * @return SpellCheckApiFactory|\PHPUnit\Framework\MockObject\MockObject
     */
    private function getSpellCheckApiFactory()
    {
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
