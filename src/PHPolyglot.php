<?php

namespace GinoPane\PHPolyglot;

define('ROOT_DIRECTORY', dirname(__FILE__, 2));

/**
 *  PHPolyglot
 *
 *  Easily translate, do spell check and speak-out texts in different languages
 *
 *  @author Sergey <Gino Pane> Karavay
 */
class PHPolyglot
{
    public function from(string $language): PHPolyglot
    {
        return $this;
    }

    public function to(string $language): PHPolyglot
    {
        return $this;
    }

    public function withLookup(bool $enableLookup): PHPolyglot
    {
        return $this;
    }

    public function usingVoice(string $voice): PHPolyglot
    {
        return $this;
    }

    public function translate()
    {

    }

    public function translateBulk()
    {

    }

    public function speak()
    {

    }

    public function lookup()
    {

    }
}
