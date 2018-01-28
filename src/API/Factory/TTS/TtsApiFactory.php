<?php

namespace GinoPane\PHPolyglot\API\Factory\TTS;

use GinoPane\PHPolyglot\API\Factory\ApiFactoryAbstract;
use GinoPane\PHPolyglot\Exception\InvalidConfigException;
use GinoPane\PHPolyglot\Exception\InvalidApiClassException;
use GinoPane\PHPolyglot\API\Implementation\TTS\TtsApiInterface;
use GinoPane\PHPolyglot\Exception\InvalidPathException;

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
     * Config properties that must exist for valid config
     *
     * @var array
     */
    protected $configProperties = [
        'default',
        'directory'
    ];

    /**
     * Gets necessary Dictionary API object
     *
     * @param array $parameters
     *
     * @return TtsApiInterface
     */
    public function getApi(array $parameters = []): TtsApiInterface
    {
        return parent::getApi($parameters);
    }

    /**
     * @return string
     *
     * @throws InvalidPathException
     */
    public function getTargetDirectory(): string
    {
        $directoryName = $this->getRootRelatedPath($this->getFactorySpecificConfig()['directory']);

        $this->assertDirectoryIsWriteable($directoryName);

        return $directoryName;
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
