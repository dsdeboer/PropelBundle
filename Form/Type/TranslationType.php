<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Propel\Bundle\PropelBundle\Form\Type;

use Propel\Bundle\PropelBundle\Form\EventListener\TranslationFormListener;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Translation type class.
 *
 * @author Patrick Kaufmann
 */
class TranslationType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addEventSubscriber(
            new TranslationFormListener($options['columns'], $options['data_class'])
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'propel1_translation';
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setRequired([
            'data_class',
            'columns',
        ]);
    }
}
