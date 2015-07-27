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
            'itemsperpage' => null
        ));
    }
}