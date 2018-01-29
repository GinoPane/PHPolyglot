<?php

namespace GinoPane\PHPolyglot\API\Factory\Specllcheck;

use GinoPane\PHPolyglot\API\Factory\ApiFactoryAbstract;

class SpecllcheckApiFactory extends ApiFactoryAbstract
{
    /**
     * Config section name that is being checked for existence. API-specific properties must
     * be located under that section
     *
     * @var string
     */
    protected $configSectionName = 'specllcheckApi';

    /**
     * API interface that must be implemented by API class
     *
     * @var string
     */
    protected $apiInterface = SpecllcheckApiInterface::class;

    /**
     * Gets necessary Specllcheck API object
     *
     * @param array $parameters
     *
     * @return SpecllcheckApiInterface
     */
    public function getApi(array $parameters = []): SpecllcheckApiInterface
    {
        return parent::getApi($parameters);
    }
}
