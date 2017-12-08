<?php

namespace GinoPane\PHPolyglot\API\Implementation;

use GinoPane\PHPolyglot\API\Implementation\Translate\TranslateApiInterface;
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
        $response = $this->callApi(__FUNCTION__);

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
        $response = $this->callApi(__FUNCTION__);

        return $response;
    }
}
