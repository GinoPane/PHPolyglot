<?php

namespace GinoPane\PHPolyglot;

use GinoPane\PHPolyglot\Supplemental\Language;

/**
 * Corresponding class to test Language class
 *
 * @author Sergey <Gino Pane> Karavay
 */
class LanguageTest extends PHPolyglotTestCase
{
    /**
     * Just check if the PHPolyglot can be created
     */
    public function testIfRootObjectCanBeCreated()
    {
        $object = new Language();

        $this->assertTrue($object instanceof Language);
    }

    /**
     * @dataProvider getLanguageCodes
     *
     * @param string $code
     * @param bool   $expectedResult
     */
    public function testIfCodeCheckWorksCorrectly(
        string $code,
        bool $expectedResult
    ) {
        $this->assertEquals($expectedResult, (new Language())->codeIsValid($code));
    }

    /**
     * @return array
     */
    public function getLanguageCodes(): array
    {
        return [
            ['ru', true],
            ['be', true],
            ['en', true],
            ['rus', false],
            ['I don\'t exist', false]
        ];
    }
}
