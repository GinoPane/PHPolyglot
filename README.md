PHPolyglot
============

<sub>Powered by [composer-package-template](https://github.com/GinoPane/composer-package-template)</sub>

[![Latest Stable Version](https://poser.pugx.org/gino-pane/phpolyglot/v/stable)](https://packagist.org/packages/gino-pane/phpolyglot)
[![License](https://poser.pugx.org/gino-pane/phpolyglot/license)](https://packagist.org/packages/gino-pane/phpolyglot)
[![composer.lock](https://poser.pugx.org/gino-pane/phpolyglot/composerlock)](https://packagist.org/packages/gino-pane/phpolyglot)
[![Total Downloads](https://poser.pugx.org/gino-pane/phpolyglot/downloads)](https://packagist.org/packages/gino-pane/phpolyglot)

Requirements
------------

* PHP >= 7.1;
* credentials for Yandex Translate API and IBM Watson API.

Features
--------

Installation
============

    composer require gino-pane/phpolyglot
    
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

Also please refer to [CONTRIBUTION.md](https://github.com/GinoPane/PHPolyglot/blob/master/CONTRIBUTION.md).

License
=======

Please refer to [LICENSE](https://github.com/GinoPane/PHPolyglot/blob/master/LICENSE).

Useful Tools
============

Code sniffer tool:
------------------

 ```php vendor/squizlabs/php_codesniffer/scripts/phpcs -s --report-full=phpcs.txt --standard=PSR2 src/```

Code auto-fixer:
----------------

 ```php vendor/squizlabs/php_codesniffer/scripts/phpcbf --standard=PSR2 src/```    
 
PhpUnit:
--------

 ```php vendor/phpunit/phpunit/phpunit -c build/phpunit.xml```
