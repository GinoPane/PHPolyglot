<?php

namespace GinoPane\PHPolyglot;

use GinoPane\NanoRest\NanoRest;
use GinoPane\NanoRest\Request\RequestContext;
use GinoPane\NanoRest\Response\DummyResponseContext;
use GinoPane\NanoRest\Response\ResponseContext;
use GinoPane\NanoRest\Response\JsonResponseContext;
use GinoPane\PHPolyglot\API\Factory\TTS\TtsApiFactory;
use GinoPane\PHPolyglot\API\Implementation\TTS\IbmWatson\IbmWatsonTtsApi;
use GinoPane\PHPolyglot\API\Implementation\TTS\TtsApiInterface;
use GinoPane\PHPolyglot\API\Response\TTS\TtsResponse;
use GinoPane\PHPolyglot\API\Supplemental\TTS\TtsAudioFormat;
use GinoPane\PHPolyglot\Exception\InvalidAudioFormatCodeException;
use GinoPane\PHPolyglot\Exception\InvalidAudioFormatParameterException;
use GinoPane\PHPolyglot\Exception\InvalidIoException;
use GinoPane\PHPolyglot\Exception\InvalidPathException;
use GinoPane\PHPolyglot\Exception\InvalidPropertyException;
use GinoPane\PHPolyglot\Exception\InvalidEnvironmentException;
use GinoPane\PHPolyglot\API\Factory\Translate\TranslateApiFactory;
use GinoPane\PHPolyglot\API\Response\Translate\TranslateResponse;
use GinoPane\PHPolyglot\Exception\InvalidVoiceCodeException;
use GinoPane\PHPolyglot\Exception\InvalidVoiceParametersException;
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

        $ttsApi = $this->getTtsApiFactory()->getApi();

        $this->assertTrue($ttsApi instanceof TtsApiInterface);
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

    /**
     * @dataProvider getInvalidDataForTtsContext
     *
     * @param string $language
     * @param string $audio
     * @param array  $additional
     * @param string $expectedException
     * @param string $expectedExceptionMessage
     */
    public function testIfTtsApiThrowsExceptionsForInvalidTextToSpeechRequestContextData(
        string $language,
        string $audio,
        array $additional,
        string $expectedException,
        string $expectedExceptionMessage
    ) {
        $this->expectException($expectedException);
        $this->expectExceptionMessage($expectedExceptionMessage);

        $ttsApi = $this->getTtsApiFactory()->getApi();

        $createRequestMethod = $this->getInternalMethod($ttsApi, 'createTextToSpeechContext');

        $textString = 'Hello World!';

        $format = new TtsAudioFormat();
        $this->setInternalProperty($format, 'format', $audio);

        /** @var RequestContext $context */
        $createRequestMethod->invoke(
            $ttsApi,
            $textString,
            new Language($language),
            $format,
            $additional
        );
    }

    /**
     * @dataProvider getErroneousResponsesForErrorProcessing
     *
     * @param ResponseContext $context
     * @param string          $expectedError
     * @param int             $expectedErrorCode
     */
    public function testIfProcessApiErrorsWorksCorrectly(
        ResponseContext $context,
        string $expectedError,
        int $expectedErrorCode = 0)
    {
        $this->expectExceptionCode($expectedErrorCode);
        $this->expectExceptionMessage($expectedError);

        $nanoRest = $this->getMockBuilder(NanoRest::class)
            ->setMethods(array('sendRequest'))
            ->getMock();

        $nanoRest->method('sendRequest')->willReturn($context);

        $ttsApi = $this->getTtsApiFactory()->getApi();

        $this->setInternalProperty($ttsApi, 'httpClient', $nanoRest);

        $ttsApi->textToSpeech('Hello world', new Language('en'), new TtsAudioFormat());
    }

    /**
     * @dataProvider getValidResponsesForResponseProcessing
     *
     * @param ResponseContext $context
     *
     * @throws InvalidIoException
     * @throws InvalidPathException
     */
    public function testIfValidResponseCanBeProcessed(
        ResponseContext $context
    ) {
        $nanoRest = $this->getMockBuilder(NanoRest::class)
            ->setMethods(array('sendRequest'))
            ->getMock();

        $nanoRest->method('sendRequest')->willReturn($context);

        $ttsApi = $this->getTtsApiFactory()->getApi();

        $this->setInternalProperty($ttsApi, 'httpClient', $nanoRest);

        /** @var TtsResponse $response */
        $response = $ttsApi->textToSpeech('Hello world', new Language('en'), new TtsAudioFormat());

        $this->assertTrue($response instanceof TtsResponse);
    }

    /**
     * @dataProvider getValidResponsesForResponseProcessing
     *
     * @param ResponseContext $context
     * @param string          $expected
     */
    public function testIfValidResponseCanBeProcessedByTtsResponse(
        ResponseContext $context,
        string $expected
    ) {
        $api = $this->getTtsApiFactory()->getApi();
        $getAudioFormatByContentTypeHeader = $this->getInternalMethod($api, 'getAudioFormatByContentTypeHeader');

        /** @var TtsResponse $stub */
        $stub = $this->getMockBuilder(TtsResponse::class)
            ->setMethods(array('getTtsApiFactory'))
            ->disableOriginalConstructor()
            ->getMock();

        $stub->method('getTtsApiFactory')->willReturn($this->getTtsApiFactory());

        $stub->__construct(
            $context->getRaw(),
            $getAudioFormatByContentTypeHeader->invoke($api, $context->headers()),
            json_decode($context->getRequestContext()->getData(), true)['text']
        );

        $file = $stub->storeFile();

        $this->assertEquals(md5('hello world').".ogg", $file);
        $this->assertEquals($expected, file_get_contents($this->getTtsApiFactory()->getTargetDirectory() . DIRECTORY_SEPARATOR . $file));

        $directory = TEST_ROOT . DIRECTORY_SEPARATOR . 'media_test';
        $file = $stub->storeFile('hello world', 'ogg', $directory);

        $this->assertEquals('hello world.ogg', $file);
        $this->assertEquals(
            $expected,
            file_get_contents($directory . DIRECTORY_SEPARATOR . $file)
        );
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
                'https://stream.watsonplatform.net/text-to-speech/api/v1/synthesize?accept=audio%2Fmp3&voice=en-US_AllisonVoice'
            ],
            [
                'en',
                TtsAudioFormat::AUDIO_MPEG,
                ['voice' => 'en-US_LisaVoice'],
                'https://stream.watsonplatform.net/text-to-speech/api/v1/synthesize?accept=audio%2Fmpeg&voice=en-US_LisaVoice'
            ],
            [
                'de',
                TtsAudioFormat::AUDIO_WAV,
                ['gender' => 'm', 'rate' => 8000],
                'https://stream.watsonplatform.net/text-to-speech/api/v1/synthesize?accept=audio%2Fwav%3Brate%3D8000&voice=de-DE_DieterVoice'
            ],
            [
                'de',
                TtsAudioFormat::AUDIO_OGG,
                ['gender' => 'f', 'rate' => 'foo', 'codec' => 'vorbis'],
                'https://stream.watsonplatform.net/text-to-speech/api/v1/synthesize?accept=audio%2Fogg%3Bcodecs%3Dvorbis&voice=de-DE_BirgitVoice'
            ],
            [
                'es',
                TtsAudioFormat::AUDIO_OGG,
                ['gender' => 'f', 'rate' => '22050', 'codec' => 'opus'],
                'https://stream.watsonplatform.net/text-to-speech/api/v1/synthesize?accept=audio%2Fogg%3Brate%3D22050%3Bcodecs%3Dopus&voice=es-ES_LauraVoice'
            ],
            [
                'it',
                TtsAudioFormat::AUDIO_WEBM,
                ['gender' => 'f', 'rate' => '22050', 'codec' => 'opus'],
                'https://stream.watsonplatform.net/text-to-speech/api/v1/synthesize?accept=audio%2Fwebm%3Bcodecs%3Dopus&voice=it-IT_FrancescaVoice'
            ],
            [
                'it',
                TtsAudioFormat::AUDIO_WEBM,
                ['gender' => 'f', 'rate' => '22050', 'codec' => 'vorbis'],
                'https://stream.watsonplatform.net/text-to-speech/api/v1/synthesize?accept=audio%2Fwebm%3Brate%3D22050%3Bcodecs%3Dvorbis&voice=it-IT_FrancescaVoice'
            ],
            [
                'it',
                TtsAudioFormat::AUDIO_MULAW,
                ['gender' => 'f', 'rate' => '22050', 'codec' => 'vorbis'],
                'https://stream.watsonplatform.net/text-to-speech/api/v1/synthesize?accept=audio%2Fmulaw%3Brate%3D22050&voice=it-IT_FrancescaVoice'
            ]
        ];
    }

    /**
     * @return array
     */
    public function getInvalidDataForTtsContext(): array
    {
        return [
            [
                'it',
                'mp3',
                ['gender' => 'm'],
                InvalidVoiceParametersException::class,
                "Couldn't find the voice for requested language \"it\" and gender \"m\""
            ],
            [
                'it',
                'mp13',
                [],
                InvalidAudioFormatCodeException::class,
                'Audio format "mp13" is invalid'
            ],
            [
                'it',
                'ogg',
                ['codec' => 'popus'],
                InvalidAudioFormatParameterException::class,
                'Specified codec "popus" is invalid'
            ],
            [
                'it',
                'ogg',
                ['codec' => 'popus'],
                InvalidAudioFormatParameterException::class,
                'Specified codec "popus" is invalid'
            ],
            [
                'it',
                'mulaw',
                ['codec' => 'popus'],
                InvalidAudioFormatParameterException::class,
                'Parameter "rate" is required'
            ],
            [
                'it',
                'mp3',
                ['voice' => 'en'],
                InvalidVoiceCodeException::class,
                'Voice code "en" is invalid'
            ],
            [
                'it',
                'mp3',
                ['voice' => 'de-DE_DieterVoice'],
                InvalidVoiceParametersException::class,
                'The requested language "it" is not compatible with the requested voice "de-DE_DieterVoice"'
            ]
        ];
    }

    /**
     * Get stubbed version of TtsApiFactory
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
        $responseContextWithoutContentType = (new DummyResponseContext())->setHttpStatusCode(200);
        $responseContextWithoutContentType->headers()->setHeadersFromString(
            "
                Connection: Keep-Alive
                Content-Disposition: inline; filename=\"result.ogg\"
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

        $responseContextWithInvalidAudioFormat1 = (new DummyResponseContext())->setHttpStatusCode(200);
        $responseContextWithInvalidAudioFormat1->headers()->setHeadersFromString(
            "
                Connection: Keep-Alive
                Content-Disposition: inline; filename=\"result.ogg\"
                Content-Type: audio/unknown codecs=opus
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

        $responseContextWithInvalidAudioFormat2 = (new DummyResponseContext())->setHttpStatusCode(200);
        $responseContextWithInvalidAudioFormat2->headers()->setHeadersFromString(
            "
                Connection: Keep-Alive
                Content-Disposition: inline; filename=\"result.ogg\"
                Content-Type: audio/unknown; codecs=opus
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

        return [
            [
                (new DummyResponseContext())->setHttpStatusCode(501),
                'Not Implemented',
                501
            ],
            [
                (new DummyResponseContext())->setHttpStatusCode(400),
                'Bad Request',
                400
            ],
            [
                (new DummyResponseContext('{
                    "code_description": "Not Acceptable",
                    "code": 406,
                    "error": "Unsupported mimetype.  Supported mimetypes are: [\'application/json\', \'audio/basic\', \'audio/flac\', \'audio/l16\', \'audio/l16; rate=22050\', \'audio/mp3\', \'audio/mpeg\', \'audio/ogg\', \'audio/ogg;codecs=opus\', \'audio/wav\', \'audio/webm\']"
                }'))->setHttpStatusCode(406),
                "Not Acceptable: Unsupported mimetype.  Supported mimetypes are: ['application/json', 'audio/basic', 'audio/flac', 'audio/l16', 'audio/l16; rate=22050', 'audio/mp3', 'audio/mpeg', 'audio/ogg', 'audio/ogg;codecs=opus', 'audio/wav', 'audio/webm']",
                406
            ],
            [
                (new DummyResponseContext('{
                    "code_description": "Not Found",
                    "code": 404,
                    "error": "Model es-ES_LauraVoic not found"
                }'))->setHttpStatusCode(404),
                "Not Found: Model es-ES_LauraVoic not found",
                404
            ],
            [
                $responseContextWithoutContentType,
                "Response content-type is invalid or empty",
                0
            ],
            [
                $responseContextWithInvalidAudioFormat1,
                'Cannot extract audio format from content type: "audio/unknown codecs=opus"',
                0
            ],
            [
                $responseContextWithInvalidAudioFormat2,
                'Cannot extract audio format from content type: "audio/unknown; codecs=opus"',
                0
            ]
        ];
    }

    /**
     * @return array
     */
    public function getValidResponsesForResponseProcessing(): array
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

        return [
            [
                $responseContext,
                $fileContents
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
