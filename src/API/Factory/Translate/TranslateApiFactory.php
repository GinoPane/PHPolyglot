<?php

namespace GinoPane\PHPolyglot\API\Factory\Translate;

use GinoPane\PHPolyglot\API\Factory\ApiFactoryAbstract;
use GinoPane\PHPolyglot\API\Implementation\Translate\TranslateApiInterface;

/**
 * Class TranslateApiFactory
 *
 * @author Sergey <Gino Pane> Karavay
 */
class TranslateApiFactory extends ApiFactoryAbstract
{
    /**
     * Config section name that is being checked for existence. API-specific properties must
     * be located under that section
     *
     * @var string
     */
    protected $configSectionName = 'translateApi';

    /**
     * API interface that must be implemented by API class
     *
     * @var string
     */
    protected $apiInterface = TranslateApiInterface::class;

    /**
     * Gets necessary Translate API object
     *
     * @param array $parameters
     *
     * @return TranslateApiInterface
     */
    public function getApi(array $parameters = []): TranslateApiInterface
    {
        return parent::getApi($parameters);
    }
}
