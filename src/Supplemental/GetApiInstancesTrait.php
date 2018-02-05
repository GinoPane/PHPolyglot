<?php

namespace GinoPane\PHPolyglot\Supplemental;

use GinoPane\PHPolyglot\API\Factory\TTS\TtsApiFactory;
use GinoPane\PHPolyglot\API\Implementation\TTS\TtsApiInterface;
use GinoPane\PHPolyglot\API\Factory\Translate\TranslateApiFactory;
use GinoPane\PHPolyglot\API\Factory\Dictionary\DictionaryApiFactory;
use GinoPane\PHPolyglot\API\Factory\SpellCheck\SpellCheckApiFactory;
use GinoPane\PHPolyglot\API\Implementation\Translate\TranslateApiInterface;
use GinoPane\PHPolyglot\API\Implementation\Dictionary\DictionaryApiInterface;
use GinoPane\PHPolyglot\API\Implementation\SpellCheck\SpellCheckApiInterface;

/**
 * Trait GetApiInstancesTrait
 *
 * @author Sergey <Gino Pane> Karavay
 */
trait GetApiInstancesTrait
{
    /**
     * @codeCoverageIgnore
     *
     * Get Translate API instance
     *
     * @param array $parameters
     *
     * @return TranslateApiInterface
     */
    protected function getTranslateApi(array $parameters = []): TranslateApiInterface
    {
        return (new TranslateApiFactory())->getApi($parameters);
    }

    /**
     * @codeCoverageIgnore
     *
     * Get Dictionary API instance
     *
     * @param array $parameters
     *
     * @return DictionaryApiInterface
     */
    protected function getDictionaryApi(array $parameters = []): DictionaryApiInterface
    {
        return (new DictionaryApiFactory())->getApi($parameters);
    }

    /**
     * @codeCoverageIgnore
     *
     * Get Tts API instance
     *
     * @param array $parameters
     *
     * @return TtsApiInterface
     */
    protected function getTtsApi(array $parameters = []): TtsApiInterface
    {
        return (new TtsApiFactory())->getApi($parameters);
    }

    /**
     * @codeCoverageIgnore
     *
     * Get SpellCheck API instance
     *
     * @param array $parameters
     *
     * @return SpellCheckApiInterface
     */
    protected function getSpellCheckApi(array $parameters = []): SpellCheckApiInterface
    {
        return (new SpellCheckApiFactory())->getApi($parameters);
    }
}