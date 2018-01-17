<?php

namespace GinoPane\PHPolyglot\API\Implementation\Dictionary;

use GinoPane\PHPolyglot\Supplemental\Language\Language;
use GinoPane\PHPolyglot\API\Response\Dictionary\DictionaryResponse;

/**
 * Interface DictionaryApiInterface
 *
 * @author Sergey <Gino Pane> Karavay
 */
interface DictionaryApiInterface
{
    /**
     * Gets text alternatives
     *
     * @param string   $text
     * @param Language $language
     *
     * @return DictionaryResponse
     */
    public function getTextAlternatives(
        string $text,
        Language $language
    ): DictionaryResponse;

    /**
     * Gets text translate alternatives
     *
     * @param string   $text
     * @param Language $languageTo
     * @param Language $languageFrom
     *
     * @return DictionaryResponse
     */
    public function getTranslateAlternatives(
        string $text,
        Language $languageTo,
        Language $languageFrom
    ): DictionaryResponse;
}
