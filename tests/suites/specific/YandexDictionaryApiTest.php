<?php

namespace GinoPane\PHPolyglot;

use GinoPane\NanoRest\NanoRest;
use GinoPane\NanoRest\Request\RequestContext;
use GinoPane\NanoRest\Response\ResponseContext;
use GinoPane\NanoRest\Response\JsonResponseContext;
use GinoPane\PHPolyglot\API\Response\Dictionary\DictionaryResponse;
use GinoPane\PHPolyglot\Exception\InvalidConfigException;
use GinoPane\PHPolyglot\Exception\InvalidPropertyException;
use GinoPane\PHPolyglot\Exception\InvalidEnvironmentException;
use GinoPane\PHPolyglot\Exception\MethodDoesNotExistException;
use GinoPane\PHPolyglot\API\Response\Translate\TranslateResponse;
use GinoPane\PHPolyglot\API\Factory\Dictionary\DictionaryApiFactory;
use GinoPane\PHPolyglot\API\Implementation\Dictionary\DictionaryApiInterface;
use GinoPane\PHPolyglot\API\Implementation\Dictionary\Yandex\YandexDictionaryApi;

/**
*  Corresponding class to test YandexDictionaryApiTest class
*
*  @author Sergey <Gino Pane> Karavay
*/
class YandexDictionaryApiTest extends PHPolyglotTestCase
{
    public function testIfDictionaryApiCanBeCreatedByFactory()
    {
        $this->setInternalProperty(DictionaryApiFactory::class, 'config', null);

        $translateApi = $this->getDictionaryApiFactory()->getApi();

        $this->assertTrue($translateApi instanceof DictionaryApiInterface);
    }

