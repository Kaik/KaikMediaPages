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

class PageFilterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {


        $builder->add('limit', ChoiceType::class, ['choices' => ['10' => '10', '15' => '15', '25' => '25', '50' => '50', '100' => '100'], 'required' => false])
        ->add('title', TextType::class, ['required' => false])
        ->add('online', ChoiceType::class, ['choices' => ['1' => 'Online', '0' => 'Offline'], 'required' => false])
        ->add('depot', ChoiceType::class, ['choices' => ['1' => 'Allowed', '0' => 'Depot'], 'required' => false])
        ->add('inlist', ChoiceType::class, ['choices' => ['1' => 'In List', '0' => 'Not in list'], 'required' => false])
        ->add('inmenu', ChoiceType::class, ['choices' => ['1' => 'In Menu', '0' => 'Not in menu'], 'required' => false])
        //todo add language detection
        ->add('language', ChoiceType::class, ['choices' => ['any' => 'Any', 'en' => 'English', 'pl' => 'Polish'], 'required' => false])
        //todo add layout detection
        ->add('layout', ChoiceType::class, ['choices' => ['default' => 'Default', 'slider' => 'Slider'], 'required' => false])
        ->add('author', TextType::class, ['required' => false])
        ->add('filter', SubmitType::class, ['label' => 'Filter'])
        ;

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