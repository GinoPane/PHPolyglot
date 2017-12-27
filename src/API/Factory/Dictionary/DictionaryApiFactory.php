<?php

namespace GinoPane\PHPolyglot\API\Factory\Dictionary;

use GinoPane\PHPolyglot\API\Factory\ApiFactoryAbstract;
use GinoPane\PHPolyglot\Exception\InvalidConfigException;
use GinoPane\PHPolyglot\Exception\InvalidApiClassException;
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
     * Gets necessary Dictionary API object
     *
     * @throws InvalidConfigException
     *
     * @return DictionaryApiInterface
     */
    public function getApi(): DictionaryApiInterface
    {
        return parent::getApi();
    }

    /**
     * Performs basic validation of config for Dictionary API
     *
     * @throws InvalidConfigException
     *
     * @throws InvalidApiClassException
     */
    protected function assertConfigIsValid(): void
    {
        parent::assertConfigIsValid();

        $apiClass = $this->getFactorySpecificConfig()['class'];

        if (!in_array(
            DictionaryApiInterface::class,
            class_implements($apiClass, true)
        )) {
            throw new InvalidApiClassException(
                sprintf("Class %s must implement %s interface", $apiClass, DictionaryApiInterface::class)
            );
        }
    }
}
