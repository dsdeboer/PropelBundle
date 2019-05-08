<?php

/**
 * This file is part of the PropelBundle package.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @license    MIT License
 */

namespace Propel\Bundle\PropelBundle\DependencyInjection;

/**
 * Properties.
 *
 * @author Duncan de Boer <duncan@charpand.nl>
 */
class Properties
{
    /**
     * Build properties.
     *
     * @var array
     */
    private $properties;

    /**
     * Default constructor.
     *
     * @param $properties   An array of properties.
     */
    public function __construct(array $properties = [])
    {
        $this->properties = $properties;
    }

    /**
     * Get properties.
     *
     * @return array An array of properties.
     */
    public function getProperties()
    {
        return $this->properties;
    }
}
