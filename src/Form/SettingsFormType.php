<?php

namespace App\Form;

use App\Entity\Settings;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class SettingsFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('title', TextType::class, array(
            'label' => "Titre*",
            'attr' => array(
                'placeholder' => "Nom du titre"
            ),
            'required' => true
        ));

        $builder->add('metaTitle', TextType::class, array(
            'label' => "Méta titre*",
            'attr' => array(
                'placeholder' => "Nom du méta titre"
            ),
            'required' => true
        ));

        $builder->add('metaDescription', TextareaType::class, array(
            'label' => "Méta description*",
            'attr' => array(
                'placeholder' => "Ecrivez votre méta description"
            ),
            'required' => true
        ));

        $builder->add('isInMaintenance', ChoiceType::class, array(
            'label' => "Maintenance",
            'choices' => [
                0 => false,
                1 => true
            ],
            'expanded' => true,
            'multiple' => false,
            'required' => true,
        ));

        $builder->add('save', SubmitType::class, array(
            'label' => 'Modifier',
            'attr' => array(
                'class' => 'button black',
            ),
        ));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => Settings::class,
        ));
    }
}