<?php

namespace GinoPane\PHPolyglot\API\Implementation\Translate\Yandex;

use GinoPane\NanoRest\Request\RequestContext;
use GinoPane\NanoRest\Response\ResponseContextAbstract;
use GinoPane\PHPolyglot\Supplemental\Language\Language;
use GinoPane\NanoRest\Exceptions\RequestContextException;
use GinoPane\PHPolyglot\Exception\InvalidResponseContent;
use GinoPane\PHPolyglot\Exception\BadResponseContextException;
use GinoPane\PHPolyglot\API\Supplemental\Yandex\YandexApiTrait;
use GinoPane\PHPolyglot\API\Response\Translate\TranslateResponse;
use GinoPane\PHPolyglot\API\Implementation\Translate\TranslateApiAbstract;

/**
 * Class YandexTranslateApi
 *
 * Yandex Translate API implementation
 *
 * API version 1.5
 *
 * @link   https://tech.yandex.com/translate/doc/dg/concepts/api-overview-docpage/
 * @link   https://tech.yandex.com/keys/?service=trnsl
 *
 * @author Sergey <Gino Pane> Karavay
 */
class YandexTranslateApi extends TranslateApiAbstract
{
    /**
     * API constant string for undetected language
     */
    protected const LANGUAGE_UNDETECTED = 'no';

    /**
     * URL path for translate action
     */
    const TRANSLATE_API_PATH = 'translate';

    /**
     * Main API endpoint
     *
     * @var string
     */
    protected $apiEndpoint = 'https://translate.yandex.net/api/v1.5/tr.json';

    /**
     * Mapping of properties to environment variables which must supply these properties
     *
     * @var array
     */
    protected $envProperties = [
        'apiKey' => 'YANDEX_TRANSLATE_API_KEY'
    ];

    use YandexApiTrait;

    /**
     * Create request context for translate request
     *
     * @param string   $text
     * @param Language $languageTo
     * @param Language $languageFrom
     *
     * @throws RequestContextException
     *
     * @return RequestContext
     */
    protected function createTranslateContext(
        string $text,
        Language $languageTo,
        Language $languageFrom
    ): RequestContext {
        $requestContext = (new RequestContext(sprintf("%s/%s", $this->apiEndpoint, self::TRANSLATE_API_PATH)))
            ->setRequestParameters(
                [
                    'lang' => $this->getLanguageString($languageTo, $languageFrom)
                ] + $this->getAuthData()
            )
            ->setData(['text' => $text])
            ->setMethod(RequestContext::METHOD_POST);

        return $this->fillGeneralRequestSettings($requestContext);
    }

    /**
     * Process response of translate request and prepare valid response
     *
     * @param ResponseContextAbstract $context
     *
     * @return TranslateResponse
     */
    protected function prepareTranslateResponse(ResponseContextAbstract $context): TranslateResponse
    {
        return $this->processTranslateResponse($context);
    }

    /**
     * Create request context for bulk translate request
     *
     * @param array    $texts
     * @param Language $languageTo
     * @param Language $languageFrom
     *
     * @throws RequestContextException
     *
     * @return RequestContext
     */
    protected function createTranslateBulkContext(
        array $texts,
        Language $languageTo,
        Language $languageFrom
    ): RequestContext {
        $requestContext = (new RequestContext(sprintf("%s/%s", $this->apiEndpoint, self::TRANSLATE_API_PATH)))
            ->setRequestParameters(
                [
                    'lang' => $this->getLanguageString($languageTo, $languageFrom)
                ] + $this->getAuthData()
            )
            ->setData(['text' => $texts])
            ->setMethod(RequestContext::METHOD_POST)
            ->setEncodeArraysUsingDuplication(true);

        return $this->fillGeneralRequestSettings($requestContext);
    }

    /**
     * Process response of bulk translate request and prepare valid response
     *
     * @param ResponseContextAbstract $context
     *
     * @return TranslateResponse
     */
    protected function prepareTranslateBulkResponse(ResponseContextAbstract $context): TranslateResponse
    {
        return $this->processTranslateResponse($context);
    }

    /**
     * Filters ResponseContext from common HTTP errors
     *
     * @param ResponseContextAbstract $responseContext
     *
     * @throws BadResponseContextException
     * @throws InvalidResponseContent
     */
    protected function processApiResponseContextErrors(ResponseContextAbstract $responseContext): void
    {
        $responseArray = $responseContext->getArray();

        $this->filterYandexSpecificErrors($responseArray);

        parent::processApiResponseContextErrors($responseContext);

        if (empty($responseArray['text'])) {
            throw new InvalidResponseContent(sprintf('There is no required field "%s" in response', 'text'));
        }
    }

    /**
     * @param ResponseContextAbstract $context
     *
     * @return TranslateResponse
     */
    private function processTranslateResponse(ResponseContextAbstract $context): TranslateResponse
    {
        $responseArray = $context->getArray();

        $response = new TranslateResponse();

        $response->setTranslations((array)$responseArray['text']);

        if (isset($responseArray['lang'])) {
            list($fromLanguage, $toLanguage) = explode("-", $responseArray['lang']);

            if ($fromLanguage !== self::LANGUAGE_UNDETECTED) {
                $response->setLanguageFrom($fromLanguage);
            }

            $response->setLanguageTo($toLanguage);
        }

        return $response;
    }
}
