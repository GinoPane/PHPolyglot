<?php

namespace GinoPane\PHPolyglot\API\Factory\Translate;

use GinoPane\PHPolyglot\API\Factory\ApiFactoryAbstract;
use GinoPane\PHPolyglot\Exception\InvalidConfigException;
use GinoPane\PHPolyglot\API\Implementation\TtsApiInterface;
use GinoPane\PHPolyglot\Exception\InvalidApiClassException;

class TtsApiFactory extends ApiFactoryAbstract
{
    /**
     * Config section name that is being checked for existence. API-specific properties must
     * be located under that section
     *
     * @var string
     */
    protected $configSectionName = 'ttsApi';

    /**
     * Gets necessary Dictionary API object
     *
     * @throws InvalidConfigException
     *
     * @return TtsApiInterface
     */
    public function getApi(): TtsApiInterface
    {
        return parent::getApi();
    }

    /**
     * Performs basic validation of config for Tts API
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
            TtsApiInterface::class,
            class_implements($apiClass, true)
        )) {
            throw new InvalidApiClassException(
                sprintf("Class %s must implement %s interface", $apiClass, TtsApiInterface::class)
            );
        }
    }
}
