<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Propel\Bundle\PropelBundle\Tests\Form\Type;

use Propel\Bundle\PropelBundle\Form\PropelExtension;
use Propel\Bundle\PropelBundle\Form\Type\TranslationCollectionType;
use Propel\Bundle\PropelBundle\Tests\Fixtures\Item;
use Propel\Bundle\PropelBundle\Tests\Fixtures\TranslatableItem;
use Propel\Bundle\PropelBundle\Tests\Fixtures\TranslatableItemI18n;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Test\TypeTestCase;
use Symfony\Component\Form\Form;

class TranslationCollectionTypeTest extends TypeTestCase
{
    public function testTranslationsAdded()
    {
        $item = new TranslatableItem();
        $item->addTranslatableItemI18n(new TranslatableItemI18n(1, 'fr', 'val1'));
        $item->addTranslatableItemI18n(new TranslatableItemI18n(2, 'en', 'val2'));

        $builder = $this->factory->createBuilder(FormType::class, null, [
            'data_class' => TranslatableItem::class,
        ]);

        $builder->add('translatableItemI18ns', TranslationCollectionType::class, [
            'languages' => ['en', 'fr'],
            'entry_options'   => [
                'data_class' => TranslatableItemI18n::class,
                'columns'    => ['value', 'value2' => ['label' => 'Label', 'type' => TextareaType::class]],
            ],
        ]);
        $form = $builder->getForm();
        $form->setData($item);
        $translations = $form->get('translatableItemI18ns');

        $this->assertCount(2, $translations);
        $this->assertInstanceOf(Form::class, $translations['en']);
        $this->assertInstanceOf(Form::class, $translations['fr']);

        $this->assertInstanceOf(TranslatableItemI18n::class, $translations['en']->getData());
        $this->assertInstanceOf(TranslatableItemI18n::class, $translations['fr']->getData());

        $this->assertEquals($item->getTranslation('en'), $translations['en']->getData());
        $this->assertEquals($item->getTranslation('fr'), $translations['fr']->getData());

        $columnOptions = $translations['fr']->getConfig()->getOption('columns');
        $this->assertEquals('value', $columnOptions[0]);
        $this->assertEquals(TextareaType::class, $columnOptions['value2']['type']);
        $this->assertEquals('Label', $columnOptions['value2']['label']);
    }

    public function testNotPresentTranslationsAdded()
    {
        $item = new TranslatableItem();

        $this->assertCount(0, $item->getTranslatableItemI18ns());

        $builder = $this->factory->createBuilder(FormType::class, null, [
            'data_class' => TranslatableItem::class,
        ]);
        $builder->add('translatableItemI18ns', TranslationCollectionType::class, [
            'languages' => ['en', 'fr'],
            'entry_options'   => [
                'data_class' => TranslatableItemI18n::class,
                'columns'    => ['value', 'value2' => ['label' => 'Label', 'type' => TextareaType::class]],
            ],
        ]);

        $form = $builder->getForm();
        $form->setData($item);

        $this->assertCount(2, $item->getTranslatableItemI18ns());
    }

    public function testNoArrayGiven()
    {
        $this->expectException('Symfony\Component\Form\Exception\UnexpectedTypeException');
        $item = new Item(null, 'val');

        $builder = $this->factory->createBuilder(FormType::class, null, [
            'data_class' => Item::class,
        ]);
        $builder->add('value', TranslationCollectionType::class, [
            'languages' => ['en', 'fr'],
            'entry_options'   => [
                'data_class' => TranslatableItemI18n::class,
                'columns'    => ['value', 'value2' => ['label' => 'Label', 'type' => TextareaType::class]],
            ],
        ]);

        $form = $builder->getForm();
        $form->setData($item);
    }

    public function testNoDataClassAdded()
    {
        $this->expectException('Symfony\Component\OptionsResolver\Exception\MissingOptionsException');
        $this->factory->createNamed('itemI18ns', TranslationCollectionType::class, null, [
            'languages' => ['en', 'fr'],
            'entry_options'   => [
                'columns' => ['value', 'value2'],
            ],
        ]);
    }

    public function testNoLanguagesAdded()
    {
        $this->expectException('Symfony\Component\OptionsResolver\Exception\MissingOptionsException');
        $this->factory->createNamed('itemI18ns', TranslationCollectionType::class, null, [
            'entry_options' => [
                'data_class' => TranslatableItemI18n::class,
                'columns'    => ['value', 'value2'],
            ],
        ]);
    }

    public function testNoColumnsAdded()
    {
        $this->expectException('Symfony\Component\OptionsResolver\Exception\MissingOptionsException');
        $this->factory->createNamed('itemI18ns', TranslationCollectionType::class, null, [
            'languages' => ['en', 'fr'],
            'entry_options'   => [
                'data_class' => TranslatableItemI18n::class,
            ],
        ]);
    }

    protected function getExtensions()
    {
        return [new PropelExtension()];
    }
}
