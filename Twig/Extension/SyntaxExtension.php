<?php

/**
 * This file is part of the PropelBundle package.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @license    MIT License
 */

namespace Propel\Bundle\PropelBundle\Twig\Extension;

/**
 * SyntaxExtension class
 *
 * @package PropelBundle
 * @subpackage Extension
 * @author Duncan de Boer <duncan@charpand.nl>
 */
class SyntaxExtension extends \Twig_Extension
{
    public function getFilters()
    {
        return [
            new \Twig_SimpleFilter('format_sql', [$this, 'formatSQL'], ['is_safe' => ['html']]),
        ];
    }

    public function getName()
    {
        return 'propel_syntax_extension';
    }

    public function formatSQL($sql)
    {
        // list of keywords to prepend a newline in output
        $newlines = [
            'FROM',
            '(((FULL|LEFT|RIGHT)? ?(OUTER|INNER)?|CROSS|NATURAL)? JOIN)',
            'VALUES',
            'WHERE',
            'ORDER BY',
            'GROUP BY',
            'HAVING',
            'LIMIT',
        ];

        // list of keywords to highlight
        $keywords = array_merge($newlines, [
            // base
            'SELECT',
            'UPDATE',
            'DELETE',
            'INSERT',
            'REPLACE',
            'SET',
            'INTO',
            'AS',
            'DISTINCT',

            // most used methods
            'COUNT',
            'AVG',
            'MIN',
            'MAX',

            // joins
            'ON',
            'USING',

            // where clause
            '(IS (NOT)?)?NULL',
            '(NOT )?IN',
            '(NOT )?I?LIKE',
            'AND',
            'OR',
            'XOR',
            'BETWEEN',

            // order, group, limit ..
            'ASC',
            'DESC',
            'OFFSET',
        ]);

        $sql = preg_replace([
            '/\b(' . implode('|', $newlines) . ')\b/',
            '/\b(' . implode('|', $keywords) . ')\b/',
            '/(\/\*.*\*\/)/',
            '/(`[^`.]*`)/',
            '/(([0-9a-zA-Z$_]+)\.([0-9a-zA-Z$_]+))/',
        ], [
            '<br />\\1',
            '<span class="SQLKeyword">\\1</span>',
            '<span class="SQLComment">\\1</span>',
            '<span class="SQLName">\\1</span>',
            '<span class="SQLName">\\1</span>',
        ], $sql);

        return $sql;
    }
}
