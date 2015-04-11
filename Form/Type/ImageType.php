<?php

namespace Kaikmedia\PagesModule\Form\Type;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;


class ImageType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
              ->add('name','text', array('required' => false))
              ->add('path','text', array('required' => false))
              ->add('file', 'file', array('required' => false));

    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Kaikmedia\PagesModule\Entity\ImageEntity',
        ));
    }

    public function getName()
    {
        return 'images';
    }
}