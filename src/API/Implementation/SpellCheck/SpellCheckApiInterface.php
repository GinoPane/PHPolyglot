<?php

namespace GinoPane\PHPolyglot\API\Implementation\SpellCheck;

use GinoPane\PHPolyglot\Supplemental\Language\Language;
use GinoPane\PHPolyglot\API\Response\SpellCheck\SpellCheckResponse;

/**
 * Interface SpellCheckApiInterface
 *
 * @author Sergey <Gino Pane> Karavay
 */
interface SpellCheckApiInterface
{
    /**
     * Check spelling for multiple text strings
     *
     * @param array    $texts
     * @param Language $languageFrom
     *
     * @return SpellCheckResponse
     */
    public function checkTexts(array $texts, Language $languageFrom): SpellCheckResponse;
}
