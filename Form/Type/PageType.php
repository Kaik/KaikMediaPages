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
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
//use Symfony\Component\Form\Extension\Core\Type\IntegerType;
//use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
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
            ->add('online', ChoiceType::class, [
            'choices' => [
                '0' => 'Offline',
                '1' => 'Online'
            ],
            'multiple' => false,
            'expanded' => true,
            'required' => true
        ])
            ->add('images', HiddenType::class, [
                'mapped' => false,
            ])

            ->add('depot', ChoiceType::class, [
            'choices' => [
                '0' => 'Depot',
                '1' => 'Allowed'
            ],
            'multiple' => false,
            'expanded' => true,
            'required' => true
        ])
            ->add('inmenu', ChoiceType::class, [
            'choices' => [
                '0' => 'Not in menus',
                '1' => 'In menus'
            ],
            'multiple' => false,
            'expanded' => true,
            'required' => true
        ])
            ->add('inlist', ChoiceType::class, [
            'choices' => [
                '0' => 'Not in list',
                '1' => 'In List'
            ],
            'multiple' => false,
            'expanded' => true,
            'required' => true
        ])
            ->add('title', TextType::class, [
            'required' => false
        ])
            ->add('urltitle', TextType::class, [
            'required' => false
        ])
//            ->add($builder->create('author', 'text', ['attr' => ['class' => 'author_search'],
//            'required' => false])
//            ->addModelTransformer($transformer))

            ->add('views', TextType::class, [
            'required' => false ])

            ->add('publishedAt', DateType::class, [
            'format' => \IntlDateFormatter::SHORT,
            'input' => 'datetime',
            'required' => false,
            'widget' => 'single_text'
        ])
            ->add('expiredAt', DateType::class, [
            'format' => \IntlDateFormatter::SHORT,
            'input' => 'datetime',
            'required' => false,
            'widget' => 'single_text'
        ])
            ->add('layout', ChoiceType::class, [
            'choices' => [
                'default' => 'Default',
                'slider' => 'Slider'
            ],
            'required' => false
        ])
            ->add('language', ChoiceType::class, [
            'choices' => [
                'all' => 'All',
                'en' => 'English',
                'pl' => 'Polish'
            ],'required' => false
        ])
            ->add('content', TextareaType::class, [
            'required' => false,
            'attr' => [
                'class' => 'tinymce'
            ]
        ])
            ->add('description', TextareaType::class, [
            'required' => false,
            'attr' => [
                'class' => 'tinymc'
            ]
        ])
            ->add('save', SubmitType::class, [
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