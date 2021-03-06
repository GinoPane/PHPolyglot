<?php

require_once __DIR__ . '/../vendor/autoload.php';

use GinoPane\PHPolyglot\PHPolyglot;

try {
    $phpolyglot = new PHPolyglot();

    $textToTranslate = 'Hello world';

    $languages = ['it', 'de', 'es', 'ru', 'fi', 'be', 'en'];
    $languageFrom = 'en';

    foreach ($languages as $languageTo) {
        $response = $phpolyglot->translate($textToTranslate, $languageTo, $languageFrom);

        echo sprintf(
            "%s (%s) => (%s) %s\n",
            $textToTranslate,
            $languageFrom,
            $languageTo,
            $response
        );

        $textToTranslate = $response->getTranslations()[0];

        $languageFrom = $languageTo;
    }
} catch (Exception $exception) {
    $errorMessage = $exception->getMessage();

    echo sprintf("Error happened: %s", $errorMessage);
}

echo PHP_EOL;