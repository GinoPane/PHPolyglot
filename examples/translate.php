<?php

require_once __DIR__.'/../vendor/autoload.php';

use GinoPane\PHPolyglot\PHPolyglot;

try {
    $phpolyglot = new PHPolyglot();

    echo $phpolyglot->translate('Hello world', 'it', 'en')->getTranslations()[0];
} catch (Exception $exception) {
    $errorMessage = $exception->getMessage();

    echo sprintf("Error happened: %s", $errorMessage);
}