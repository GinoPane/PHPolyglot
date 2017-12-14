<?php

namespace GinoPane\PHPolyglot;

use GinoPane\PHPolyglot\API\Factory\Translate\TranslateApiFactory;
use GinoPane\PHPolyglot\API\Response\Translate\TranslateApiResponse;
use GinoPane\PHPolyglot\API\Implementation\Translate\TranslateApiInterface;

define(__NAMESPACE__ . '\ROOT_DIRECTORY', dirname(__FILE__, 2));

/**
 *  PHPolyglot
 *
 *  Easily translate, do spell check and speak-out texts in different languages
 *
 *  @author Sergey <Gino Pane> Karavay
 */
class PHPolyglot
{
    public function from(string $language): PHPolyglot
    {
        return $this;
    }

    public function to(string $language): PHPolyglot
    {
        return $this;
    }

    public function withLookup(bool $enableLookup): PHPolyglot
    {
        return $this;
    }

    public function usingVoice(string $voice): PHPolyglot
    {
        return $this;
    }

    /**
     * @param string $text
     * @param string $languageTo
     * @param string $languageFrom
     *
     * @return TranslateApiResponse
     */
    public function translate(string $text, string $languageTo, string $languageFrom = ''): TranslateApiResponse
    {
        return $this->getTranslateApi()->translate($text, $languageTo, $languageFrom);
    }

    public function translateBulk()
    {

    }

    public function speak()
    {

    }

    public function lookup()
    {

    }

    /**
     * Get Translate API instance
     *
     * @return TranslateApiInterface
     */
    protected function getTranslateApi(): TranslateApiInterface
    {
        return (new TranslateApiFactory())->getApi();
    }
}
