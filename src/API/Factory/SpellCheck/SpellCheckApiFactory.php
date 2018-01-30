<?php

namespace GinoPane\PHPolyglot\API\Factory\SpellCheck;

use GinoPane\PHPolyglot\API\Factory\ApiFactoryAbstract;
use GinoPane\PHPolyglot\API\Implementation\SpellCheck\SpellCheckApiInterface;

class SpellCheckApiFactory extends ApiFactoryAbstract
{
    /**
     * Config section name that is being checked for existence. API-specific properties must
     * be located under that section
     *
     * @var string
     */
    protected $configSectionName = 'spellCheckApi';

    /**
     * API interface that must be implemented by API class
     *
     * @var string
     */
    protected $apiInterface = SpellCheckApiInterface::class;

    /**
     * Gets necessary SpellCheck API object
     *
     * @param array $parameters
     *
     * @return SpellCheckApiInterface
     */
    public function getApi(array $parameters = []): SpellCheckApiInterface
    {
        return parent::getApi($parameters);
    }
}