    public function testIfDictionaryApiThrowsExceptionWhenPropertyDoesNotExist()
    {
        $this->expectException(InvalidPropertyException::class);

        $stub = $this->getMockBuilder(YandexDictionaryApi::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->setInternalProperty($stub, 'envProperties', ['apiKeys' => 'YANDEX_DICTIONARY_API_KEY']);

        $stub->__construct();
    }

    public function testIfDictionaryApiThrowsExceptionWhenEnvVariableDoesNotExist()
    {
        $this->expectException(InvalidEnvironmentException::class);

        $stub = $this->getMockBuilder(YandexDictionaryApi::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->setInternalProperty($stub, 'envProperties', ['apiKey' => 'WRONG_VARIABLE']);

        $stub->__construct();
    }

    public function testIfDictionaryApiThrowsExceptionForNonExistentMethod()
    {
        $this->expectException(MethodDoesNotExistException::class);
        $this->expectExceptionMessage('Specified method "createWrongMethodContext" does not exist');

        $dictionaryApi = $this->getDictionaryApiFactory()->getApi();

        $callApiMethod = $this->getInternalMethod($dictionaryApi, 'callApi');

        $callApiMethod->invoke($dictionaryApi, 'wrongMethod', []);
    }

    public function testIfDictionaryApiCreatesValidGetTextAlternativesContext()
    {
        $dictionaryApi = $this->getDictionaryApiFactory()->getApi();

        $createContextMethod = $this->getInternalMethod($dictionaryApi, 'createGetTextAlternativesContext');

        $text = 'Hello';
        /** @var RequestContext $context */
        $context = $createContextMethod->invoke($dictionaryApi, $text, 'en');

        $this->assertTrue($context instanceof RequestContext);
        $this->assertEquals(
            'https://dictionary.yandex.net/api/v1/dicservice.json/lookup?lang=en-en&flags=4&ui=en&key=YANDEX_DICTIONARY_TEST_KEY',
            $context->getRequestUrl()
        );
        $this->assertEquals('text=' . urlencode($text), $context->getRequestData());
    }

    public function testIfDictionaryApiCreatesValidGetTranslateAlternativesContext()
    {
        $dictionaryApi = $this->getDictionaryApiFactory()->getApi();

        $createRequestMethod = $this->getInternalMethod($dictionaryApi, 'createGetTranslateAlternativesContext');

        $text = 'Hello';
        /** @var RequestContext $context */
        $context = $createRequestMethod->invoke($dictionaryApi, $text, 'ru', 'en');

        $this->assertTrue($context instanceof RequestContext);
        $this->assertEquals(
            'https://dictionary.yandex.net/api/v1/dicservice.json/lookup?lang=en-ru&flags=4&ui=en&key=YANDEX_DICTIONARY_TEST_KEY',
            $context->getRequestUrl()
        );

        $this->assertEquals('text=' . urlencode($text), $context->getRequestData());
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

        $dictionaryApi = $this->getDictionaryApiFactory()->getApi();

        $this->setInternalProperty($dictionaryApi, 'httpClient', $nanoRest);

        $callApiMethod = $this->getInternalMethod($dictionaryApi, 'callApi');

        $callApiMethod->invoke($dictionaryApi, 'getTextAlternatives', ['world','en']);
    }

    /**
     * @dataProvider getValidResponsesForTextLookupProcessing
     *
     * @param ResponseContext $context
     * @param array           $expectedResult
     *
     * @throws InvalidConfigException
     */
    public function testIfValidTextLookupResponseCanBeProcessed(
        ResponseContext $context,
        array $expectedResult
    ) {
        $nanoRest = $this->getMockBuilder(NanoRest::class)
            ->setMethods(array('sendRequest'))
            ->getMock();

        $nanoRest->method('sendRequest')->willReturn($context);

        $dictionaryApi = $this->getDictionaryApiFactory()->getApi();

        $this->setInternalProperty($dictionaryApi, 'httpClient', $nanoRest);

        /** @var DictionaryResponse $response */
        $response = $dictionaryApi->getTextAlternatives('', '');

        $this->assertTrue($response instanceof DictionaryResponse);
    }

    /**
     * @dataProvider getValidResponsesForTextTranslateLookupProcessing
     *
     * @param ResponseContext $context
     * @param array           $result
     *
     * @throws InvalidConfigException
     */
    public function testIfValidTextTranslateLookupResponseCanBeProcessed(
        ResponseContext $context,
        array $result
    ) {
        $nanoRest = $this->getMockBuilder(NanoRest::class)
            ->setMethods(array('sendRequest'))
            ->getMock();

        $nanoRest->method('sendRequest')->willReturn($context);

        $translateApi = $this->getDictionaryApiFactory()->getApi();

        $this->setInternalProperty($translateApi, 'httpClient', $nanoRest);

        /** @var DictionaryResponse $response */
        $response = $translateApi->getTranslateAlternatives('', '', '');

        $this->assertTrue($response instanceof DictionaryResponse);
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
                (new JsonResponseContext())->setHttpStatusCode(405),
                'Method Not Allowed',
                405
            ],
            [
                (new JsonResponseContext('{
                    "code": 200
                }'))->setHttpStatusCode(200),
                'There is no required field "def" in response',
                0
            ],
        ];
    }

    /**
     * @return array
     */
    public function getValidResponsesForTextLookupProcessing(): array
    {
        return [
            [
                (
                    new JsonResponseContext('{
                        "head": {},
                        "def": [
                            {
                                "text": "go",
                                "pos": "verb",
                                "ts": "gəʊ",
                                "fl": "went, gone",
                                "tr": [
                                    {
                                        "text": "get",
                                        "pos": "verb",
                                        "syn": [
                                            {
                                                "text": "move",
                                                "pos": "verb"
                                            },
                                            {
                                                "text": "take",
                                                "pos": "verb"
                                            },
                                            {
                                                "text": "turn",
                                                "pos": "verb"
                                            },
                                            {
                                                "text": "pass",
                                                "pos": "verb"
                                            },
                                            {
                                                "text": "become",
                                                "pos": "verb"
                                            },
                                            {
                                                "text": "fly",
                                                "pos": "verb"
                                            },
                                            {
                                                "text": "go up",
                                                "pos": "verb"
                                            },
                                            {
                                                "text": "go through",
                                                "pos": "verb"
                                            },
                                            {
                                                "text": "jump",
                                                "pos": "verb"
                                            },
                                            {
                                                "text": "get out",
                                                "pos": "verb"
                                            },
                                            {
                                                "text": "go for",
                                                "pos": "verb"
                                            },
                                            {
                                                "text": "click",
                                                "pos": "verb"
                                            },
                                            {
                                                "text": "attend",
                                                "pos": "verb"
                                            },
                                            {
                                                "text": "last",
                                                "pos": "verb"
                                            },
                                            {
                                                "text": "stand",
                                                "pos": "verb"
                                            },
                                            {
                                                "text": "agree",
                                                "pos": "verb"
                                            },
                                            {
                                                "text": "navigate",
                                                "pos": "verb"
                                            },
                                            {
                                                "text": "lead",
                                                "pos": "verb"
                                            },
                                            {
                                                "text": "refer",
                                                "pos": "verb"
                                            },
                                            {
                                                "text": "take place",
                                                "pos": "verb"
                                            },
                                            {
                                                "text": "grow",
                                                "pos": "verb"
                                            },
                                            {
                                                "text": "get through",
                                                "pos": "verb"
                                            },
                                            {
                                                "text": "pass on",
                                                "pos": "verb"
                                            },
                                            {
                                                "text": "fetch",
                                                "pos": "verb"
                                            },
                                            {
                                                "text": "pass over",
                                                "pos": "verb"
                                            },
                                            {
                                                "text": "pass away",
                                                "pos": "verb"
                                            },
                                            {
                                                "text": "elapse",
                                                "pos": "verb"
                                            }
                                        ]
                                    },
                                    {
                                        "pos": "verb",
                                        "syn": [
                                            {
                                                "text": "come out",
                                                "pos": "verb"
                                            },
                                            {
                                                "text": "look out",
                                                "pos": "verb"
                                            },
                                            {
                                                "text": "step out",
                                                "pos": "verb"
                                            },
                                            {
                                                "text": "emerge",
                                                "pos": "verb"
                                            },
                                            {
                                                "text": "come off",
                                                "pos": "verb"
                                            }
                                        ]
                                    },
                                    {
                                        "text": "run",
                                        "pos": "verb",
                                        "syn": [
                                            {
                                                "text": "start",
                                                "pos": "verb"
                                            },
                                            {
                                                "text": "try",
                                                "pos": "verb"
                                            },
                                            {
                                                "text": "walk",
                                                "pos": "verb"
                                            },
                                            {
                                                "text": "flee",
                                                "pos": "verb"
                                            }
                                        ]
                                    },
                                    {
                                        "text": "proceed",
                                        "pos": "verb",
                                        "syn": [
                                            {
                                                "text": "continue",
                                                "pos": "verb"
                                            },
                                            {
                                                "text": "keep on",
                                                "pos": "verb"
                                            }
                                        ]
                                    },
                                    {
                                        "text": "go away",
                                        "pos": "verb",
                                        "syn": [
                                            {
                                                "text": "depart",
                                                "pos": "verb"
                                            },
                                            {
                                                "text": "leave",
                                                "pos": "verb"
                                            },
                                            {
                                                "text": "quit",
                                                "pos": "verb"
                                            },
                                            {
                                                "text": "exit",
                                                "pos": "verb"
                                            },
                                            {
                                                "text": "retire",
                                                "pos": "verb"
                                            },
                                            {
                                                "text": "be off",
                                                "pos": "verb"
                                            },
                                            {
                                                "text": "come away",
                                                "pos": "verb"
                                            }
                                        ]
                                    },
                                    {
                                        "text": "travel",
                                        "pos": "noun",
                                        "syn": [
                                            {
                                                "text": "head",
                                                "pos": "noun"
                                            },
                                            {
                                                "text": "cross",
                                                "pos": "noun"
                                            },
                                            {
                                                "text": "course",
                                                "pos": "noun"
                                            }
                                        ]
                                    },
                                    {
                                        "text": "go over",
                                        "pos": "verb",
                                        "syn": [
                                            {
                                                "text": "come along",
                                                "pos": "verb"
                                            },
                                            {
                                                "text": "come over",
                                                "pos": "verb"
                                            }
                                        ]
                                    },
                                    {
                                        "text": "drive",
                                        "pos": "noun",
                                        "syn": [
                                            {
                                                "text": "get going",
                                                "pos": "noun"
                                            }
                                        ]
                                    },
                                    {
                                        "text": "fall",
                                        "pos": "verb",
                                        "syn": [
                                            {
                                                "text": "descend",
                                                "pos": "verb"
                                            },
                                            {
                                                "text": "drop",
                                                "pos": "verb"
                                            },
                                            {
                                                "text": "drop in",
                                                "pos": "verb"
                                            }
                                        ]
                                    },
                                    {
                                        "text": "set off",
                                        "pos": "verb",
                                        "syn": [
                                            {
                                                "text": "set",
                                                "pos": "verb"
                                            },
                                            {
                                                "text": "set out",
                                                "pos": "verb"
                                            }
                                        ]
                                    },
                                    {
                                        "text": "lie",
                                        "pos": "verb",
                                        "syn": [
                                            {
                                                "text": "lay",
                                                "pos": "verb"
                                            }
                                        ]
                                    },
                                    {
                                        "text": "fit",
                                        "pos": "verb",
                                        "syn": [
                                            {
                                                "text": "wear",
                                                "pos": "verb"
                                            }
                                        ]
                                    },
                                    {
                                        "text": "stop by",
                                        "pos": "verb",
                                        "syn": [
                                            {
                                                "text": "stop in",
                                                "pos": "verb"
                                            }
                                        ]
                                    },
                                    {
                                        "text": "fade",
                                        "pos": "verb",
                                        "syn": [
                                            {
                                                "text": "vanish",
                                                "pos": "verb"
                                            }
                                        ]
                                    },
                                    {
                                        "text": "suit",
                                        "pos": "noun"
                                    },
                                    {
                                        "text": "sink",
                                        "pos": "noun"
                                    }
                                ]
                            }
                        ]
                    }')
                )->setHttpStatusCode(200),
                ['Hello World!']
            ],
            [
                (
                    new JsonResponseContext('{
                        "head": {},
                        "def": []
                    }')
                )->setHttpStatusCode(200),
                ['Hello World!']
            ]
        ];
    }

    /**
     * @return array
     */
    public function getValidResponsesForTextTranslateLookupProcessing(): array
    {
        return [
            [
                (
                    new JsonResponseContext('{
                        "head": {},
                        "def": [
                            {
                                "text": "go",
                                "pos": "v",
                                "ts": "gəʊ",
                                "fl": "went, gone",
                                "tr": [
                                    {
                                        "text": "идти",
                                        "pos": "гл",
                                        "asp": "несов",
                                        "syn": [
                                            {
                                                "text": "пойти",
                                                "pos": "гл",
                                                "asp": "сов"
                                            },
                                            {
                                                "text": "собираться",
                                                "pos": "гл",
                                                "asp": "несов"
                                            },
                                            {
                                                "text": "ходить",
                                                "pos": "гл",
                                                "asp": "несов"
                                            },
                                            {
                                                "text": "поехать",
                                                "pos": "гл",
                                                "asp": "сов"
                                            },
                                            {
                                                "text": "выйти",
                                                "pos": "гл",
                                                "asp": "сов"
                                            },
                                            {
                                                "text": "выходить",
                                                "pos": "гл"
                                            },
                                            {
                                                "text": "зайти",
                                                "pos": "гл",
                                                "asp": "сов"
                                            },
                                            {
                                                "text": "заходить",
                                                "pos": "гл"
                                            },
                                            {
                                                "text": "сходить",
                                                "pos": "гл"
                                            }
                                        ],
                                        "mean": [
                                            {
                                                "text": "come"
                                            },
                                            {
                                                "text": "walk"
                                            },
                                            {
                                                "text": "gather"
                                            },
                                            {
                                                "text": "come down"
                                            }
                                        ],
                                        "ex": [
                                            {
                                                "text": "go right ahead",
                                                "tr": [
                                                    {
                                                        "text": "идти прямо вперед"
                                                    }
                                                ]
                                            },
                                            {
                                                "text": "go south",
                                                "tr": [
                                                    {
                                                        "text": "пойти на юг"
                                                    }
                                                ]
                                            },
                                            {
                                                "text": "go barefoot",
                                                "tr": [
                                                    {
                                                        "text": "ходить босиком"
                                                    }
                                                ]
                                            },
                                            {
                                                "text": "go by train",
                                                "tr": [
                                                    {
                                                        "text": "поехать поездом"
                                                    }
                                                ]
                                            },
                                            {
                                                "text": "go ashore",
                                                "tr": [
                                                    {
                                                        "text": "сходить на берег"
                                                    }
                                                ]
                                            }
                                        ]
                                    },
                                    {
                                        "text": "проходить",
                                        "pos": "гл",
                                        "syn": [
                                            {
                                                "text": "пройти",
                                                "pos": "гл",
                                                "asp": "сов"
                                            }
                                        ],
                                        "mean": [
                                            {
                                                "text": "be"
                                            },
                                            {
                                                "text": "pass"
                                            }
                                        ],
                                        "ex": [
                                            {
                                                "text": "go through walls",
                                                "tr": [
                                                    {
                                                        "text": "проходить сквозь стены"
                                                    }
                                                ]
                                            },
                                            {
                                                "text": "go well",
                                                "tr": [
                                                    {
                                                        "text": "пройти успешно"
                                                    }
                                                ]
                                            }
                                        ]
                                    },
                                    {
                                        "text": "ехать",
                                        "pos": "гл",
                                        "asp": "несов",
                                        "syn": [
                                            {
                                                "text": "ездить",
                                                "pos": "гл",
                                                "asp": "несов"
                                            }
                                        ],
                                        "mean": [
                                            {
                                                "text": "ride"
                                            },
                                            {
                                                "text": "travel"
                                            }
                                        ],
                                        "ex": [
                                            {
                                                "text": "go anywhere",
                                                "tr": [
                                                    {
                                                        "text": "ехать никуда"
                                                    }
                                                ]
                                            },
                                            {
                                                "text": "go everywhere",
                                                "tr": [
                                                    {
                                                        "text": "ездить везде"
                                                    }
                                                ]
                                            }
                                        ]
                                    },
                                    {
                                        "text": "отправиться",
                                        "pos": "гл",
                                        "asp": "сов",
                                        "syn": [
                                            {
                                                "text": "уходить",
                                                "pos": "гл"
                                            },
                                            {
                                                "text": "уйти",
                                                "pos": "гл",
                                                "asp": "сов"
                                            },
                                            {
                                                "text": "отправляться",
                                                "pos": "гл",
                                                "asp": "несов"
                                            }
                                        ],
                                        "mean": [
                                            {
                                                "text": "leave"
                                            }
                                        ],
                                        "ex": [
                                            {
                                                "text": "go on leave",
                                                "tr": [
                                                    {
                                                        "text": "уходить в отпуск"
                                                    }
                                                ]
                                            },
                                            {
                                                "text": "go quietly",
                                                "tr": [
                                                    {
                                                        "text": "уйти спокойно"
                                                    }
                                                ]
                                            }
                                        ]
                                    },
                                    {
                                        "text": "перейти",
                                        "pos": "гл",
                                        "asp": "сов",
                                        "mean": [
                                            {
                                                "text": "move"
                                            }
                                        ]
                                    },
                                    {
                                        "text": "происходить",
                                        "pos": "гл",
                                        "asp": "несов",
                                        "mean": [
                                            {
                                                "text": "happen"
                                            }
                                        ]
                                    }
                                ]
                            }
                        ]
                    }')
                )->setHttpStatusCode(200),
                ['Hello', 'World']
            ],
            [
                (
                    new JsonResponseContext('{
                        "head": {},
                        "def": [
                            {
                                "text": "infeasible",
                                "pos": "adjective",
                                "ts": "ɪnˈfiːzəbl",
                                "tr": [
                                    {
                                        "text": "невыполнимый",
                                        "pos": "adjective",
                                        "mean": [
                                            {
                                                "text": "impracticable"
                                            }
                                        ]
                                    }
                                ]
                            }
                        ]
                    }')
                )->setHttpStatusCode(200),
                ['Hello', 'World']
            ],
            [
                (
                new JsonResponseContext('{
                        "head": {},
                        "def": []
                    }')
                )->setHttpStatusCode(200),
                ['Hello', 'World']
            ],
            [
                (
                    new JsonResponseContext('{
                        "head": {},
                        "def": [
                            {
                                "text": "home",
                                "pos": "adjective",
                                "ts": "həʊm",
                                "tr": [
                                    {
                                        "text": "домашний",
                                        "pos": "adjective",
                                        "syn": [
                                            {
                                                "pos": "adjective"
                                            }
                                        ],
                                        "mean": [
                                            {
                                                "texts": "household"
                                            }
                                        ],
                                        "ex": [
                                            {
                                                "tr": [
                                                    {
                                                        "text": "домашний телефон"
                                                    }
                                                ]
                                            },
                                            {
                                                "text": "home computer network",
                                                "tr": [
                                                    {
                                                        "texts": "домашняя компьютерная сеть"
                                                    }
                                                ]
                                            },
                                            {
                                                "text": "home theater system",
                                                "tr": [
                                                    {
                                                        "text": "домашний кинотеатр"
                                                    }
                                                ]
                                            },
                                            {
                                                "text": "home appliances",
                                                "tr": [
                                                    {
                                                        "text": "бытовая техника"
                                                    }
                                                ]
                                            }
                                        ]
                                    },
                                    {
                                        "text": "внутренний",
                                        "pos": "adjective",
                                        "syn": [
                                            {
                                                "text": "отечественный",
                                                "pos": "adjective"
                                            }
                                        ],
                                        "mean": [
                                            {
                                                "text": "internal"
                                            },
                                            {
                                                "text": "domestic"
                                            }
                                        ],
                                        "ex": [
                                            {
                                                "text": "minister for home affairs",
                                                "tr": [
                                                    {
                                                        "text": "министр внутренних дел"
                                                    }
                                                ]
                                            },
                                            {
                                                "text": "home producer",
                                                "tr": [
                                                    {
                                                        "text": "отечественный производитель"
                                                    }
                                                ]
                                            }
                                        ]
                                    },
                                    {
                                        "text": "родной",
                                        "pos": "adjective",
                                        "mean": [
                                            {
                                                "text": "native"
                                            }
                                        ],
                                        "ex": [
                                            {
                                                "text": "home city",
                                                "tr": [
                                                    {
                                                        "text": "родной город"
                                                    }
                                                ]
                                            }
                                        ]
                                    },
                                    {
                                        "text": "главный",
                                        "pos": "adjective",
                                        "mean": [
                                            {
                                                "text": "main"
                                            }
                                        ],
                                        "ex": [
                                            {
                                                "text": "home office",
                                                "tr": [
                                                    {
                                                        "text": "главный офис"
                                                    }
                                                ]
                                            }
                                        ]
                                    }
                                ]
                            },
                            {
                                "text": "home",
                                "pos": "noun",
                                "ts": "həʊm",
                                "tr": [
                                    {
                                        "text": "родина",
                                        "pos": "noun",
                                        "gen": "ж",
                                        "mean": [
                                            {
                                                "text": "homeland"
                                            }
                                        ],
                                        "ex": [
                                            {
                                                "text": "return home",
                                                "tr": [
                                                    {
                                                        "text": "вернуться на родину"
                                                    }
                                                ]
                                            }
                                        ]
                                    },
                                    {
                                        "text": "жилище",
                                        "pos": "noun",
                                        "gen": "ср",
                                        "syn": [
                                            {
                                                "text": "жилье",
                                                "pos": "noun",
                                                "gen": "ср"
                                            },
                                            {
                                                "text": "проживание",
                                                "pos": "noun",
                                                "gen": "ср"
                                            }
                                        ],
                                        "mean": [
                                            {
                                                "text": "house"
                                            },
                                            {
                                                "text": "housing"
                                            },
                                            {
                                                "text": "residence"
                                            }
                                        ],
                                        "ex": [
                                            {
                                                "text": "humble homes",
                                                "tr": [
                                                    {
                                                        "text": "скромные жилища"
                                                    }
                                                ]
                                            },
                                            {
                                                "text": "permanent home",
                                                "tr": [
                                                    {
                                                        "text": "постоянное жилье"
                                                    }
                                                ]
                                            }
                                        ]
                                    },
                                    {
                                        "text": "семья",
                                        "pos": "noun",
                                        "gen": "ж",
                                        "mean": [
                                            {
                                                "text": "family"
                                            }
                                        ],
                                        "ex": [
                                            {
                                                "text": "broken home",
                                                "tr": [
                                                    {
                                                        "text": "распавшаяся семья"
                                                    }
                                                ]
                                            }
                                        ]
                                    },
                                    {
                                        "text": "домашний очаг",
                                        "pos": "noun",
                                        "syn": [
                                            {
                                                "text": "родной дом",
                                                "pos": "noun"
                                            },
                                            {
                                                "text": "родной очаг",
                                                "pos": "noun"
                                            }
                                        ],
                                        "mean": [
                                            {
                                                "text": "hearth"
                                            },
                                            {
                                                "text": "family home"
                                            }
                                        ]
                                    },
                                    {
                                        "text": "дома",
                                        "pos": "noun",
                                        "gen": "м",
                                        "mean": [
                                            {
                                                "text": "at home"
                                            }
                                        ]
                                    },
                                    {
                                        "text": "домашние условия",
                                        "pos": "noun",
                                        "mean": [
                                            {
                                                "text": "home conditions"
                                            }
                                        ]
                                    },
                                    {
                                        "text": "кров",
                                        "pos": "noun",
                                        "gen": "м",
                                        "mean": [
                                            {
                                                "text": "shelter"
                                            }
                                        ]
                                    }
                                ]
                            },
                            {
                                "text": "home",
                                "pos": "adverb",
                                "ts": "həʊm",
                                "tr": [
                                    {
                                        "text": "домой",
                                        "pos": "adverb",
                                        "mean": [
                                            {
                                                "text": "homeward"
                                            }
                                        ]
                                    },
                                    {
                                        "text": "на родину",
                                        "pos": "adverb"
                                    },
                                    {
                                        "text": "к себе",
                                        "pos": "adverb"
                                    }
                                ]
                            },
                            {
                                "text": "home",
                                "pos": "verb",
                                "ts": "həʊm",
                                "tr": [
                                    {
                                        "text": "возвращаться домой",
                                        "pos": "verb",
                                        "mean": [
                                            {
                                                "text": "return home"
                                            }
                                        ]
                                    }
                                ]
                            }
                        ]
                    }')
                )->setHttpStatusCode(200),
                ['Hello', 'World']
            ]
        ];
    }
}


