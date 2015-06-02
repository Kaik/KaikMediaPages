<?php
/**
 * Copyright (c) KaikMedia.com 2014
 */
namespace Kaikmedia\PagesModule\Form\Type;

use ServiceUtil;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\Extension\Core\ChoiceList\ChoiceList;
use Kaikmedia\PagesModule\Form\DataTransformer\UserToIdTransformer;
use Kaikmedia\PagesModule\Form\Type\ImageType;

class PageType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        // this assumes that the entity manager was passed in as an optio
        $em = ServiceUtil::getService('doctrine.entitymanager');
        // $entityManager = $options['em'];
        $transformer = new UserToIdTransformer($em);
        $builder->setMethod('POST')
            ->add('online', 'choice', array(
            'choices' => array(
                '0' => 'Offline',
                '1' => 'Online'
            ),
            'multiple' => false,
            'expanded' => true,
            'required' => true
        ))
            ->add('depot', 'choice', array(
            'choices' => array(
                '0' => 'Depot',
                '1' => 'Allowed'
            ),
            'multiple' => false,
            'expanded' => true,
            'required' => true
        ))
            ->add('inmenu', 'choice', array(
            'choices' => array(
                '0' => 'Not in menus',
                '1' => 'In menus'
            ),
            'multiple' => false,
            'expanded' => true,
            'required' => true
        ))
            ->add('inlist', 'choice', array(
            'choices' => array(
                '0' => 'Not in list',
                '1' => 'In List'
            ),
            'multiple' => false,
            'expanded' => true,
            'required' => true
        ))
            ->add('title', 'text', array(
            'required' => false
        ))
            ->add('urltitle', 'text', array(
            'required' => false
        ))
            ->add($builder->create('author', 'text', [
            'attr' => [
                'class' => 'author_search'
            ]
        ])
            ->addModelTransformer($transformer))
            ->add('views', 'text', array(
            'required' => false
        ))
            ->add('publishedAt', 'datetime', array(
            'format' => \IntlDateFormatter::SHORT,
            'input' => 'datetime',
            'required' => false,
            'widget' => 'single_text'
        ))
            ->add('expiredAt', 'datetime', array(
            'format' => \IntlDateFormatter::SHORT,
            'input' => 'datetime',
            'required' => false,
            'widget' => 'single_text'
        ))
            ->add('images', 'collection', array(
            'type' => new ImageType(),
            'allow_add' => true,
            'required' => false,
            'delete_empty' => true,
            'by_reference' => false,
            'allow_delete' => true,
            'prototype' => true
        ))
            ->add('layout', 'choice', array(
            'choices' => array(
                'default' => 'Default',
                'slider' => 'Slider'
            ),
            'preferred_choices' => array(
                'default'
            ),
            'required' => false
        ))
            ->add('language', 'choice', array(
            'choices' => array(
                'any' => 'Any',
                'en' => 'English',
                'pl' => 'Polish'
            )
        ))
            ->add('content', 'textarea', array(
            'required' => false,
            'attr' => array(
                'class' => 'tinymce'
            )
        ))
            -> // Skip it if you want to use default theme))
add('description', 'textarea', array(
            'required' => false,
            'attr' => array(
                'class' => 'tinymc'
            )
        ))
            -> // Skip it if you want to use default theme))
add('save', 'submit', array(
            'label' => 'Save'
        ));
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