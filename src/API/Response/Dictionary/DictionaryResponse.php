<?php

namespace GinoPane\PHPolyglot\API\Response\Dictionary;

use GinoPane\PHPolyglot\API\Response\ApiResponseAbstract;
use GinoPane\PHPolyglot\API\Response\Dictionary\Entry\DictionaryEntry;

/**
 * Class DictionaryResponse
 *
 * @author Sergey <Gino Pane> Karavay
 */
class DictionaryResponse extends ApiResponseAbstract
{
    /**
     * @var DictionaryEntry[]
     */
    protected $data;

    /**
     * @param DictionaryEntry $entry
     *
     * @return DictionaryResponse
     */
    public function addEntry(DictionaryEntry $entry): DictionaryResponse
    {
        $this->data[] = $entry;

        return $this;
    }

    /**
     * Returns an array of saved entries
     *
     * @return DictionaryEntry[]
     */
    public function getEntries()
    {
        return (array)parent::getData();
    }
}
