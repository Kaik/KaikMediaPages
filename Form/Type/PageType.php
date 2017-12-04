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

use Kaikmedia\PagesModule\Entity\CategoryAssignmentEntity;
use Kaikmedia\PagesModule\Entity\PageEntity;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Zikula\Bundle\FormExtensionBundle\Form\DataTransformer\NullToEmptyTransformer;
use Zikula\CategoriesModule\Form\Type\CategoriesType;
use Zikula\UsersModule\Form\Type\UserLiveSearchType;

class PageType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        ->add('online', ChoiceType::class, [
            'choices' => [
                'Offline' => '0',
                'Online' => '1'
            ],
            'multiple' => false,
            'expanded' => true,
            'required' => true
        ])
        ->add('depot', ChoiceType::class, [
            'choices' => [
                'Depot' => 1,
                'Allowed' => 0
            ],
            'multiple' => false,
            'expanded' => true,
            'required' => true
        ])
        ->add('inmenu', ChoiceType::class, [
            'choices' => [
                'Not in menus' => '0',
                'In menus' => '1'
            ],
            'multiple' => false,
            'expanded' => true,
            'required' => true
        ])
        ->add('inlist', ChoiceType::class, [
            'choices' => [
                'Not in list' => '0',
                'In List' => '1'
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
        ->add('categoryAssignments', CategoriesType::class, [
            'required' => true,
            'multiple' => false,
            'module' => 'KaikmediaPagesModule',
            'entity' => 'PageEntity',
            'entityCategoryClass' => CategoryAssignmentEntity::class,
        ])
        ->add('views', TextType::class, [
            'required' => false
            ])
        ->add('publishedAt', DateTimeType::class, [
            'required' => false,
            'date_widget' => 'single_text',
            'time_widget' => 'single_text',
        ])
        ->add('expiredAt', DateTimeType::class, [
            'required' => false,
            'date_widget' => 'single_text',
            'time_widget' => 'single_text',
        ])
        ->add('layout', ChoiceType::class, [
            'choices' => $options['layouts'],
            'required' => true
        ])
        ->add($builder->create('language', ChoiceType::class, [
                    'choices' => $options['locales'],
                    'required' => false,
                ])->addModelTransformer(new NullToEmptyTransformer())
            )
        ->add('content', TextareaType::class, [
            'required' => false,
        ])
        ->add('description', TextareaType::class, [
            'required' => false,
        ])
        ->add('save', SubmitType::class, [
            'label' => 'Save'
        ]);
        $builder->add('author', UserLiveSearchType::class, [
            'mapped' => true,
            'attr' => [
                'maxlength' => 11,
            ],
            'empty_data' => 0,
            'inline_usage' => true,
            'required' => true,
        ]);
    }

    public function getName()
    {
        return 'kaikmediapagesmodule_page';
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => PageEntity::class,
            'locales' => ['English' => 'en'],
            'layouts' => ['Default' => 'default']
        ]);
    }
}
