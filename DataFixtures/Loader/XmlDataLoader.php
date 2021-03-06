<?php

/**
 * This file is part of the PropelBundle package.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @license    MIT License
 */

namespace Propel\Bundle\PropelBundle\DataFixtures\Loader;

/**
 * XML fixtures loader.
 *
 * @author Duncan de Boer <duncan@charpand.nl>
 */
class XmlDataLoader extends AbstractDataLoader
{
    /**
     * {@inheritdoc}
     */
    protected function transformDataToArray($file)
    {
        $xml = simplexml_load_string(file_get_contents($file));

        return $this->simpleXmlToArray($xml);
    }

    /**
     * @param SimpleXMLElement $xml
     * @return array
     */
    protected function simpleXmlToArray($xml)
    {
        $array = [];
        if ($xml instanceof \SimpleXMLElement) {
            foreach ($xml as $key => $value) {
                // First make a valid key which is the Ns (Namespace) attribute
                // + the element name (the class name)
                foreach ($value->attributes() as $subkey => $subvalue) {
                    if ('Namespace' === (string)$subkey) {
                        $key = $subvalue . '\\' . $key;
                        break;
                    }
                }

                $array[$key] = [];
                foreach ($value as $elementKey => $elementValue) {
                    $array[$key][$elementKey] = [];

                    foreach ($elementValue->attributes() as $subkey => $subvalue) {
                        $array[$key][$elementKey][$subkey] = (string)$subvalue;
                    }
                }
            }
        }

        return $array;
    }
}
