<?php
/**
 * Copyright (c) KaikMedia.com 2014
 */
namespace Kaikmedia\PagesModule\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ImageType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('name', 'text', array(
            'required' => false
        ))
            ->add('path', 'text', array(
            'required' => false
        ))
            ->add('description', 'textarea', array(
            'required' => false
        ))
            ->add('legal', 'textarea', array(
            'required' => false
        ))
            ->add('publicdomain', 'checkbox', array(
            'label' => 'public',
            'required' => false
        ))
            ->add('promoted', 'checkbox', array(
            'label' => 'promoted',
            'required' => false
        ))
            ->add('file', 'file', array(
            'required' => false
        ));
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'isXmlHttpRequest' => false,
            'data_class' => 'Kaikmedia\PagesModule\Entity\ImageEntity'
        ));
    }

    public function getName()
    {
        return 'images';
    }
}