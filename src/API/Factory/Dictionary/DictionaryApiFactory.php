<?php

namespace GinoPane\PHPolyglot\API\Factory\Dictionary;

use GinoPane\PHPolyglot\API\Factory\ApiFactoryAbstract;
use GinoPane\PHPolyglot\API\Implementation\Dictionary\DictionaryApiInterface;

class DictionaryApiFactory extends ApiFactoryAbstract
{
    /**
     * Config section name that is being checked for existence. API-specific properties must
     * be located under that section
     *
     * @var string
     */
    protected $configSectionName = 'dictionaryApi';

    /**
     * API interface that must be implemented by API class
     *
     * @var string
     */
    protected $apiInterface = DictionaryApiInterface::class;

    /**
     * Gets necessary Dictionary API object
     *
     * @param array $parameters
     *
     * @return DictionaryApiInterface
     */
    public function getApi(array $parameters = []): DictionaryApiInterface
    {
        return parent::getApi($parameters);
    }
}
