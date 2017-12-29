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
     * Gets text alternatives either in the same language (possible text forms)
     * or in different language (alternative translations)
     *
     * @param string $text
     * @param string $languageFrom
     * @param string $languageTo
     *
     * @return DictionaryResponse
     */
    public function getTextAlternatives(
        string $text,
        string $languageFrom,
        string $languageTo = ''
    ): DictionaryResponse;
}
