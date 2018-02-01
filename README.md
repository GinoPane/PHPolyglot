PHPolyglot
==========

[![Latest Stable Version](https://poser.pugx.org/gino-pane/phpolyglot/v/stable)](https://packagist.org/packages/gino-pane/phpolyglot)
[![Build Status](https://travis-ci.org/GinoPane/PHPolyglot.svg?branch=master)](https://travis-ci.org/GinoPane/PHPolyglot)
[![Maintainability](https://api.codeclimate.com/v1/badges/b1e2b6042612f67f7e13/maintainability)](https://codeclimate.com/github/GinoPane/PHPolyglot/maintainability)
[![Test Coverage](https://api.codeclimate.com/v1/badges/b1e2b6042612f67f7e13/test_coverage)](https://codeclimate.com/github/GinoPane/PHPolyglot/test_coverage)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/GinoPane/phpolyglot/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/GinoPane/phpolyglot/?branch=master)
[![License](https://poser.pugx.org/gino-pane/phpolyglot/license)](https://packagist.org/packages/gino-pane/phpolyglot)
[![composer.lock](https://poser.pugx.org/gino-pane/phpolyglot/composerlock)](https://packagist.org/packages/gino-pane/phpolyglot)
[![Total Downloads](https://poser.pugx.org/gino-pane/phpolyglot/downloads)](https://packagist.org/packages/gino-pane/phpolyglot)

Combining and featuring different APIs for language translation, dictionary lookup, spelling correction and speech synthesis (TTS) in an easy to use and extend way.

Table of Contents
-----------------
* [General Information](#phpolyglot)
    * [Features](#features)
    * [Requirements](#requirements)
    * [Installation](#installation)
* [Basic Usage](#basic-usage)
    * [Translation](#translation)
        * [Yandex Translate API](#yandex-translate-api)
    * [Dictionary Lookup](#dictionary-lookup)
        * [Yandex Dictionary API](#yandex-dictionary-api)
    * [Spelling Check](#spelling-check)
        * [Yandex Speller API](#yandex-speller-api)
    * [Speech Synthesis](#speech-synthesis)
        * [IBM Watson Text-to-Speech](#ibm-watson-text-to-speech)
* [Possible ToDos](#possible-todos)
* [Useful Tools](#useful-tools)
    * [Running Tests](#running-tests)
    * [Code Sniffer Tool](#code-sniffer-tool)
    * [Code Auto-fixer](#code-auto-fixer)
    * [Building Docs](#building-docs)
* [Changelog](#changelog)
* [Contributing](#contributing)
* [License](#license)
* [Notes](#notes)    

Features
--------

* provides an easy-to-use way to utilise different language-related APIs for translation, grammar correction, TTS, etc.;
* custom APIs can be easily added, because the package heavily relies on implementation of different interfaces, therefore it is easy to plug-in (pull requests are appreciated);
* open or free (possibly with limitations) APIs are preferred;
* language codes must be [ISO-639](https://www.loc.gov/standards/iso639-2/php/code_list.php) compatible (alpha-2 or alpha-3 if there's no alpha-2);
* third-party APIs may contain their own limitations or licensing requirements (see [License](#license))

Requirements
------------

* PHP >= 7.1;
* credentials for Yandex Translate API, Yandex Dictionary API and IBM Watson API (depending on what you are going to use).

Installation
------------
```
composer require gino-pane/phpolyglot
```

Create a copy of `.env.example` file, name it `.env` and put your own API credentials in it. File contains links to pages which may be related to required credentials.

> In order to run examples from [examples](https://github.com/GinoPane/PHPolyglot/blob/master/examples) directory you have to specify your own valid API credentials.
    
Basic Usage
===========

The package contains a plenty of ready-to-use examples in [examples](https://github.com/GinoPane/PHPolyglot/blob/master/examples) directory.
All endpoints either return a valid response or throws a relevant exception.
All APIs are configured through [config.php](https://github.com/GinoPane/PHPolyglot/blob/master/config.php) file which contains the default API classes mapping. Dynamic configs are not supported yet, but they are listed in [possible todos](#possible-todos).

Translation
-----------

There are two endpoints. For a single string:

```
function translate(string $text, string $languageTo, string $languageFrom = ''): TranslateResponse
```

and for multiple strings:

```
function translateBulk(array $text, string $languageTo, string $languageFrom = ''): TranslateResponse
```

As a minimum example you can pass text and language to translate into (source language will be detected by API):

```
$response = (new PHPolyglot())->translate('Hello world', 'it')->getTranslations(); // [ 0 => Ciao mondo ]
```

`TranslateResponse` has `getTranslations` method which returns an array of translations.

Supported languages may vary depending on third-party API.

### Yandex Translate API

Please check the [list of supported languages](https://tech.yandex.com/translate/doc/dg/concepts/api-overview-docpage/#languages).
[Yandex Translate API](https://tech.yandex.com/translate/doc/dg/concepts/api-overview-docpage/) is free to use with [limitations](https://translate.yandex.com/developers/prices) (1000 000 characters per day, up to 10 000 000 per month). If you want you can get a paid plan of course. The API won't let you to get into paid plan automatically, it will simply return an error when the limit is reached.
In order to use the API you need to get the valid [API key](https://tech.yandex.com/keys/?service=trnsl).

Dictionary Lookup
-----------------

There a single endpoint, which can be used in two different forms.

For a lookup within the same language (get word forms):

```
function lookup(string $text, string $languageFrom): DictionaryResponse
```

and for translation-with-lookup (get multiple translations and additional information including word forms, examples, meanings, synonyms, transcription, etc.):

```
function lookup(string $text, string $languageFrom, string $languageTo): DictionaryResponse
```

As a minimum example you can pass text and its source language:

```
$response = (new PHPolyglot)->lookup('Hello', 'en)->getEntries();

$synonyms = implode(", ", $response[0]->getSynonyms());

$output = <<<TEXT
Initial word: {$response[0]->getTextFrom()}

Part of speech: {$response[0]->getPosFrom()}
Transcription: {$response[0]->getTranscription()}

Main alternative: {$response[0]->getTextTo()}
Synonyms: {$synonyms}
TEXT

echo $output

/**
Initial word: hello
  
Part of speech: noun
Transcription: ˈheˈləʊ

Main alternative: hi
Synonyms: hallo, salut
*/
```

Supported languages may vary depending on third-party API.

### Yandex Dictionary API

Please check the [list of supported languages](https://tech.yandex.com/dictionary/doc/dg/reference/getLangs-docpage/).
[Yandex Dictionary API](https://tech.yandex.com/dictionary/doc/dg/concepts/api-overview-docpage/) is free to use with [limitations](https://yandex.com/legal/dictionary_api/?lang=en) (up to 10 000 references per day).
In order to use the API you need to get the valid [API key](https://tech.yandex.com/keys/?service=dict).


Spelling Check
--------------

There are two endpoints. For a single string:

```
function spellCheck(string $text, string $languageFrom = ''): SpellCheckResponse
```

and for multiple strings:

```
function spellCheckBulk(array $texts, string $languageFrom = ''): SpellCheckResponse
```

As a minimum example you can pass only a text to check:

```
$corrections = $phpolyglot->spellCheckText('Helo werld', $languageFrom)->getCorrections();

/**
array(1) {
  [0] =>
  array(2) {
    'Helo' =>
    array(1) {
      [0] =>
      string(5) "Hello"
    }
    'werld' =>
    array(1) {
      [0] =>
      string(5) "world"
    }
  }
}
*/

```

Supported languages may vary depending on third-party API.

### Yandex Speller API

Please check the [list of supported languages](https://tech.yandex.ru/speller/doc/dg/concepts/speller-overview-docpage/) (basically, only English, Russian and Ukrainian are supported at the moment).
[Yandex Speller API](https://tech.yandex.ru/speller/doc/dg/concepts/api-overview-docpage/) is free to use with [limitations](https://yandex.ru/legal/speller_api/) (up to 10 000 calls/10 000 000 characters per day). No keys are required.

Speech Synthesis
----------------

The main endpoint is `PHPolyglot`'s `speak` method:

```
public function speak(
    string $text,
    string $languageFrom,
    string $audioFormat = TtsAudioFormat::AUDIO_MP3,
    array $additionalData = []
): TtsResponse
``` 
    
Only two parameters are required - text for synthesis `$text` and its source language `$languageFrom`. 

Optional parameters `$audioFormat` and `$additionalData` may be omitted. Audio format allows to explicitly specify the required audio format of returned audio. Additional data allows to set API specific parameters for more precise results (voice, pitch, speed, etc.).

The list of audio formats which are currently recognized:

* TtsAudioFormat::AUDIO_BASIC
* TtsAudioFormat::AUDIO_FLAC
* TtsAudioFormat::AUDIO_L16
* TtsAudioFormat::AUDIO_MP3
* TtsAudioFormat::AUDIO_MPEG
* TtsAudioFormat::AUDIO_MULAW
* TtsAudioFormat::AUDIO_OGG
* TtsAudioFormat::AUDIO_WAV
* TtsAudioFormat::AUDIO_WEBM

Please note that not all of them may be supported by your API of choice.

The TTS method returns `TtsResponse` which has `storeFile` method to store generated file with required name and extension into the specified directory (or by using default values):

```
function storeFile(string $fileName = '', string $extension = '', string $directory = ''): string
```

By default the file name is a simple `md5` hash of `$text` that was used for TTS, `$extension` is being populated based on `content-type` header (at least, for IBM Watson API), `$directory` is based on config setting.

```
(new PHPolyglot())->speak('Hello world', 'en')->storeFile(); // stores 3e25960a79dbc69b674cd4ec67a72c62.mp3
```

### IBM Watson Text-to-Speech

Please check the [list of supported languages and voices](https://console.bluemix.net/docs/services/text-to-speech/http.html#voices).
IBM Watson TTS requires API credentials for authorization. Create your TTS project [there](https://www.ibm.com/watson/services/text-to-speech/) and get your API-specific credentials. API is free to use with limitations (up to 10 000 characters per month).

Possible ToDos
==============
* transcribe words;
* get synonyms, antonyms, derivatives;
* detect text language;
* add more configuration flexibility (choose API based on config constraints, like different APIs for different languages);
* pass config override into root constructor.
    
Useful Tools
============

Running Tests:
--------------

    php vendor/bin/phpunit
 
 or 
 
    composer test

Code Sniffer Tool:
------------------

    php vendor/bin/phpcs --standard=PSR2 src/
 
 or
 
    composer psr2check

Code Auto-fixer:
----------------

    php vendor/bin/phpcbf --standard=PSR2 src/ 
    
 or
 
    composer psr2autofix
 
 
Building Docs:
-------------

    php vendor/bin/phpdoc -d "src" -t "docs"
 
 or 
 
    composer docs
    
Changelog
=========

To keep track, please refer to [CHANGELOG.md](https://github.com/GinoPane/PHPolyglot/blob/master/CHANGELOG.md).

Contributing
============

1. Fork it;
2. Create your feature branch (git checkout -b my-new-feature);
3. Make your changes;
4. Run the tests, adding new ones for your own code if necessary (phpunit);
5. Commit your changes (git commit -am 'Added some feature');
6. Push to the branch (git push origin my-new-feature);
7. Create new pull request.

Also please refer to [CONTRIBUTING.md](https://github.com/GinoPane/PHPolyglot/blob/master/CONTRIBUTING.md).

License
=======

Please refer to [LICENSE](https://github.com/GinoPane/PHPolyglot/blob/master/LICENSE).
> The [PHPolyglot](https://github.com/GinoPane/PHPolyglot) does not own any of results that APIs may return. Also, APIs may have their own rules about data usage, so beware of them when you use them.

Notes
=====

Powered by [composer-package-template](https://github.com/GinoPane/composer-package-template) and [PHP Nano Rest](https://github.com/GinoPane/php-nano-rest).
