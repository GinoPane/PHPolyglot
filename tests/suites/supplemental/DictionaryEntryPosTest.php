<?php

namespace GinoPane\PHPolyglot;

use GinoPane\PHPolyglot\API\Response\Dictionary\POS\DictionaryEntryPos;

/**
 * Corresponding class to test Dictionary POS class
 *
 * @author Sergey <Gino Pane> Karavay
 */
class DictionaryEntryPosTest extends PHPolyglotTestCase
{
    /**
     * Just check if the DictionaryPos object can be created
     */
    public function testIfRootObjectCanBeCreated()
    {
        $object = new DictionaryEntryPos();

        $this->assertTrue($object instanceof DictionaryEntryPos);
    }

    /**
     * @dataProvider getPosCodes
     *
     * @param string $code
     * @param string $expectedCode
     */
    public function testIfCodeCheckWorksCorrectly(
        string $code,
        string $expectedCode
    ) {
        $this->assertEquals($expectedCode, (new DictionaryEntryPos($code))->getPos());
    }

    /**
     * @return array
     */
    public function getPosCodes(): array
    {
        return [
            ['noun', DictionaryEntryPos::POS_NOUN],
            ['Verb', DictionaryEntryPos::POS_VERB],
            ['conjuction', DictionaryEntryPos::POS_CONJUCTION],
            ['', DictionaryEntryPos::POS_UNDEFINED],
            ['some name', DictionaryEntryPos::POS_UNDEFINED]
        ];
    }
}
