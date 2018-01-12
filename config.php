<?php

return [
    'translateApi' => [
        'default' => \GinoPane\PHPolyglot\API\Implementation\Translate\Yandex\YandexTranslateApi::class
    ],
    'dictionaryApi' => [
        'default' => \GinoPane\PHPolyglot\API\Implementation\Dictionary\Yandex\YandexDictionaryApi::class
    ],
];
