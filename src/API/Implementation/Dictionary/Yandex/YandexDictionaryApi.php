<?php

namespace GinoPane\PHPolyglot\API\Implementation\Dictionary\Yandex;

use GinoPane\NanoRest\Exceptions\RequestContextException;
use GinoPane\NanoRest\Request\RequestContext;
use GinoPane\NanoRest\Response\ResponseContext;
use GinoPane\PHPolyglot\API\Response\Dictionary\DictionaryResponse;
use GinoPane\PHPolyglot\API\Response\Dictionary\Entry\DictionaryEntry;
use GinoPane\PHPolyglot\API\Response\Dictionary\POS\DictionaryEntryPos;
use GinoPane\PHPolyglot\API\Supplemental\Yandex\YandexApiTrait;
use GinoPane\PHPolyglot\API\Implementation\Dictionary\DictionaryApiAbstract;
use GinoPane\PHPolyglot\Exception\BadResponseContextException;
use GinoPane\PHPolyglot\Exception\InvalidResponseContent;
use GinoPane\PHPolyglot\Supplemental\Language\Language;

/**
 * Class YandexDictionaryApi
 *
 * Yandex Dictionary API implementation
 *
 * API version 1
 *
 * @link https://tech.yandex.com/dictionary/doc/dg/concepts/api-overview-docpage/
 *
 * @author Sergey <Gino Pane> Karavay
 */
class YandexDictionaryApi extends DictionaryApiAbstract
{
    /**
     * Family search filter (child-safe)
     */
    const LOOKUP_FAMILY_FLAG = 0x1;

    /**
     * Search by word form
     */
    const LOOKUP_MORPHO_FLAG = 0x4;

    /**
     * Enable a filter that requires matching parts of speech for the search word and translation
     */
    const LOOKUP_POS_FILTER_FLAG = 0x8;

    /**
     * URL path for lookup action
     */
    protected const LOOKUP_API_PATH = 'lookup';

    /**
     * Main API endpoint
     *
     * @var string
     */
    protected $apiEndpoint = 'https://dictionary.yandex.net/api/v1/dicservice.json';

    /**
     * Mapping of properties to environment variables which must supply these properties
     *
     * @var array
     */
    protected $envProperties = [
        'apiKey' => 'YANDEX_DICTIONARY_API_KEY'
    ];

    use YandexApiTrait;

    /**
     * Create request context for get-text-alternatives request
     *
     * @param string $text
     * @param string $language
     *
     * @throws RequestContextException
     *
     * @return RequestContext
     */
    protected function createGetTextAlternativesContext(
        string $text,
        string $language
    ): RequestContext {
        $requestContext = $this->getLookupRequest($text, $language, $language);

        return $this->fillGeneralRequestSettings($requestContext);
    }

    /**
     * Process response of get-text-alternatives request and prepare valid response
     *
     * @param ResponseContext $context
     *
     * @return DictionaryResponse
     */
    protected function prepareGetTextAlternativesResponse(ResponseContext $context): DictionaryResponse
    {
        $responseArray = $context->getArray()['def'];

        $response = new DictionaryResponse();

        foreach ($responseArray as $sourceTextGroup) {
            if (empty($sourceTextGroup['text'])) {
                continue;
            }

            if (empty($sourceTextGroup['tr']) || !is_array($sourceTextGroup['tr'])) {
                continue;
            }

            foreach ($sourceTextGroup['tr'] as $targetTextGroup) {
                if (empty($targetTextGroup['text'])) {
                    continue;
                }

                $entry = new DictionaryEntry();

                $entry->setTextFrom($sourceTextGroup['text']);
                $entry->setTextTo($targetTextGroup['text']);

                if (!empty($sourceTextGroup['ts'])) {
                    $entry->setTranscription($sourceTextGroup['ts']);
                }

                if (!empty($sourceTextGroup['pos'])) {
                    $entry->setPosFrom(new DictionaryEntryPos($sourceTextGroup['pos']));
                }

                if (!empty($targetTextGroup['pos'])) {
                    $entry->setPosTo(new DictionaryEntryPos($targetTextGroup['pos']));
                }

                if (!empty($targetTextGroup['syn']) && is_array($targetTextGroup['syn'])) {
                    $synonyms = [];

                    foreach ($targetTextGroup['syn'] as $synonym) {
                        if (empty($synonym['text'])) {
                            continue;
                        }

                        $synonyms[] = $synonym['text'];
                    }

                    $entry->setSynonyms($synonyms);
                }

                if (!empty($targetTextGroup['mean']) && is_array($targetTextGroup['mean'])) {
                    $meanings = [];

                    foreach ($targetTextGroup['mean'] as $meaning) {
                        if (empty($meaning['text'])) {
                            continue;
                        }

                        $meanings[] = $meaning['text'];
                    }

                    $entry->setMeanings($meanings);
                }

                $response->addEntry($entry);
            }
        }

        var_dump($response->getEntries());

        return $response;
    }

    /**
     * Create request context for get-translate-alternatives request
     *
     * @param string $text
     * @param string $languageTo
     * @param string $languageFrom
     *
     * @throws RequestContextException
     *
     * @return RequestContext
     */
    protected function createGetTranslateAlternativesContext(
        string $text,
        string $languageTo,
        string $languageFrom
    ): RequestContext {
        $requestContext = $this->getLookupRequest($text, $languageTo, $languageFrom);

        return $this->fillGeneralRequestSettings($requestContext);
    }

    /**
     * Process response of get-translate-alternatives request and prepare valid response
     *
     * @param ResponseContext $context
     *
     * @return DictionaryResponse
     */
    protected function prepareGetTranslateAlternativesResponse(ResponseContext $context): DictionaryResponse
    {
        // TODO: Implement prepareGetTranslateAlternativesResponse() method.
    }

    /**
     * Filters ResponseContext from common HTTP errors
     *
     * @param ResponseContext $responseContext
     *
     * @throws InvalidResponseContent
     * @throws BadResponseContextException
     */
    protected function processApiResponseContextErrors(ResponseContext $responseContext): void
    {
        $responseArray = $responseContext->getArray();

        $this->filterYandexSpecificErrors($responseArray);

        parent::processApiResponseContextErrors($responseContext);

        if (!isset($responseArray['def'])) {
            throw new InvalidResponseContent(sprintf('There is no required field "%s" in response', 'def'));
        }
    }

    /**
     * @param string $text
     * @param string $languageTo
     * @param string $languageFrom
     *
     * @return RequestContext
     * @throws RequestContextException
     */
    private function getLookupRequest(string $text, string $languageTo, string $languageFrom): RequestContext
    {
        $requestContext = (new RequestContext(sprintf("%s/%s", $this->apiEndpoint, self::LOOKUP_API_PATH)))
            ->setRequestParameters(
                [
                    'lang'  => sprintf("%s-%s", $languageFrom, $languageTo),
                    'flags' => self::LOOKUP_MORPHO_FLAG,
                    'ui'    => Language::CODE_ENGLISH
                ] + $this->getAuthData()
            )
            ->setData(['text' => $text])
            ->setMethod(RequestContext::METHOD_POST);

        return $requestContext;
    }
}
