<?php

namespace GinoPane\PHPolyglot\API\Response\Translate;

use GinoPane\PHPolyglot\API\Response\ApiResponseAbstract;

/**
 * Class TranslateApiResponse
 */
class TranslateApiResponse extends ApiResponseAbstract
{
    /**
     * @var string
     */
    private $languageFrom = '';

    /**
     * @var string
     */
    private $languageTo = '';

    /**
     * Returns an array of translations
     *
     * @return array
     */
    public function getTranslations(): array
    {
        return array_values((array)$this->data);
    }

    /**
     * Sets an array of translations
     *
     * @param array $translations
     */
    public function setTranslations(array $translations)
    {
        $this->setData($translations);
    }

    /**
     * Returns source language string
     *
     * @return string
     */
    public function getLanguageFrom(): string
    {
        return $this->languageFrom;
    }

    /**
     * Sets source language string
     *
     * @param string $languageFrom
     */
    public function setLanguageFrom(string $languageFrom)
    {
        $this->languageFrom = $languageFrom;
    }

    /**
     * Returns target language string
     *
     * @return string
     */
    public function getLanguageTo(): string
    {
        return $this->languageTo;
    }

    /**
     * Sets target language string
     *
     * @param string $languageTo
     */
    public function setLanguageTo(string $languageTo)
    {
        $this->languageTo = $languageTo;
    }

    /**
     * Returns string representation of translation.
     * Please note that for bulk response it will return imploded translations divided by newlines
     *
     * @return string
     */
    public function __toString(): string
    {
        $translations = $this->getTranslations();

        if (count($translations) == 1) {
            return $translations[0];
        } else {
            return implode(PHP_EOL, $translations);
        }
    }
}
