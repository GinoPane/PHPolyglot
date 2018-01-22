<?php

namespace GinoPane\PHPolyglot\Exception;

/**
 * Class InvalidGenderCodeException
 *
 * @author Sergey <Gino Pane> Karavay
 */
class InvalidGenderCodeException extends \Exception
{
    /**
     * InvalidGenderCodeException constructor
     *
     * @param string $genderCode
     */
    public function __construct(string $genderCode)
    {
        parent::__construct(sprintf("Gender code \"%s\" is invalid", $genderCode));
    }
}
