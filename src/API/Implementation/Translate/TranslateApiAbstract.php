<?php

namespace GinoPane\PHPolyglot\API\Implementation;

use GinoPane\PHPolyglot\API\Implementation\Translate\TranslateApiInterface;
use GinoPane\PHPolyglot\API\Response\Translate\TranslateResponse;
use GinoPane\PHPolyglot\API\Response\Translate\TranslateResponseCollection;

class TranslateApiAbstract implements TranslateApiInterface
{

    /**
     * @param string $text
     * @param string $to
     * @param string $from
     *
     * @return TranslateResponse
     */
    public function translate(string $text, string $to, string $from = ''): TranslateResponse
    {
        // TODO: Implement translate() method.
    }

    /**
     * @param array $text
     * @param string $to
     * @param string $from
     *
     * @return TranslateResponseCollection
     */
    public function translateBulk(array $text, string $to, string $from = ''): TranslateResponseCollection
    {
        // TODO: Implement translateBulk() method.
    }
}
