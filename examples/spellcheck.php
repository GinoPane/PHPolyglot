<?php

require_once __DIR__.'/../vendor/autoload.php';

use GinoPane\PHPolyglot\PHPolyglot;

try {
    $phpolyglot = new PHPolyglot();

    $textsToCheck = ['Helo werld', 'Thanxs for ussing thas API'];

    $languageFrom = 'en';

    $corrections = $phpolyglot->spellCheckTexts($textsToCheck, $languageFrom)->getCorrections();

    print_r($corrections);
} catch (Exception $exception) {
    $errorMessage = $exception->getMessage();

    echo sprintf("Error happened: %s", $errorMessage);
}

echo PHP_EOL;