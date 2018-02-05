<?php

namespace GinoPane\PHPolyglot;

use GinoPane\PHPolyglot\API\Response\TTS\TtsResponse;
use GinoPane\PHPolyglot\Supplemental\GetApiInstancesTrait;
use GinoPane\PHPolyglot\Supplemental\Language\Language;
use GinoPane\PHPolyglot\API\Supplemental\TTS\TtsAudioFormat;
use GinoPane\PHPolyglot\API\Response\Translate\TranslateResponse;
use GinoPane\PHPolyglot\API\Response\Dictionary\DictionaryResponse;
use GinoPane\PHPolyglot\API\Response\SpellCheck\SpellCheckResponse;

define(__NAMESPACE__ . '\ROOT_DIRECTORY', dirname(__FILE__));

/**
 * PHPolyglot
 * Easily translate, do spell check and speak-out texts in different languages
 *
 * @author Sergey <Gino Pane> Karavay
 */
class PHPolyglot
{
    use GetApiInstancesTrait;

    /**
     * @param string $text
     * @param string $languageTo
     * @param string $languageFrom
     *
     * @return TranslateResponse
     */
    public function translate(string $text, string $languageTo, string $languageFrom = ''): TranslateResponse
    {
        return $this->getTranslateApi()->translate($text, new Language($languageTo), new Language($languageFrom));
    }

    /**
     * @param array  $text
     * @param string $languageTo
     * @param string $languageFrom
     *
     * @return TranslateResponse
     */
    public function translateBulk(array $text, string $languageTo, string $languageFrom = ''): TranslateResponse
    {
        return $this->getTranslateApi()->translateBulk($text, new Language($languageTo), new Language($languageFrom));
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
     * @return DictionaryResponse
     */
    public function lookup(string $text, string $languageFrom, string $languageTo = ''): DictionaryResponse
    {
        if ($languageTo) {
            $response = $this->getDictionaryApi()->getTranslateAlternatives(
                $text, //@codeCoverageIgnore
                new Language($languageTo),
                new Language($languageFrom)
            );
        } else {
            $response = $this->getDictionaryApi()->getTextAlternatives($text, new Language($languageFrom));
        }

        return $response;
    }

    /**
     * @param string $text
     * @param string $languageFrom
     * @param string $audioFormat
     * @param array  $additionalData
     *
     * @return TtsResponse
     */
    public function speak(
        string $text,
        string $languageFrom,
        string $audioFormat = TtsAudioFormat::AUDIO_MP3,
        array $additionalData = []
    ): TtsResponse {
        $languageFrom = new Language($languageFrom);

        return $this
            ->getTtsApi($additionalData)
            ->textToSpeech($text, new Language($languageFrom), new TtsAudioFormat($audioFormat), $additionalData);
    }

    /**
     * @param string $text
     * @param string $languageFrom
     *
     * @return SpellCheckResponse
     */
    public function spellCheck(string $text, string $languageFrom = ''): SpellCheckResponse
    {
        return $this->getSpellCheckApi()->checkTexts([$text], new Language($languageFrom));
    }

    /**
     * @param array  $texts
     * @param string $languageFrom
     *
     * @return SpellCheckResponse
     */
    public function spellCheckBulk(array $texts, string $languageFrom = ''): SpellCheckResponse
    {
        return $this->getSpellCheckApi()->checkTexts($texts, new Language($languageFrom));
    }
}
