<?php

/**
 * KaikMedia PagesModule
 *
 * @package    KaikmediaPagesModule
 * @author     Kaik <contact@kaikmedia.com>
 * @copyright  KaikMedia
 * @license    http://www.php.net/license/3_0.txt  PHP License 3.0
 * @link       https://github.com/Kaik/KaikMediaPages.git
 */

namespace Kaikmedia\PagesModule\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PageType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
//        // this assumes that the entity manager was passed in as an optio
//        $em = ServiceUtil::getService('doctrine.entitymanager');
//        // $entityManager = $options['em'];
//        $transformer = new UserToIdTransformer($em);
        $builder->setMethod('POST')
            ->add('online', 'choice', [
            'choices' => [
                '0' => 'Offline',
                '1' => 'Online'
            ],
            'multiple' => false,
            'expanded' => true,
            'required' => true
        ])
            ->add('images', 'hidden', [
                'mapped' => false,
            ])

            ->add('depot', 'choice', [
            'choices' => [
                '0' => 'Depot',
                '1' => 'Allowed'
            ],
            'multiple' => false,
            'expanded' => true,
            'required' => true
        ])
            ->add('inmenu', 'choice', [
            'choices' => [
                '0' => 'Not in menus',
                '1' => 'In menus'
            ],
            'multiple' => false,
            'expanded' => true,
            'required' => true
        ])
            ->add('inlist', 'choice', [
            'choices' => [
                '0' => 'Not in list',
                '1' => 'In List'
            ],
            'multiple' => false,
            'expanded' => true,
            'required' => true
        ])
            ->add('title', 'text', [
            'required' => false
        ])
            ->add('urltitle', 'text', [
            'required' => false
        ])
//            ->add($builder->create('author', 'text', ['attr' => ['class' => 'author_search'],
//            'required' => false])
//            ->addModelTransformer($transformer))

            ->add('views', 'text', [
            'required' => false ])

            ->add('publishedAt', 'datetime', [
            'format' => \IntlDateFormatter::SHORT,
            'input' => 'datetime',
            'required' => false,
            'widget' => 'single_text'
        ])
            ->add('expiredAt', 'datetime', [
            'format' => \IntlDateFormatter::SHORT,
            'input' => 'datetime',
            'required' => false,
            'widget' => 'single_text'
        ])
            ->add('layout', 'choice', [
            'choices' => [
                'default' => 'Default',
                'slider' => 'Slider'
            ],
            'required' => false
        ])
            ->add('language', 'choice', [
            'choices' => [
                'all' => 'All',
                'en' => 'English',
                'pl' => 'Polish'
            ],'required' => false
        ])
            ->add('content', 'textarea', [
            'required' => false,
            'attr' => [
                'class' => 'tinymce'
            ]
        ])
            ->add('description', 'textarea', [
            'required' => false,
            'attr' => [
                'class' => 'tinymc'
            ]
        ])
            ->add('save', 'submit', [
            'label' => 'Save'
        ]);
    }

    public function getName()
    {
        return 'pageform';
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'title' => null,
            'content' => null
        ]);
    }
}