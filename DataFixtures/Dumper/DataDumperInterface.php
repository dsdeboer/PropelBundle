<?php

/**
 * This file is part of the PropelBundle package.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @license    MIT License
 */

namespace Propel\Bundle\PropelBundle\DataFixtures\Dumper;

/**
 * Interface that exposes how Propel data dumpers should work.
 *
 * @author Duncan de Boer <duncan@charpand.nl>
 */
interface DataDumperInterface
{
    /**
     * Dumps data to fixtures from a given connection.
     *
     * @param string $filename The file name to write data.
     * @param string $connectionName The Propel connection name.
     */
    public function dump($filename, $connectionName = null);
}
