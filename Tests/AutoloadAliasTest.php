<?php

namespace Propel\Bundle\PropelBundle\Tests;

use PHPUnit\Framework\TestCase as PhpUnitTestCase;

class AutoloadAliasTest extends PhpUnitTestCase
{
    public function testOldNamespaceWorks()
    {
        $inflector = new \Propel\PropelBundle\Util\PropelInflector();

        static::assertInstanceOf('Propel\PropelBundle\Util\PropelInflector', $inflector);
        static::assertInstanceOf('Propel\Bundle\PropelBundle\Util\PropelInflector', $inflector);
    }
}
