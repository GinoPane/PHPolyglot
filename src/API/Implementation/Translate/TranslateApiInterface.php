<?php

namespace GinoPane\PHPolyglot\API\Implementation\Translate;

use GinoPane\PHPolyglot\API\Response\Translate\TranslateResponse;

/**
 * Interface TranslateApiInterface
 */
interface TranslateApiInterface
{
    /**
     * Translate single text string
     *
     * @param string $text
     * @param string $languageTo
     * @param string $languageFrom
     *
     * @return TranslateResponse
     */
    public function translate(string $text, string $languageTo, string $languageFrom = ''): TranslateResponse;

    /**
     * Translate multiple text strings
     *
     * @param array $text
     * @param string $languageTo
     * @param string $languageFrom
     *
     * @return TranslateResponse
     */
    public function translateBulk(array $text, string $languageTo, string $languageFrom = ''): TranslateResponse;
}
