<?php

require_once __DIR__.'/../vendor/autoload.php';

use GinoPane\PHPolyglot\PHPolyglot;

try {
    $phpolyglot = new PHPolyglot();

    $textToLookup = 'Hello!';

    $languageFrom = 'en';

    $response = $phpolyglot->lookup($textToLookup, $languageFrom)->getEntries();

    if (!$response) {
        throw new Exception('Nothing returned! Maybe API has changed?');
    }

    $synonyms = implode(", ", $response[0]->getSynonyms());

    $output = <<<TEXT
    Initial word: {$response[0]->getTextFrom()}
  
    Part of speech: {$response[0]->getPosFrom()}
    Transcription: {$response[0]->getTranscription()}
    
    Main alternative: {$response[0]->getTextTo()}
    Synonyms: {$synonyms}

TEXT;

    echo $output;
} catch (Exception $exception) {
    $errorMessage = $exception->getMessage();

    echo sprintf("Error happened: %s", $errorMessage);
}

echo PHP_EOL;