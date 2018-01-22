<?php

namespace GinoPane\PHPolyglot\Exception;

/**
 * Class InvalidAudioFormatCodeException
 *
 * @author Sergey <Gino Pane> Karavay
 */
class InvalidAudioFormatCodeException extends \Exception
{
    /**
     * InvalidVoiceCodeException constructor
     *
     * @param string $audioFormat
     */
    public function __construct(string $audioFormat)
    {
        parent::__construct(sprintf("Audio format \"%s\" is invalid", $audioFormat));
    }
}
