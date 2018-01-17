<?php

namespace GinoPane\PHPolyglot\API\Implementation\Translate;

use GinoPane\PHPolyglot\Supplemental\Language\Language;
use GinoPane\PHPolyglot\API\Response\Translate\TranslateResponse;

/**
 * Interface TranslateApiInterface
 *
 * @author Sergey <Gino Pane> Karavay
 */
interface TranslateApiInterface
{
    /**
     * Translate single text string
     *
     * @param string   $text
     * @param Language $languageTo
     * @param Language $languageFrom
     *
     * @return TranslateResponse
     */
    public function translate(string $text, Language $languageTo, Language $languageFrom): TranslateResponse;

    /**
     * Translate multiple text strings
     *
     * @param array    $text
     * @param Language $languageTo
     * @param Language $languageFrom
     *
     * @return TranslateResponse
     */
    public function translateBulk(array $text, Language $languageTo, Language $languageFrom): TranslateResponse;
}
