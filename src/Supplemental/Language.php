<?php

namespace GinoPane\PHPolyglot\Supplemental;

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
        return array_search($code, $this->getConstants()) !== false;
    }
}
