<?php

namespace GinoPane\PHPolyglot\API\Implementation\Spellcheck;

use GinoPane\PHPolyglot\Supplemental\Language\Language;
use GinoPane\PHPolyglot\API\Response\Spellcheck\SpellcheckResponse;

/**
 * Interface SpellcheckApiInterface
 *
 * @author Sergey <Gino Pane> Karavay
 */
interface SpellcheckApiInterface
{
    /**
     * Check spelling for multiple text strings
     *
     * @param array    $text
     * @param Language $languageFrom
     *
     * @return SpellcheckResponse
     */
    public function translateBulk(array $text, Language $languageFrom): SpellcheckResponse;
}
