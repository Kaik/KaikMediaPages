<?php
/**
 * Copyright (c) KaikMedia.com 2014
 *
 */

namespace Kaikmedia\PagesModule\Form\Type;

use ServiceUtil;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class PagesFilterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $em = ServiceUtil::getService('doctrine.entitymanager');
        $builder
            ->setMethod('GET')
            ->add('limit', 'choice', array('choices'   => array('10' => '10', '50' => '50'),
                                           'required'  => false,
                                           'data'=> $options['limit']))               
            ->add('title', 'text', array('required'  => false,
                                           'data'=> $options['title']))
            ->add('online', 'choice', array('choices'   => array('1' => 'Online', '0' => 'Offline'),
                                           'required'  => false,
                                           'data'=> $options['online']))            
            ->add('filter', 'submit', array('label' => 'Filter'));
    }

    public function getName()
    {
        return 'pagesfilterform';
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
            'limit' => null,
            'title' => null,
            'online' => null,           
        ));
    }
}