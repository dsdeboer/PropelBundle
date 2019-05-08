<?php

namespace Propel\Bundle\PropelBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

abstract class BaseAbstractType extends AbstractType
{
    protected $options = [
        'name' => '',
    ];

    public function __construct($mergeOptions = null)
    {
        if ($mergeOptions) {
            $this->mergeOptions($mergeOptions);
        }
    }

    public function mergeOptions($options)
    {
        $this->options = array_merge($this->options, $options);
    }

    public function setOption($name, $value)
    {
        $this->options[$name] = $value;
    }

    public function getOptions()
    {
        return $this->options;
    }

    public function setOptions($options)
    {
        $this->options = $options;
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults($this->options);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->getOption('name');
    }

    public function getOption($name)
    {
        return $this->options[$name];
    }
}
