<?php

namespace GinoPane\PHPolyglot;

use GinoPane\NanoRest\NanoRest;
use GinoPane\NanoRest\Request\RequestContext;
use GinoPane\NanoRest\Response\ResponseContext;
use GinoPane\NanoRest\Response\JsonResponseContext;
use GinoPane\PHPolyglot\API\Response\Dictionary\DictionaryResponse;
use GinoPane\PHPolyglot\API\Response\Dictionary\Entry\POS\DictionaryEntryPos;
use GinoPane\PHPolyglot\Exception\InvalidConfigException;
use GinoPane\PHPolyglot\Exception\InvalidPropertyException;
use GinoPane\PHPolyglot\Exception\InvalidEnvironmentException;
use GinoPane\PHPolyglot\Exception\MethodDoesNotExistException;
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
 * @throws InvalidConfigException
 */
    public function testIfValidTextLookupResponseCanBeProcessed()
    {
        $nanoRest = $this->getMockBuilder(NanoRest::class)
            ->setMethods(array('sendRequest'))
            ->getMock();

        $nanoRest->method('sendRequest')->willReturn($this->getValidResponseForTextLookupProcessing());

        $dictionaryApi = $this->getDictionaryApiFactory()->getApi();

        $this->setInternalProperty($dictionaryApi, 'httpClient', $nanoRest);

        /** @var DictionaryResponse $response */
        $response = $dictionaryApi->getTextAlternatives('', '');

        $this->assertTrue($response instanceof DictionaryResponse);

        $entries = $response->getEntries();

        $this->assertCount(15, $entries);
        $this->assertEquals(DictionaryEntryPos::POS_NOUN, $entries[4]->getPosTo());
        $this->assertEmpty($entries[13]->getSynonyms());
        $this->assertEmpty($entries[13]->getMeanings());
        $this->assertEmpty($entries[13]->getExamples());
        $this->assertCount(7, $entries[3]->getSynonyms());
    }

    /**
     * @throws InvalidConfigException
     */
    public function testIfEmptyTextLookupResponseCanBeProcessed()
    {
        $nanoRest = $this->getMockBuilder(NanoRest::class)
            ->setMethods(array('sendRequest'))
            ->getMock();

        $nanoRest->method('sendRequest')->willReturn($this->getEmptyResponseForTextLookupProcessing());

        $dictionaryApi = $this->getDictionaryApiFactory()->getApi();

        $this->setInternalProperty($dictionaryApi, 'httpClient', $nanoRest);

        /** @var DictionaryResponse $response */
        $response = $dictionaryApi->getTextAlternatives('', '');

        $this->assertTrue($response instanceof DictionaryResponse);
        $this->assertEmpty($response->getEntries());
    }

    /**
     * @dataProvider getInvalidResponseForTextLookupProcessing
     *
     * @param ResponseContext $response
     *
     * @throws InvalidConfigException
     */
    public function testIfInvalidTextLookupResponseCanBeProcessed(ResponseContext $response)
    {
        $nanoRest = $this->getMockBuilder(NanoRest::class)
            ->setMethods(array('sendRequest'))
            ->getMock();

        $nanoRest->method('sendRequest')->willReturn($response);

        $dictionaryApi = $this->getDictionaryApiFactory()->getApi();

        $this->setInternalProperty($dictionaryApi, 'httpClient', $nanoRest);

        /** @var DictionaryResponse $response */
        $response = $dictionaryApi->getTextAlternatives('', '');

        $this->assertTrue($response instanceof DictionaryResponse);
        $this->assertEmpty($response->getEntries());
    }

    /**
     * @throws InvalidConfigException
     */
    public function testIfValidTextTranslateLookupResponseCanBeProcessed()
    {
        $nanoRest = $this->getMockBuilder(NanoRest::class)
            ->setMethods(array('sendRequest'))
            ->getMock();

        $nanoRest->method('sendRequest')->willReturn(
            $this->getValidResponseForTextTranslateLookupProcessing()
        );

        $translateApi = $this->getDictionaryApiFactory()->getApi();

        $this->setInternalProperty($translateApi, 'httpClient', $nanoRest);

        /** @var DictionaryResponse $response */
        $response = $translateApi->getTranslateAlternatives('', '', '');

        $this->assertTrue($response instanceof DictionaryResponse);

        $entries = $response->getEntries();

        $this->assertNotEmpty($entries);
        $this->assertCount(6, $entries);
        $this->assertCount(3, $entries[0]->getMeanings());
        $this->assertCount(3, $entries[0]->getExamples());
        $this->assertCount(8, $entries[0]->getSynonyms());
        $this->assertEquals("уходить в отпуск", $entries[3]->getExamples()['go on leave']);
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
     * @return ResponseContext
     */
    public function getValidResponseForTextLookupProcessing(): ResponseContext
    {
        return (
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
            )->setHttpStatusCode(200);
    }

    /**
     * @return array
     */
    public function getInvalidResponseForTextLookupProcessing(): array
    {
        return [
            [
                (
                    new JsonResponseContext('{
                            "head": {},
                            "def": [
                                {
                                    "tr": []
                                }
                            ]
                        }')
                )->setHttpStatusCode(200)
            ],
            [
                (
                    new JsonResponseContext('{
                            "head": {},
                            "def": [
                                {
                                    "text": "hello"
                                }
                            ]
                        }')
                )->setHttpStatusCode(200)
            ],
            [
                (
                    new JsonResponseContext('{
                            "head": {},
                            "def": [
                                {
                                    "text": "hello",
                                    "tr": "world"
                                }
                            ]
                        }')
                )->setHttpStatusCode(200)
            ]
        ];
    }

    public function getEmptyResponseForTextLookupProcessing(): ResponseContext
    {
        return (
            new JsonResponseContext('{
                        "head": {},
                        "def": []
                    }')
        )->setHttpStatusCode(200);
    }

    /**
     * @return ResponseContext
     */
    public function getValidResponseForTextTranslateLookupProcessing(): ResponseContext
    {
        return (
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
                                                "texts": "пойти",
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
                                                "texts": "come"
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
                                                "texts": "go right ahead",
                                                "tr": [
                                                    {
                                                        "texts": "идти прямо вперед"
                                                    }
                                                ]
                                            },
                                            {
                                                "text": "go south"
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
                )->setHttpStatusCode(200);
    }
}


