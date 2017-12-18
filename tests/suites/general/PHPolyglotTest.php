<?php

namespace GinoPane\PHPolyglot;

use GinoPane\NanoRest\NanoRest;
use GinoPane\NanoRest\Response\JsonResponseContext;
use GinoPane\NanoRest\Response\ResponseContext;
use GinoPane\PHPolyglot\API\Factory\Translate\TranslateApiFactory;
use GinoPane\PHPolyglot\API\Implementation\Translate\TranslateApiInterface;
use GinoPane\PHPolyglot\Exception\InvalidConfigException;

/**
 *  Corresponding class to test PHPolyglot class
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

    /**
     * @dataProvider getValidTranslateResponses
     *
     * @param ResponseContext $context
     * @param string          $translation
     *
     * @throws InvalidConfigException
     */
    public function testIfTranslateWorksCorrectlyForValidInput(
        ResponseContext $context,
        string $translation
    ) {
        $translateApi = $this->getApiFactoryWithMockedHttpClient(
            $context,
            $this->getTranslateApiFactory()->getApi()
        );

        /** @var PHPolyglot $phpolyglot */
        $phpolyglot = $this->getMockedPhpolyglot('getTranslateApi', $translateApi);

        $response = $phpolyglot->translate('Прывітанне, Свет!', 'en', 'be');

        $this->assertEquals($translation, $response->getTranslations()[0]);
    }

    /**
     * @dataProvider getValidTranslateBulkResponses
     *
     * @param ResponseContext $context
     * @param array           $translations
     *
     * @throws InvalidConfigException
     */
    public function testIfTranslateBulkWorksCorrectlyForValidInput(
        ResponseContext $context,
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
     * @param string                $method
     * @param TranslateApiInterface $returnValue
     *
     * @return \PHPUnit\Framework\MockObject\MockObject
     */
    private function getMockedPhpolyglot(string $method, TranslateApiInterface $returnValue)
    {
        $phpolyglot = $this->getMockBuilder(PHPolyglot::class)
            ->setMethods(array($method))
            ->getMock();

        $phpolyglot->method($method)->willReturn($returnValue);

        return $phpolyglot;
    }

    /**
     * @param ResponseContext $context
     * @param                 $apiFactory
     *
     * @return mixed
     */
    private function getApiFactoryWithMockedHttpClient(
        ResponseContext $context,
        TranslateApiInterface $apiFactory
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
}
