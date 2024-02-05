<?php 

// src/Form/EditProfilFormType.php
namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class EditProfilFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder->add('firstName', TextType::class, array(
            'label' => 'Prénom*',
            'attr' => array(
                'placeholder' => 'Jean'
            ),
            'required' => true
        ));
        
        $builder->add('lastName', TextType::class, array(
            'label' => 'Nom*',
            'attr' => array(
                'placeholder' => 'DUPONT'
            ),
            'required' => true
        ));

        $builder->add('phone', TextType::class, array(
            'label' => 'Téléphone*',
            'attr' => array(
                'placeholder' => '0725582631'
            ),
            'required' => true
        ));

        $builder->add('email', EmailType::class, array(
            'label' => 'Email*',
            'attr' => array(
                'placeholder' => 'j.dupont@mail.com'
            ),
            'required' => true
        ));

        $builder->add('dateEntry', DateType::class, array(
            'label' => "Date de création de l'entreprise*",
            'widget' => 'single_text',
            'html5' => false,
            'required' => true
        ));
        
        $builder->add('job', TextType::class, array(
            'label' => 'Métier*',
            'attr' => array(
                'placeholder' => 'Product Owner'
            ),
            'required' => true
        ));
        
        $builder->add('country', TextType::class, array(
            'label' => 'Pays*',
            'attr' => array(
                'placeholder' => 'France'
            ),
            'required' => true
        ));

        $builder->add('save', SubmitType::class, array(
            'label' => 'Modifier',
            'attr' => array('class' => 'button black')
        ));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => User::class,
        ));
    }
}