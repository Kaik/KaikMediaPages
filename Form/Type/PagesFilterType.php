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
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Zikula\Bundle\FormExtensionBundle\Form\Type\LocaleType;
use Zikula\CategoriesModule\Form\Type\CategoriesType;
use Zikula\UsersModule\Form\Type\UserLiveSearchType;

class PagesFilterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        ->add('sortby', HiddenType::class)
        ->add('sortorder', HiddenType::class)
        ->add('limit', ChoiceType::class, [
            'choices'   => [
                '5'     => '5',
                '10'    => '10',
                '15'    => '15',
                '25'    => '25',
                '50'    => '50',
                '100'   => '100'
                ],
            'required'  => true
        ])
        ->add('title', TextType::class, [
            'required'  => false
        ])
        ->add('online', ChoiceType::class, [
            'choices' => [
                'Offline'   => '0',
                'Online'    => '1'
            ],
            'required' => false
        ])
        ->add('depot', ChoiceType::class, [
            'choices' => [
                'Depot'     => '0',
                'Allowed'   => '1'
                ],
            'required' => false
        ])
        ->add('inlist', ChoiceType::class, [
            'choices' => [
                'Not in list'   => '0',
                'In List'       => '1'
                ],
            'required' => false
        ])
        ->add('inmenu', ChoiceType::class, [
            'choices' => [
                'Not in menus'  => '0',
                'In menus'      => '1'
                ],
            'required' => false
        ])
        ->add('published', ChoiceType::class, [
            'choices' => [
                'Not defined'   => 'unset',
                'Awaiting'      => 'awaiting',
                'Published'     => 'published'
                ],
            'required' => false
        ])
        ->add('expired', ChoiceType::class, [
            'choices' => [
                'Not defined'   => 'unset',
                'Awaiting'      => 'awaiting',
                'Expired'       => 'expired',
                'Published'     => 'published'
                ],
            'required' => false
        ])
        ->add('categoryAssignments', CategoriesType::class, [
            'required'              => false,
            'multiple'              => false,
            'module'                => 'KaikmediaPagesModule',
            'entity'                => 'PageEntity',
            'entityCategoryClass'   => CategoryAssignmentEntity::class,
        ])
        ->add('author', UserLiveSearchType::class, [
            'empty_data'    => 0,
            'inline_usage'  => true,
            'required'      => false,
        ])
        ->add('language', LocaleType::class, [
            'choices'   => $options['locales'],
            'required'  => false,
        ])
        ->add('layout', ChoiceType::class, [
            'choices'   => $options['layouts'],
            'required'  => false
            ])
        ->add('filter', SubmitType::class, [])
        ;
    }

    public function getName()
    {
        return 'kaikmediapagesmodule_pages_filter_form';
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'csrf_protection'   => false,
            'locales'           => ['English' => 'en'],
            'layouts'           => ['Default' => 'default']
        ]);
    }
}