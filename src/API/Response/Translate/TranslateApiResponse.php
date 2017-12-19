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
     * @return array
     */
    public function getTranslations(): array
    {
        return (array)$this->data;
    }

    /**
     * @param array $translations
     */
    public function setTranslations(array $translations)
    {
        $this->setData($translations);
    }

    /**
     * @return string
     */
    public function getLanguageFrom(): string
    {
        return $this->languageFrom;
    }

    /**
     * @param string $languageFrom
     */
    public function setLanguageFrom(string $languageFrom)
    {
        $this->languageFrom = $languageFrom;
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
     */
    public function setLanguageTo(string $languageTo)
    {
        $this->languageTo = $languageTo;
    }
}
