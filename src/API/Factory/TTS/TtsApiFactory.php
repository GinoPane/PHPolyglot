<?php

namespace GinoPane\PHPolyglot\API\Factory\TTS;

use GinoPane\PHPolyglot\API\Factory\ApiFactoryAbstract;
use GinoPane\PHPolyglot\Exception\InvalidPathException;
use GinoPane\PHPolyglot\API\Implementation\TTS\TtsApiInterface;

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
     * API interface that must be implemented by API class
     *
     * @var string
     */
    protected $apiInterface = TtsApiInterface::class;

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
}
