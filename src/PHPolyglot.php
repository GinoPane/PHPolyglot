<?php

namespace GinoPane\PHPolyglot;

use GinoPane\PHPolyglot\API\Factory\Translate\TranslateApiFactory;
use GinoPane\PHPolyglot\API\Response\Translate\TranslateResponse;
use GinoPane\PHPolyglot\API\Implementation\Translate\TranslateApiInterface;
use GinoPane\PHPolyglot\Exception\InvalidConfigException;

define(__NAMESPACE__ . '\ROOT_DIRECTORY', dirname(__FILE__));

/**
 * PHPolyglot
 * Easily translate, do spell check and speak-out texts in different languages
 *
 * @author Sergey <Gino Pane> Karavay
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
     * @throws InvalidConfigException
     * @return TranslateResponse
     */
    public function translate(string $text, string $languageTo = '', string $languageFrom = ''): TranslateResponse
    {
        return $this->getTranslateApi()->translate($text, $languageTo, $languageFrom);
    }

    /**
     * @param array  $text
     * @param string $languageTo
     * @param string $languageFrom
     *
     * @throws InvalidConfigException
     * @return TranslateResponse
     */
    public function translateBulk(array $text, string $languageTo = '', string $languageFrom = ''): TranslateResponse
    {
        return $this->getTranslateApi()->translateBulk($text, $languageTo, $languageFrom);
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
     * @throws InvalidConfigException
     * @return TranslateApiInterface
     */
    protected function getTranslateApi(): TranslateApiInterface
    {
        return (new TranslateApiFactory())->getApi();
    }
}
