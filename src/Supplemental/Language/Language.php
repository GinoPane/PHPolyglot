<?php

namespace GinoPane\PHPolyglot\Supplemental\Language;

use GinoPane\PHPolyglot\Exception\InvalidLanguageCodeException;
use GinoPane\PHPolyglot\Supplemental\GetConstantsTrait;

/**
 * Class Language
 *
 * @author Sergey <Gino Pane> Karavay
 */
class Language implements LanguageInterface
{
    use GetConstantsTrait;

    /**
     * Checks if code is valid
     *
     * @param string $code
     *
     * @return bool
     */
    public function codeIsValid(string $code): bool
    {
        return $this->constantValueExists($code);
    }

    /**
     * Translate requests usually require target language, but do not require the language for source text,
     * that's why this method allows validation for both cases of provided language list
     *
     * @param string $languageTo
     * @param string $languageFrom
     *
     * @throws InvalidLanguageCodeException
     *
     * @return array
     */
    public function getLanguagesForTranslation(string $languageTo, string $languageFrom): array
    {
        if (!empty($languageFrom)) {
            list($languageTo, $languageFrom) = $this->sanitizeLanguages([$languageTo, $languageFrom]);
        } else {
            list($languageTo) = $this->sanitizeLanguages([$languageTo]);
        }

        return array($languageTo, $languageFrom);
    }

    /**
     * Checks that language codes are valid and also transforms them
     *
     * @param array $languages
     *
     * @throws InvalidLanguageCodeException
     *
     * @return array
     */
    private function sanitizeLanguages(array $languages): array
    {
        $languages = array_map('strtolower', $languages);

        $this->assertLanguagesAreValid($languages);

        return $languages;
    }

    /**
     * Checks that specified language codes are valid
     *
     * @param array $languages
     *
     * @throws InvalidLanguageCodeException
     */
    private function assertLanguagesAreValid(array $languages): void
    {
        foreach ($languages as $language) {
            if (!$this->codeIsValid($language)) {
                throw new InvalidLanguageCodeException(
                    sprintf("Language code \"%s\" is invalid", $language)
                );
            }
        }
    }
}
