<?php

return [
    'translateApi' => [
        'default' => \GinoPane\PHPolyglot\API\Factory\Translate\TranslateApiFactory::class
    ],
    'dictionaryApi' => [
        'default' => \GinoPane\PHPolyglot\API\Factory\Dictionary\DictionaryApiFactory::class
    ],
    'ttsApi' => [
        'default' => \GinoPane\PHPolyglot\API\Factory\TTS\TtsApiFactory::class
    ],
];
