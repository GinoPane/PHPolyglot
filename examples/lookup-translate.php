<?php

require_once __DIR__ . '/../vendor/autoload.php';

use GinoPane\PHPolyglot\PHPolyglot;

try {
    $phpolyglot = new PHPolyglot();

    $textToLookup = 'Hello!';

    $languageFrom = 'en';
    $languageTo = 'ru';

    $response = $phpolyglot->lookup($textToLookup, $languageFrom, $languageTo)->getEntries();

    if (empty($response)) {
        throw new Exception('Nothing returned! Maybe API has changed?');
    }

    echo "Word to translate: $textToLookup \n";

    echo "Translations: \n";

    foreach ($response as $entry) {
        echo $entry->getTextTo();

        if ($meanings = $entry->getMeanings()) {
            echo " (" . implode(", ", $meanings) . ")";
        }

        echo "\n";
    }
} catch (Exception $exception) {
    $errorMessage = $exception->getMessage();

    echo sprintf("Error happened: %s", $errorMessage);
}

echo PHP_EOL;