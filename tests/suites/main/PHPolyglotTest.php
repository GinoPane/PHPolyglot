<?php 

namespace GinoPane\PHPolyglot;

use PHPUnit\Framework\TestCase;

/**
*  Corresponding class to test PHPolyglot class
*
*  @author Sergey <Gino Pane> Karavay
*/
class PHPolyglotTest extends TestCase
{
    /**
     * Just check if the PHPolyglot can be created
     */
    public function testIfRootObjectCanBeCreated()
    {
        $object = new PHPolyglot();

        $this->assertTrue(is_object($object));
    }
}
