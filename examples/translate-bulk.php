<?php

require_once __DIR__ . '/../vendor/autoload.php';

use GinoPane\PHPolyglot\PHPolyglot;

try {
    $phpolyglot = new PHPolyglot();

    $textToTranslate = ['Hi!', 'I am PHPolyglot - an easy-to-use library for translation', 'Happy coding!'];

    $languages = ['it', 'de', 'es', 'ru', 'fi', 'be', 'en'];
    $languageFrom = 'en';

    foreach ($languages as $languageTo) {
        $response = $phpolyglot->translateBulk($textToTranslate, $languageTo, $languageFrom);

        echo $languageTo . PHP_EOL;
        echo $response . PHP_EOL;
        echo PHP_EOL;
    }
} catch (Exception $exception) {
    $errorMessage = $exception->getMessage();

    echo sprintf("Error happened: %s", $errorMessage);
}

echo PHP_EOL;