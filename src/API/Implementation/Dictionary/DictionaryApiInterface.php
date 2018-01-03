<?php

namespace GinoPane\PHPolyglot\API\Implementation\Dictionary;

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
     * @param string $text
     * @param string $language
     *
     * @return DictionaryResponse
     */
    public function getTextAlternatives(
        string $text,
        string $language
    ): DictionaryResponse;

    /**
     * Gets text translate alternatives
     *
     * @param string $text
     * @param string $languageTo
     * @param string $languageFrom
     *
     * @return DictionaryResponse
     */
    public function getTranslateAlternatives(
        string $text,
        string $languageTo,
        string $languageFrom
    ): DictionaryResponse;
}
