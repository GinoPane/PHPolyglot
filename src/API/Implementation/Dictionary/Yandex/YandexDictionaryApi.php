<?php

namespace GinoPane\PHPolyglot\API\Implementation\Dictionary\Yandex;

use GinoPane\PHPolyglot\API\Supplemental\Yandex\YandexApiErrorsTrait;
use GinoPane\PHPolyglot\API\Implementation\Dictionary\DictionaryApiAbstract;

/**
 * Class YandexDictionaryApi
 *
 * @author Sergey <Gino Pane> Karavay
 */
class YandexDictionaryApi extends DictionaryApiAbstract
{
    /**
     * Main API endpoint
     *
     * @var string
     */
    protected $apiEndpoint = 'https://dictionary.yandex.net/api/v1/dicservice.json';

    /**
     * API key required for calls
     *
     * @var string
     */
    protected $apiKey = '';

    /**
     * Mapping of properties to environment variables which must supply these properties
     *
     * @var array
     */
    protected $envProperties = [
        'apiKey' => 'YANDEX_DICTIONARY_API_KEY'
    ];

    use YandexApiErrorsTrait;
}
