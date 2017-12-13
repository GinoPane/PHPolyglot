<?php

namespace GinoPane\PHPolyglot;

use GinoPane\PHPolyglot\API\Factory\Translate\TranslateApiFactory;
use GinoPane\PHPolyglot\API\Implementation\Translate\TranslateApiInterface;

/**
*  Corresponding class to test TranslateApiFactory class
*
*  @author Sergey <Gino Pane> Karavay
*/
class TranslateApiFactoryTest extends PHPolyglotTestCase
{
    /**
     * Just check if the TranslateApiFactory can be created
     */
    public function testIfTranslateApiFactoryObjectCanBeCreated()
    {
        $object = new TranslateApiFactory();

        $this->assertTrue($object instanceof TranslateApiFactory);
    }

    public function testIfTranslateApiCanBeCreatedByFactory()
    {
        $translateApi = (new TranslateApiFactory())->getApi();

        $this->assertTrue($translateApi instanceof TranslateApiInterface);
    }
}
