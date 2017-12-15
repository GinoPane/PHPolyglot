<?php

namespace GinoPane\PHPolyglot\API\Implementation\Translate;

use GinoPane\PHPolyglot\API\Implementation\ApiAbstract;
use GinoPane\PHPolyglot\API\Response\Translate\TranslateApiResponse;

class TranslateApiAbstract extends ApiAbstract implements TranslateApiInterface
{
    /**
     * @param string $text
     * @param string $languageTo
     * @param string $languageFrom
     *
     * @return TranslateApiResponse
     */
    public function translate(string $text, string $languageTo, string $languageFrom = ''): TranslateApiResponse
    {
        /** @var TranslateApiResponse $response */
        $response = $this->callApi(__FUNCTION__, [$text, $languageTo, $languageFrom]);

        return $response;
    }

    /**
     * @param array $text
     * @param string $languageTo
     * @param string $languageFrom
     *
     * @return TranslateApiResponse
     */
    public function translateBulk(array $text, string $languageTo, string $languageFrom = ''): TranslateApiResponse
    {
        /** @var TranslateApiResponse $response */
        $response = $this->callApi(__FUNCTION__, [$text, $languageTo, $languageFrom]);

        return $response;
    }
}
