<?php

namespace GinoPane\PHPolyglot\API\Response\Dictionary\Entry;

/**
 * Class DictionaryEntry
 *
 * DictionaryEntry provides detailed information about a word
 *
 * @author Sergey <Gino Pane> Karavay
 */
class DictionaryEntry
{
    /**
     * The source language used to build the dictionary entry
     *
     * @var string
     */
    private $languageFrom = '';

    /**
     * The target language used to build the dictionary entry
     *
     * @var string
     */
    private $languageTo = '';

    /**
     * A string containing the source text for current dictionary entry
     *
     * @var string
     */
    private $textFrom = '';

    /**
     * A string containing the translated text or possible text variant if the languages are the same
     *
     * @var string
     */
    private $textTo = '';

    /**
     * A string containing the transcription of the text if applicable
     *
     * @var string
     */
    private $transcription = '';

    /**
     * Part of speech of dictionary entry word
     *
     * @var string
     */
    private $pos = '';

    /**
     * An array of synonyms of the source text
     *
     * @var array
     */
    private $synonyms = [];

    /**
     * An array of strings describing the meaning of the text
     *
     * @var array
     */
    private $meanings = [];

    /**
     * An associative array of usage examples. The format is:
     *      [
     *          text_in_language_from => text_in_language_to,
     *          ...
     *      ]
     *
     * @var array
     */
    private $examples = [];

    /**
     * @return string
     */
    public function getLanguageFrom(): string
    {
        return $this->languageFrom;
    }

    /**
     * @param string $languageFrom
     *
     * @return DictionaryEntry
     */
    public function setLanguageFrom(string $languageFrom): DictionaryEntry
    {
        $this->languageFrom = $languageFrom;

        return $this;
    }

    /**
     * @return string
     */
    public function getLanguageTo(): string
    {
        return $this->languageTo;
    }

    /**
     * @param string $languageTo
     *
     * @return DictionaryEntry
     */
    public function setLanguageTo(string $languageTo): DictionaryEntry
    {
        $this->languageTo = $languageTo;

        return $this;
    }

    /**
     * @return string
     */
    public function getTextFrom(): string
    {
        return $this->textFrom;
    }

    /**
     * @param string $textFrom
     *
     * @return DictionaryEntry
     */
    public function setTextFrom(string $textFrom): DictionaryEntry
    {
        $this->textFrom = $textFrom;

        return $this;
    }

    /**
     * @return string
     */
    public function getTextTo(): string
    {
        return $this->textTo;
    }

    /**
     * @param string $textTo
     *
     * @return DictionaryEntry
     */
    public function setTextTo(string $textTo): DictionaryEntry
    {
        $this->textTo = $textTo;

        return $this;
    }

    /**
     * @return string
     */
    public function getTranscription(): string
    {
        return $this->transcription;
    }

    /**
     * @param string $transcription
     *
     * @return DictionaryEntry
     */
    public function setTranscription(string $transcription): DictionaryEntry
    {
        $this->transcription = $transcription;

        return $this;
    }

    /**
     * @return array
     */
    public function getSynonyms(): array
    {
        return $this->synonyms;
    }

    /**
     * @param array $synonyms
     *
     * @return DictionaryEntry
     */
    public function setSynonyms(array $synonyms): DictionaryEntry
    {
        $this->synonyms = $synonyms;

        return $this;
    }

    /**
     * @return array
     */
    public function getMeanings(): array
    {
        return $this->meanings;
    }

    /**
     * @param array $meanings
     *
     * @return DictionaryEntry
     */
    public function setMeanings(array $meanings): DictionaryEntry
    {
        $this->meanings = $meanings;

        return $this;
    }

    /**
     * @return array
     */
    public function getExamples(): array
    {
        return $this->examples;
    }

    /**
     * @param array $examples
     *
     * @return DictionaryEntry
     */
    public function setExamples(array $examples): DictionaryEntry
    {
        $this->examples = $examples;

        return $this;
    }
}
