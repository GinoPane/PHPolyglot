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
     * @param string          $expected
     */
    public function testIfValidResponseCanBeProcessed(
        ResponseContext $context,
        string $expected
    ) {
        $nanoRest = $this->getMockBuilder(NanoRest::class)
            ->setMethods(array('sendRequest'))
            ->getMock();

        $nanoRest->method('sendRequest')->willReturn($context);

        $ttsApi = $this->getTtsApiFactory()->getApi();

        $this->setInternalProperty($ttsApi, 'httpClient', $nanoRest);

        /** @var TranslateResponse $response */
        $response = $ttsApi->textToSpeech('Hello world', new Language('en'), new TtsAudioFormat());

        $this->assertTrue($response instanceof TtsResponse);
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

        $responseContextWithInvalidAudioFormat = (new DummyResponseContext())->setHttpStatusCode(200);
        $responseContextWithInvalidAudioFormat->headers()->setHeadersFromString(
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
                $responseContextWithInvalidAudioFormat,
                "Not Found: Model es-ES_LauraVoic not found",
                0
            ]
        ];
    }

    /**
     * @return array
     */
    public function getValidResponsesForResponseProcessing(): array
    {
        $fileContents = <<<'FILE'
OggS         ��t�    6Xk�OpusHead� �]     OggS          ��t�   i��>OpusTags
   Lavf57.71.100      encoder=Lavc57.89.100 libopusOggS ؗ      ��t�   Qs�-�\e������϶�������� �	҂�'������y������K_���C�f\f�'a�Z�j��v*a U$�sᎫ��4�{�`$�֕g��X'	�7L�����������v~��Qn�A�:���Q���A��|�9Y�YP&�<��蝈�.h�E	�*\v��B*��檷MBӸ?�}��j�E����\/��+���a�Y���l
���\e�8I�Kp�O�z\v���\�a��̭^�o0)�zo�mP��\v�G'�c�!QpE�⹑E+�y\e��dR]���5y>	7�w�Mܲ]S�QEY�&����$�$���>�� 7�
vP�~{��Y���dL�BF�#\bq�E��ge�/�:8klOr�}jw�	j�m�Ұ�cӜ�+
Ϳ\v�\f���=y֫�Ys�2h���O�Rh��U1k��˻p�L�bOE?)��=N9���w���8z�4�1J��%��'� ��)�f�\6�bm��\v��8)���~E�n���XQ*�%����K�m^sPS�L-Ӟ�ʞN�X�.��TQ�\f����E�㞞紭��#�Fݏ���c=�Fg���j�l�v��?j���z����(�VU))��E����N�\bC\";�cJ�iu�6ن^��<M�\"I_�-{3�1+?Ӑ1��\v4���$�?��\f�s���\e(\bˋ�h�j���}^M�9h�<��Hm(�p�;A5�E�m·s����U��-^���8&_tl�أw�����C��N���vr�s=ÆX��s��v�B?F�frѴH�g���n�$ ��k�kBz>=�ï�uZU~��*�et��$e]�@~>p:�>uw���
�+����^�rKf	#ώ�Uo�*AC��;Гi����Z��K�:�$b���@��<@�69	@�
g!�mQ�Zq�G;\"��*<�C��bL\"<�Ձ��z�B���I�[��#��\eDo�6P]U�X\fJ��,в�j���
���U;�:��hU��w�AVum
�H�&�}�fY�����3�Ƀ�eG��K��ԂuckXu@Ɖ�m�Jqx	���d��+QR��7�L�~c���\"�[��67�0�!�W1�F�mF� Q[[�΂L�s��m�V�5�>^-W��,�ث[�Q$��>�퐍cc(��h�Ҍ�*��Ğ��B\f3\���ǧ&�f�Q�µ�\v ��:G6y�Ɔ(\b�D���oJ �y��y$�E��,/�������n�\D\"3އM�P �#z��.â ���#��8?ntU��O���s~�c�B�<} ta)L|Lt�5�퀞L�z*���ŵl��r\e0�؍W������:�=�|��w�\E�7YQ'�?� �MB\��XU 5l���;��XY\"p�q��X�x�����(o��!P��N�9rp��-����� G�̢�*
���դn���Lv����d��-��Ӹ�wK�6d#��0aDyk��@G�\b��hWr���c�'��19��
�%��]\���9ٶ��	����{>W�#.�ث�Z���Gu�e�5M�����{�^y�I���­�C��I0\"�}��!7�f���������SNcg��\f�\b���p%/�Xe�Q�GuU�H:
��uuz���ͩ�[��D`q7	n.���.� �^S����g�$wP
<j@<>/���\e�Ƚ�u[�ugH�����!K�����p�OQ�V��/d�@j���/W��%��`�[K*�ث;���)*� V�~5=��Bf��ZZ���:k�f��
:�S��h�\�c�xl�fπWF3װ�u¼����ȝ��@�z��>�rx.�>[4�N��}�}%�7�iB<#$M��32G��C�q���L����ޤOmx��<���/q���1k:��I#\e���-hM9�1����,~���u�|�sr\e(���ĮhW��kg�M�U�b.��H|{iHČ��ti�c7w�n?�����8(�g��cr�N&5��f^�͌<�����4�S�Vs����S�)@ٝ�Xn\/�w��DE���\fU@P��A�'O#K7IU�s�3$2��Fw�d)lr^�@�A�����?�U��@��ى��a��{vc%(����\e��ҐC,���i�_���q^�e�����H߳y�J�٭5S����� ��^�b������~�4���\b�*#����y!2i�Nov�K�>��UtQ��&V�0\e�!�s�NY�,\b��v�c�͟�[����hc#c��Q�˥�%���T�;�Y=P�v��{�L�h^������~�J�e���Bp9�Iu�<yO���W����8�F�&�\e'h)���d�U8�� 
8��wAؗ�������o���Ã�)�B�I5i1�R�R��\�x��=�
U��b u	�}�n\v�Έ\e�!�(��;��؈*C\f��m���?���\"y0�#�GX�u<\e�9�%�SW�����$�r��}E�|g���Nd����1\f���W�%B�b���\b��(�
���F��'\f�#��f�Q�4�ߩ������ϥ��`�J����t��/-Kg'�\eP��9Y�[�L�Eo�&z���H���ln�h����Y��Q��:�pC���3�J
��G����hm[�N�,J���a4 
�Z���oZq����r�`���/��\e�:'�\�\v2�δĠ�$0�B�����^]�	��ղCr㨙g8cYC{!�\"�����L�ȷd�wE?4��%��L���Q���M��\f:u6�\b�9�n΍T*�:���Q�8pn���/��}��uqK[�4k����B�2�2�#���Ƞ�.��)�L����-��0����J�^�����|J�$��/	�<x3�5���GOT�^�;�d�d\"[���X�!k��;c+@����|���Y`vK$�E��m��/�����Gؙ��d��Z�6XR9?����a&ۑ�0ש5]
�F`�I=XI��Ϡ�w�r7���w�i��ԣ�:������]������اl��h���5,�[[�qo@�G^��ޕe�,x8�;�l~1~���7�wD�gj��z>�?g�d��Y衏_�>[��(�y\f�D �X�c��cQ�r����>��'�
닋��=x7,f�c@Ȼڜ\e`,����Xo��o;�y��0�  ������`��ע�@p�� �s�ֵ[���&Y%DX��a�I�I�����\f��l��%>˔!G��o��%��L�xZƣ�l�1�Ľmc�2U*���XO��	y�v�P���t)��Ѹ�ϯFg��2�K/����r\f\�3����\b��ѡu�X3�EO9��KB��t���C�E�g����\"X6�AF����\"�x;\f0\b-�,�.���Um2!Z�����WD�|^�|B#�n�(5��͢]��1� ��@)J>�#o��q}�t���-���1Rb��F�AC�`�+B�l�
�Uҍ�U��0\v37��s�F�䝐�_z�c6jiD�gv�U��J�w��+z����i����+п@X�_d'W�&tv
��6�ӄhdc{v�u�����D�eǎ��pt�E���D���RE���|C�(Z�F���y\e�Js�l���\v�B�\"�PV@\e��j8�_�,�����*7&�������<�'RO�=_����o�.�\"��Et>���lIA��zh�zt4\f�vbA�����M��nq��Y�z�d�6�1�m+ۮ\b������Ұf ������L bhE���]�oP��R�#��'|6���ǌ���=R=@V\�E?�+��M�pI�g#�&Ɉ���D3�� �t��p!ˌo�v�1ل5!qKh�L/
����y[�#�����<����I�E �����S��Q�*� G^ 1^��U(ZRd4���7�kۈ�hY�	#OCZ�=�{
����#sf[>�%\vK�w�o$(<W�^�w����s|[
a.rp� D� ;̨�קǪ��k��H��Ƀ�6^��g�XȌ+mhT��,}�����Xx�������y�z3��շ���٢5�Z�++���q<��A@�9�� �i�ǻ�eg`>�[�L��H:<Z�[,���:ߡH�����-o5/���*g��]�z� 	f`���Y���L\e���-����?�S�.Y�m5���a˼�����9�dDώ̢8X��5��t���ې��\b>��U�\v �w�n��S�D�@��Z>s�Iߧ\f��֊!<��{5��]�����6��	[�^xRQ\b��j�|��ƞÒp�����[.l���US<���X��pa�^�xLv��u)�e
;�����?~�\b@�i�[��&�]c�������\f                                        `���\fL4���
W��3�v6[���8��dˏ���r�}5��
]���K�a�BC�ˈ���|6�~߄AcY��y07\"��aO`�L�~>�؞����?��5?o/f=�j�)�P%��2��AY�ސ�v�;6R�,�ۥu�>�H��!
ݠ��p�a�\bT!�R���%�Z$<�c���r8�ѕ]p�oA��q�@���4%T:���������zȆ4��  �j���س�;wQs�Dǉ��)�2���u�N]�>ir����tRię�0d�Pgh@�,�ӆǲ߰�a�\bSo�5y�������9`�.�1�#0���������{y9�Ýhmœ,�K7�\b7��W�*ԕ�6\v�>����س�;wp�5󁞚��;��
8tM!i\_����MY�A|].=�aK������!��	)�Kc�o�Z0�*Y�u�۫��!����s��p�#�����H���&�<�Џטhm�|G�94����Q�KU����S�qb�F�ob�����������s�#S���V�n����4�����@�
���']��$[�^Nb���w;n�e��h���f¥g\"���H���㒊�	���
�
��u��m�FhG��
CX+=F�\
��6[4���$��-��z��o��2���\b��o��Q���h�n�b&�s�[��$wO�uu�o���h��R�*�4���BeW�-}�,�T�R��r�������QQy���� ��,��R}��Fa�Gŏ�?0[�\f�W���^�x�_�r�nq������������������������������������������������
FILE;

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
