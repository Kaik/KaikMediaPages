<?php
/**
 * Copyright (c) KaikMedia.com 2014
 */
namespace Kaikmedia\PagesModule\Form\Type;

use ServiceUtil;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class SettingsType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $em = ServiceUtil::getService('doctrine.entitymanager');
        $builder->setMethod('GET')
            ->add('itemsperpage', 'text', array(
            'required' => false,
            'data' => $options['itemsperpage']
        ))
            ->add('images_max_count', 'text', array(
            'required' => false,
            'data' => $options['images_max_count']
        ))
            ->add('images_max_size', 'text', array(
            'required' => false,
            'data' => $options['images_max_size']
        ))
            ->add('images_ext_allowed', 'text', array(
            'required' => false,
            'data' => $options['images_ext_allowed']
        ))
            ->add('save', 'submit', array(
            'label' => 'Save'
        ));
    }

    public function getName()
    {
        return 'settingsform';
    }

    /**
     * OptionsResolverInterface is @deprecated and is supposed to be replaced by
     * OptionsResolver but docs not clear on implementation
     * 
     * @param OptionsResolverInterface $resolver            
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'itemsperpage' => null,
            'images_max_count' => null,
            'images_max_size' => null,
            'images_ext_allowed' => null
        ));
    }
}