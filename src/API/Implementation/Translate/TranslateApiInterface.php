<?php

namespace GinoPane\PHPolyglot\API\Implementation\Translate;

use GinoPane\PHPolyglot\API\Response\Translate\{
    TranslateResponse, TranslateResponseCollection
};

/**
 * Interface TranslateApiInterface
 */
interface TranslateApiInterface
{
    /**
     * @param string $text
     * @param string $to
     * @param string $from
     *
     * @return TranslateResponse
     */
    public function translate(string $text, string $to, string $from = ''): TranslateResponse;

    /**
     * @param array $text
     * @param string $to
     * @param string $from
     *
     * @return TranslateResponseCollection
     */
    public function translateBulk(array $text, string $to, string $from = ''): TranslateResponseCollection;
}
