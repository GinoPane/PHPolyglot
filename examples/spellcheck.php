<?php

require_once __DIR__ . '/../vendor/autoload.php';

use GinoPane\PHPolyglot\PHPolyglot;

try {
    $phpolyglot = new PHPolyglot();

    $textsToCheck = ['Helo werld', 'Thanxs for ussing thas API'];

    $languageFrom = 'en';

    $corrections = $phpolyglot->spellCheckBulk($textsToCheck, $languageFrom)->getCorrections();

    $correctionsCount = count($corrections);

    for ($i = 0; $i < count($corrections); $i++) {
        printf("Errors in \"%s\":\n", $textsToCheck[$i]);

        foreach ($corrections[$i] as $invalidWord => $correctedWords) {
            printf("\t%s - %s;\n", $invalidWord, implode(',', $correctedWords));
        }

        echo "\n";
    }
} catch (Exception $exception) {
    $errorMessage = $exception->getMessage();

    echo sprintf("Error happened: %s", $errorMessage);
}

echo PHP_EOL;