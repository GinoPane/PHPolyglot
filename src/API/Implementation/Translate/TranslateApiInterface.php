<?php

namespace GinoPane\PHPolyglot\API\Implementation\Translate;

use GinoPane\PHPolyglot\API\Response\Translate\TranslateApiResponse;

/**
 * Interface TranslateApiInterface
 */
interface TranslateApiInterface
{
    /**
     * @param string $text
     * @param string $languageTo
     * @param string $languageFrom
     *
     * @return TranslateApiResponse
     */
    public function translate(string $text, string $languageTo, string $languageFrom = ''): TranslateApiResponse;

    /**
     * @param array $text
     * @param string $languageTo
     * @param string $languageFrom
     *
     * @return TranslateApiResponse
     */
    public function translateBulk(array $text, string $languageTo, string $languageFrom = ''): TranslateApiResponse;
}