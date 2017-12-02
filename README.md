PHPolyglot
============

<sub>Powered by [composer-package-template](https://github.com/GinoPane/composer-package-template)</sub>

[![Latest Stable Version](https://poser.pugx.org/gino-pane/phpolyglot/v/stable)](https://packagist.org/packages/gino-pane/composer-package-template)
[![License](https://poser.pugx.org/gino-pane/phpolyglot/license)](https://packagist.org/packages/gino-pane/composer-package-template)
[![composer.lock](https://poser.pugx.org/gino-pane/phpolyglot/composerlock)](https://packagist.org/packages/gino-pane/composer-package-template)
[![Total Downloads](https://poser.pugx.org/gino-pane/phpolyglot/downloads)](https://packagist.org/packages/gino-pane/composer-package-template)

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

To keep track, please refer to [CHANGELOG.md](https://github.com/GinoPane/composer-package-template/blob/master/CHANGELOG.md).

Contributing
============

Please refer to [CONTRIBUTION.md](https://github.com/GinoPane/composer-package-template/blob/master/CONTRIBUTION.md).

License
=======

Please refer to [LICENSE](https://github.com/GinoPane/composer-package-template/blob/master/LICENSE).

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
