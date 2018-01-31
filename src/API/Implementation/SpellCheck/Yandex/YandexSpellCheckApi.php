<?php

namespace GinoPane\PHPolyglot\API\Implementation\SpellCheck\Yandex;

use GinoPane\NanoRest\Request\RequestContext;
use GinoPane\NanoRest\Response\ResponseContext;
use GinoPane\NanoRest\Response\JsonResponseContext;
use GinoPane\PHPolyglot\Supplemental\Language\Language;
use GinoPane\NanoRest\Exceptions\RequestContextException;
use GinoPane\PHPolyglot\API\Response\SpellCheck\SpellCheckResponse;
use GinoPane\PHPolyglot\API\Implementation\SpellCheck\SpellCheckApiAbstract;

/**
 * Class YandexSpellCheckApi
 *
 * @link https://tech.yandex.ru/speller/doc/dg/concepts/About-docpage/
 *
 * @author Sergey <Gino Pane> Karavay
 */
class YandexSpellCheckApi extends SpellCheckApiAbstract
{
    /**
     * URL path for check texts action
     */
    const CHECK_TEXTS_API_PATH = 'checkTexts';

    /**
     * Main API endpoint
     *
     * @var string
     */
    protected $apiEndpoint = 'http://speller.yandex.net/services/spellservice.json';

    /**
     * Create request context for spell-check request
     *
     * @param array    $texts
     * @param Language $languageFrom
     *
     * @throws RequestContextException
     *
     * @return RequestContext
     */
    protected function createCheckTextsContext(
        array $texts,
        Language $languageFrom
    ): RequestContext {
        $requestContext = (new RequestContext(sprintf("%s/%s", $this->apiEndpoint, self::CHECK_TEXTS_API_PATH)))
            ->setRequestParameters(
                [
                    'lang' => $languageFrom->getCode()
                ]
            )
            ->setData(['text' => $texts])
            ->setMethod(RequestContext::METHOD_POST)
            ->setEncodeArraysUsingDuplication(true)
            ->setContentType(RequestContext::CONTENT_TYPE_FORM_URLENCODED)
            ->setResponseContextClass(JsonResponseContext::class);

        return $requestContext;
    }

    /**
     * Process response of spell-check request and prepare valid response
     *
     * @param ResponseContext $context
     *
     * @return SpellCheckResponse
     */
    protected function prepareCheckTextsResponse(ResponseContext $context): SpellCheckResponse
    {
        $response = new SpellCheckResponse();

        $corrections = [];

        foreach ($context->getArray() as $wordCorrections) {
            $corrected = [];

            foreach ($wordCorrections as $wordCorrection) {
                if (!empty($wordCorrection['s']) && !empty($wordCorrection['word'])) {
                    $corrected[$wordCorrection['word']] = (array)$wordCorrection['s'];
                }
            }

            $corrections[] = $corrected;
        }

        $response->setCorrections($corrections);

        return $response;
    }
}
