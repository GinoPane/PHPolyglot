<?php

namespace GinoPane\PHPolyglot\Exception;

/**
 * Class InvalidVoiceCodeException
 *
 * @author Sergey <Gino Pane> Karavay
 */
class InvalidVoiceCodeException extends \Exception
{
    /**
     * InvalidVoiceCodeException constructor
     *
     * @param string $voiceCode
     */
    public function __construct(string $voiceCode)
    {
        parent::__construct(sprintf("Voice code \"%s\" is invalid", $voiceCode));
    }
}
