PHPolyglot
============

[![Latest Stable Version](https://poser.pugx.org/gino-pane/phpolyglot/v/stable)](https://packagist.org/packages/gino-pane/phpolyglot)
[![License](https://poser.pugx.org/gino-pane/phpolyglot/license)](https://packagist.org/packages/gino-pane/phpolyglot)
[![composer.lock](https://poser.pugx.org/gino-pane/phpolyglot/composerlock)](https://packagist.org/packages/gino-pane/phpolyglot)
[![Total Downloads](https://poser.pugx.org/gino-pane/phpolyglot/downloads)](https://packagist.org/packages/gino-pane/phpolyglot)

Requirements
------------

* PHP >= 7.1;
* credentials for Yandex Translate API, Yandex Dictionary API and IBM Watson API (depending on what you are going to use).

Features
--------

Installation
============

    composer require gino-pane/phpolyglot
    
Basic Usage
===========

Text Translation
----------------

Speech Synthesis
----------------

The main endpoint is `PHPolyglot`'s `speak` method:

```
public function speak(
    string $text,
    string $languageFrom,
    string $format = TtsAudioFormat::AUDIO_MP3,
    array $additionalData = []
): TtsResponse
``` 
    
Possible ToDos
==============
* transcribe words;
* get synonyms, antonyms, derivatives;
* detect text language;
* add more configuration flexibility (choose api based on config constraints, like different APIs for different languages);
* pass config override into root constructor.
    
Useful Tools
============

Running Tests:
--------

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
--------

    php vendor/bin/phpdoc -d "src" -t "docs"
 
 or 
 
    composer docs
    
Changelog
=========

To keep track, please refer to [CHANGELOG.md](https://github.com/GinoPane/PHPolyglot/blob/master/CHANGELOG.md).

Contributing
============

1. Fork it.
2. Create your feature branch (git checkout -b my-new-feature).
3. Make your changes.
4. Run the tests, adding new ones for your own code if necessary (phpunit).
5. Commit your changes (git commit -am 'Added some feature').
6. Push to the branch (git push origin my-new-feature).
7. Create new pull request.

Also please refer to [CONTRIBUTING.md](https://github.com/GinoPane/PHPolyglot/blob/master/CONTRIBUTING.md).

License
=======

Please refer to [LICENSE](https://github.com/GinoPane/PHPolyglot/blob/master/LICENSE).

Notes
=====

Powered by [composer-package-template](https://github.com/GinoPane/composer-package-template)
