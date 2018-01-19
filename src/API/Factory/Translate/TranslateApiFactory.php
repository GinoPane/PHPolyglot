<?php

namespace GinoPane\PHPolyglot\API\Factory\Translate;

use GinoPane\PHPolyglot\API\Factory\ApiFactoryAbstract;
use GinoPane\PHPolyglot\Exception\InvalidApiClassException;
use GinoPane\PHPolyglot\API\Implementation\Translate\TranslateApiInterface;
use GinoPane\PHPolyglot\Exception\InvalidConfigException;

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

    /**
     * Performs basic validation of config for Translate API
     *
     * @throws InvalidConfigException
     *
     * @throws InvalidApiClassException
     */
    protected function assertConfigIsValid(): void
    {
        parent::assertConfigIsValid();

        $apiClass = $this->getFactorySpecificConfig()['default'];

        if (!in_array(
            TranslateApiInterface::class,
            class_implements($apiClass, true)
        )) {
            throw new InvalidApiClassException(
                sprintf("Class %s must implement %s interface", $apiClass, TranslateApiInterface::class)
            );
        }
    }
}
