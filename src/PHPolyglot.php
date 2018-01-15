<?php

namespace GinoPane\PHPolyglot;

use GinoPane\PHPolyglot\API\Factory\Dictionary\DictionaryApiFactory;
use GinoPane\PHPolyglot\API\Factory\Translate\TranslateApiFactory;
use GinoPane\PHPolyglot\API\Factory\Translate\TtsApiFactory;
use GinoPane\PHPolyglot\API\Implementation\Dictionary\DictionaryApiInterface;
use GinoPane\PHPolyglot\API\Implementation\TtsApiInterface;
use GinoPane\PHPolyglot\API\Response\Dictionary\DictionaryResponse;
use GinoPane\PHPolyglot\API\Response\Translate\TranslateResponse;
use GinoPane\PHPolyglot\API\Implementation\Translate\TranslateApiInterface;
use GinoPane\PHPolyglot\API\Response\TTS\TtsResponse;
use GinoPane\PHPolyglot\API\Supplemental\TTS\TtsAudioFormat;
use GinoPane\PHPolyglot\Exception\InvalidConfigException;
use GinoPane\PHPolyglot\Exception\InvalidLanguageCodeException;
use GinoPane\PHPolyglot\Supplemental\Language\Language;

define(__NAMESPACE__ . '\ROOT_DIRECTORY', dirname(__FILE__));

/**
 * PHPolyglot
 * Easily translate, do spell check and speak-out texts in different languages
 *
 * @author Sergey <Gino Pane> Karavay
 */
class PHPolyglot
{
    /**
     * @param string $text
     * @param string $languageTo
     * @param string $languageFrom
     *
     * @throws InvalidConfigException
     * @throws InvalidLanguageCodeException
     *
     * @return TranslateResponse
     */
    public function translate(string $text, string $languageTo, string $languageFrom = ''): TranslateResponse
    {
        list($languageTo, $languageFrom) =  (new Language())->getLanguagesForTranslation($languageTo, $languageFrom);

        return $this->getTranslateApi()->translate($text, $languageTo, $languageFrom);
    }

    /**
     * @param array  $text
     * @param string $languageTo
     * @param string $languageFrom
     *
     * @throws InvalidConfigException
     * @throws InvalidLanguageCodeException
     *
     * @return TranslateResponse
     */
    public function translateBulk(array $text, string $languageTo, string $languageFrom = ''): TranslateResponse
    {
        list($languageTo, $languageFrom) = (new Language())->getLanguagesForTranslation($languageTo, $languageFrom);

        return $this->getTranslateApi()->translateBulk($text, $languageTo, $languageFrom);
    }

    /**
     * The most common use of `lookup` is look up of the word in the same language, that's
     * why the first language parameter of `lookup` method is language-from, language-to is optional,
     * which differs from the language parameters order for translation
     *
     * @param string $text
     * @param string $languageFrom
     * @param string $languageTo
     *
     * @throws InvalidConfigException
     * @throws InvalidLanguageCodeException
     *
     * @return DictionaryResponse
     */
    public function lookup(string $text, string $languageFrom, string $languageTo = ''): DictionaryResponse
    {
        list($languageFrom, $languageTo) = (new Language())->getLanguagesForTranslation($languageFrom, $languageTo);

        if ($languageTo) {
            $response = $this->getDictionaryApi()->getTranslateAlternatives($text, $languageTo, $languageFrom);
        } else {
            $response = $this->getDictionaryApi()->getTextAlternatives($text, $languageFrom);
        }

        return $response;
    }

    /**
     * @param string $text
     * @param string $languageFrom
     * @param string $format
     *
     * @return TtsResponse
     */
    public function speak(string $text, string $languageFrom, string $format = TtsAudioFormat::AUDIO_MP3): TtsResponse
    {

    }

    /**
     * @codeCoverageIgnore
     *
     * Get Translate API instance
     *
     * @throws InvalidConfigException
     *
     * @return TranslateApiInterface
     */
    protected function getTranslateApi(): TranslateApiInterface
    {
        return (new TranslateApiFactory())->getApi();
    }

    /**
     * @codeCoverageIgnore
     *
     * Get Dictionary API instance
     *
     * @throws InvalidConfigException
     *
     * @return DictionaryApiInterface
     */
    protected function getDictionaryApi(): DictionaryApiInterface
    {
        return (new DictionaryApiFactory())->getApi();
    }

    /**
     * @codeCoverageIgnore
     *
     * Get Tts API instance
     *
     * @throws InvalidConfigException
     *
     * @return TtsApiInterface
     */
    protected function getTtsApi(): TtsApiInterface
    {
        return (new TtsApiFactory())->getApi();
    }
}
