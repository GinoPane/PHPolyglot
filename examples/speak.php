<?php

require_once __DIR__.'/../vendor/autoload.php';

use GinoPane\PHPolyglot\PHPolyglot;

try {
    $phpolyglot = new PHPolyglot();

    $textToSpeak = 'Hello world';

    $languageFrom = 'en';

    echo sprintf("File stored '%s' \n", $phpolyglot->speak($textToSpeak, $languageFrom)->storeFile());
    echo sprintf("File stored '%s' \n",
        $phpolyglot->speak($textToSpeak, $languageFrom, 'flac', ['gender' => 'f'])->storeFile()
    );
    echo sprintf("File stored '%s' \n",
        $phpolyglot->speak($textToSpeak, $languageFrom, 'ogg', ['voice' => 'en-US_MichaelVoice'])->storeFile()
    );
} catch (Exception $exception) {
    $errorMessage = $exception->getMessage();

    echo sprintf("Error happened: %s", $errorMessage);
}

echo PHP_EOL;