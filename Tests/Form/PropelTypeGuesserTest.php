<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Propel\Bundle\PropelBundle\Tests\Form;

use Propel\Bundle\PropelBundle\Form\PropelTypeGuesser;
use Propel\Bundle\PropelBundle\Tests\TestCase;
use Symfony\Component\Form\Guess\Guess;

class PropelTypeGuesserTest extends TestCase
{
    const CLASS_NAME         = 'Propel\Bundle\PropelBundle\Tests\Fixtures\Item';
    const UNKNOWN_CLASS_NAME = 'Propel\Bundle\PropelBundle\Tests\Fixtures\UnknownItem';

    private $guesser;

    public static function dataProviderForGuessType()
    {
        return [
            ['is_active', 'checkbox', Guess::HIGH_CONFIDENCE],
            ['enabled', 'checkbox', Guess::HIGH_CONFIDENCE],
            ['id', 'integer', Guess::MEDIUM_CONFIDENCE],
            ['value', 'text', Guess::MEDIUM_CONFIDENCE],
            ['price', 'number', Guess::MEDIUM_CONFIDENCE],
            ['updated_at', 'datetime', Guess::HIGH_CONFIDENCE],

            ['isActive', 'checkbox', Guess::HIGH_CONFIDENCE],
            ['updatedAt', 'datetime', Guess::HIGH_CONFIDENCE],

            ['Authors', 'model', Guess::HIGH_CONFIDENCE, true],
            ['Resellers', 'model', Guess::HIGH_CONFIDENCE, true],
            ['MainAuthor', 'model', Guess::HIGH_CONFIDENCE, false],
        ];
    }

    public function testGuessMaxLengthWithText()
    {
        $value = $this->guesser->guessMaxLength(self::CLASS_NAME, 'value');

        $this->assertNotNull($value);
        $this->assertEquals(255, $value->getValue());
    }

    public function testGuessMaxLengthWithFloat()
    {
        $value = $this->guesser->guessMaxLength(self::CLASS_NAME, 'price');

        $this->assertNotNull($value);
        $this->assertNull($value->getValue());
    }

    public function testGuessMinLengthWithText()
    {
        $value = $this->guesser->guessPattern(self::CLASS_NAME, 'value');

        $this->assertNull($value);
    }

    public function testGuessMinLengthWithFloat()
    {
        $value = $this->guesser->guessPattern(self::CLASS_NAME, 'price');

        $this->assertNotNull($value);
        $this->assertNull($value->getValue());
    }

    public function testGuessRequired()
    {
        $value = $this->guesser->guessRequired(self::CLASS_NAME, 'id');

        $this->assertNotNull($value);
        $this->assertTrue($value->getValue());
    }

    public function testGuessRequiredWithNullableColumn()
    {
        $value = $this->guesser->guessRequired(self::CLASS_NAME, 'value');

        $this->assertNotNull($value);
        $this->assertFalse($value->getValue());
    }

    public function testGuessTypeWithoutTable()
    {
        $value = $this->guesser->guessType(self::UNKNOWN_CLASS_NAME, 'property');

        $this->assertNotNull($value);
        $this->assertEquals('text', $value->getType());
        $this->assertEquals(Guess::LOW_CONFIDENCE, $value->getConfidence());
    }

    public function testGuessTypeWithoutColumn()
    {
        $value = $this->guesser->guessType(self::CLASS_NAME, 'property');

        $this->assertNotNull($value);
        $this->assertEquals('text', $value->getType());
        $this->assertEquals(Guess::LOW_CONFIDENCE, $value->getConfidence());
    }

    /**
     * @dataProvider dataProviderForGuessType
     */
    public function testGuessType($property, $type, $confidence, $multiple = null)
    {
        $value = $this->guesser->guessType(self::CLASS_NAME, $property);

        $this->assertNotNull($value);
        $this->assertEquals($type, $value->getType());
        $this->assertEquals($confidence, $value->getConfidence());

        if ($type === 'model') {
            $options = $value->getOptions();

            $this->assertSame($multiple, $options['multiple']);
        }
    }

    protected function setUp()
    {
        $this->guesser = new PropelTypeGuesser();
    }

    protected function tearDown()
    {
        $this->guesser = null;
    }
}
