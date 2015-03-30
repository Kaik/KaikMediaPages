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
use Symfony\Component\Form\Extension\Core\ChoiceList\ChoiceList;

class PageType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $em = ServiceUtil::getService('doctrine.entitymanager');
        $builder
            ->setMethod('POST')
            ->add('online', 'choice', array('choices' => array('0' => 'Offline','1' => 'Online'),
                                            'multiple' => false,
                                            'expanded' => true,
                                            'required' => true))              
            ->add('depot', 'choice', array('choices' => array('0' => 'Depot','1' => 'Allowed'),
                                            'multiple' => false,
                                            'expanded' => true,
                                            'required' => true))
            ->add('inmenu', 'choice', array('choices' => array('0' => 'Not in menus','1' => 'In menus'),
                                            'multiple' => false,
                                            'expanded' => true,
                                            'required' => true))              
            ->add('inlist', 'choice', array('choices' => array('0' => 'Not in list','1' => 'In List'),
                                            'multiple' => false,
                                            'expanded' => true,
                                            'required' => true))                 
            ->add('title', 'text', array('required'  => false))
                
            ->add('publishedAt', 'date', array('format' => \IntlDateFormatter::SHORT,
                                             'input' => 'datetime',
                                             'required'  => false,
                                             'widget' => 'single_text'))
            ->add('expiredAt', 'date', array('format' => \IntlDateFormatter::SHORT,
                                             'input' => 'datetime',
                                             'required'  => false,
                                             'widget' => 'single_text'))
            ->add('language', 'text', array('required'  => false))
            ->add('layout', 'text', array('required'  => false))              
            ->add('content', 'textarea', array('required'  => false, 'attr' => array('cols' => '5', 'rows' => '25')))            
            ->add('save', 'submit', array('label' => 'Save'));
    }

    public function getName()
    {
        return 'pageform';
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
            'title' => null,
            'content' => null          
        ));
    }
}