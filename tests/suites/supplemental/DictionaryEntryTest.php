<?php

namespace GinoPane\PHPolyglot;

use GinoPane\PHPolyglot\API\Response\Dictionary\Entry\DictionaryEntry;
use GinoPane\PHPolyglot\API\Response\Dictionary\Entry\POS\DictionaryEntryPos;

/**
 * Corresponding class to test Dictionary POS class
 *
 * @author Sergey <Gino Pane> Karavay
 */
class DictionaryEntryTest extends PHPolyglotTestCase
{
    /**
     * Just check if the DictionaryEntry object can be created
     */
    public function testIfRootObjectCanBeCreated()
    {
        $object = new DictionaryEntry();

        $this->assertTrue($object instanceof DictionaryEntry);
    }

    /**
     * @dataProvider getEntryData
     *
     * @param array $data
     */
    public function testIfDictionaryEntryDataCanBeFilled(array $data)
    {
        $entry = (new DictionaryEntry());

        foreach ($data as $property => $value) {
            $entry->{sprintf("set%s", ucfirst($property))}($value);
        }

        foreach ($data as $property => $value) {
            $result = $entry->{sprintf("get%s", ucfirst($property))}();

            $this->assertEquals($value, $result);
        }
    }

    public function testIfDictionaryEntryPosCanBeSet()
    {
        $entry = (new DictionaryEntry());

        $this->assertEquals(DictionaryEntryPos::POS_UNDEFINED, $entry->getPosFrom());
        $this->assertEquals(DictionaryEntryPos::POS_UNDEFINED, $entry->getPosTo());

        $posFrom = 'noun';
        $posTo = 'adjective';

        $entry->setPosFrom(new DictionaryEntryPos($posFrom));
        $entry->setPosTo(new DictionaryEntryPos($posTo));

        $this->assertEquals($posFrom, $entry->getPosFrom());
        $this->assertEquals($posTo, $entry->getPosTo());
    }

    /**
     * @return array
     */
    public function getEntryData(): array
    {
        return [
            [
                [
                    'textFrom' => 'hello',
                    'textTo' => 'прывітанне',
                    'transcription' => 'ˈheˈləʊ',
                    'synonyms' => ['hi'],
                    'meanings' => ['greeting'],
                    'examples' => ['Hello world!' => "Привет, Мир!"]
                ]
            ]
        ];
    }
}
