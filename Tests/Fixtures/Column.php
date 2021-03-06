<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Propel\Bundle\PropelBundle\Tests\Fixtures;

class Column extends \ColumnMap
{
    protected $type;
    private   $name;

    public function __construct($name, $type)
    {
        $this->name    = $name;
        $this->type    = $type;
        $this->phpName = ucfirst($name);
    }

    public function getSize()
    {
        return $this->isText() ? 255 : 0;
    }

    public function isText()
    {
        if (!$this->type) {
            return false;
        }

        switch ($this->type) {
            case \PropelColumnTypes::CHAR:
            case \PropelColumnTypes::VARCHAR:
            case \PropelColumnTypes::LONGVARCHAR:
            case \PropelColumnTypes::BLOB:
            case \PropelColumnTypes::CLOB:
            case \PropelColumnTypes::CLOB_EMU:
                return true;
        }

        return false;
    }

    public function isNotNull()
    {
        return 'id' === $this->name;
    }
}
