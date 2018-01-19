<?php

namespace GinoPane\PHPolyglot\API\Implementation\TTS\IbmWatson;

use GinoPane\NanoRest\Exceptions\RequestContextException;
use GinoPane\NanoRest\Request\RequestContext;
use GinoPane\NanoRest\Response\ResponseContext;
use GinoPane\PHPolyglot\API\Response\TTS\TtsResponse;
use GinoPane\PHPolyglot\Supplemental\Language\Language;
use GinoPane\PHPolyglot\API\Supplemental\TTS\TtsAudioFormat;
use GinoPane\PHPolyglot\API\Implementation\TTS\TtsApiAbstract;

/**
 * Class IbmWatsonTtsApi
 *
 * @link https://www.ibm.com/watson/services/text-to-speech/
 *
 * @author Sergey <Gino Pane> Karavay
 */
class IbmWatsonTtsApi extends TtsApiAbstract
{
    /**
     * URL path for text-to-speech action
     */
    const TEXT_TO_SPEECH_API_PATH = 'synthesize';

    /**
     * Main API endpoint
     *
     * @var string
     */
    protected $apiEndpoint = 'https://stream.watsonplatform.net/text-to-speech/api/v1/';

    /**
     * API username required for authorisation
     *
     * @var string
     */
    protected $username = '';

    /**
     * API password required for authorisation
     *
     * @var string
     */
    protected $password = '';

    /**
     * Mapping of properties to environment variables which must supply these properties
     *
     * @var array
     */
    protected $envProperties = [
        'username' => 'IBM_WATSON_TTS_API_USERNAME',
        'password' => 'IBM_WATSON_TTS_API_PASSWORD',
    ];

    /**
     * Create request context for text-to-speech request
     *
     * @param string         $text
     * @param Language       $language
     * @param TtsAudioFormat $format
     * @param array          $additionalData
     *
     * @throws RequestContextException
     *
     * @return RequestContext
     */
    protected function createTextToSpeechContext(
        string $text,
        Language $language,
        TtsAudioFormat $format,
        array $additionalData = []
    ): RequestContext {
        $requestContext = (new RequestContext(sprintf("%s/%s", $this->apiEndpoint, self::TEXT_TO_SPEECH_API_PATH)))
            ->setRequestParameters(
                array_filter(
                    [
                        'accept' => '',
                        'voice' => ''
                    ]
                )
            )
            ->setData(json_decode(['text' => $text]))
            ->setMethod(RequestContext::METHOD_POST);

        return $this->fillGeneralRequestSettings($requestContext);
    }

    /**
     * Process response of text-to-speech request and prepare valid response
     *
     * @param ResponseContext $context
     *
     * @return TtsResponse
     */
    protected function prepareTextToSpeechResponse(ResponseContext $context): TtsResponse
    {
        // TODO: Implement prepareTextToSpeechResponse() method.
    }
}
