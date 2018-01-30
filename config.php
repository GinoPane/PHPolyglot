<?php

return [
    'translateApi' => [
        'default' => \GinoPane\PHPolyglot\API\Implementation\Translate\Yandex\YandexTranslateApi::class
    ],
    'dictionaryApi' => [
        'default' => \GinoPane\PHPolyglot\API\Implementation\Dictionary\Yandex\YandexDictionaryApi::class
    ],
    'ttsApi' => [
        'default' => \GinoPane\PHPolyglot\API\Implementation\TTS\IbmWatson\IbmWatsonTtsApi::class,
        'directory' => 'media'
    ],
    'spellCheckApi' => [
        'default' => \GinoPane\PHPolyglot\API\Implementation\SpellCheck\Yandex\YandexSpellCheckApi::class
    ]
];
