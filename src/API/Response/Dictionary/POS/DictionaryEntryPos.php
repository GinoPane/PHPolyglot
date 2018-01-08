<?php

namespace GinoPane\PHPolyglot\API\Response\Dictionary\POS;

use GinoPane\PHPolyglot\Supplemental\GetConstantsTrait;

/**
 * Class DictionaryPos
 *
 * Contains
 *
 * @link http://partofspeech.org/
 *
 * @author Sergey <Gino Pane> Karavay
 */
class DictionaryEntryPos
{
    const POS_UNDEFINED     = '';
    const POS_NOUN          = 'noun';
    const POS_PRONOUN       = 'pronoun';
    const POS_VERB          = 'verb';
    const POS_ADVERB        = 'adverb';
    const POS_ADJECTIVE     = 'adjective';
    const POS_CONJUCTION    = 'conjuction';
    const POS_PREPOSITION   = 'preposition';
    const POS_INTERJECTION  = 'interjection';

    /**
     * Stored part of speech
     *
     * @var string
     */
    private $pos = self::POS_UNDEFINED;

    use GetConstantsTrait;

    public function __construct(string $pos = self::POS_UNDEFINED)
    {
        $pos = strtolower($pos);

        if ($this->constantValueExists($pos)) {
            $this->pos = $pos;
        } else {
            $this->pos = self::POS_UNDEFINED;
        }
    }

    /**
     * Returns part of speech as string
     *
     * @return string
     */
    public function getPos(): string
    {
        return $this->pos;
    }
}
