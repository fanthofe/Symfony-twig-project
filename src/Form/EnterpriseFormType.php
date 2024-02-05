<?php

// src/Form/EnterpriseFormType.php
namespace App\Form;

use App\Entity\Enterprise;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class EnterpriseFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $enterpriseId = $options['data']->getId();

        $builder->add('name', TextType::class, array(
            'label' => "Nom de l'entreprise*",
            'attr' => array(
                'placeholder' => 'Enterprise Sas'
            ),
            'required' => true
        ));
        
        $builder->add('expertiseField', TextType::class, array(
            'label' => "Domaine d'expertise*",
            'attr' => array(
                'placeholder' => 'Informatique'
            ),
            'required' => true
        ));

        $builder->add('numberDirector', IntegerType::class, array(
            'label' => 'Nombre de directeurs*',
            'attr' => array(
                'placeholder' => 'min: 1 - max: 5'
            ),
            'required' => true
        ));

        $builder->add('address', TextType::class, array(
            'label' => 'Adresse*',
            'attr' => array(
                'placeholder' => "123 rue de l'entreprise"
            ),
            'required' => true
        ));

        $builder->add('city', TextType::class, array(
            'label' => 'Ville*',
            'attr' => array(
                'placeholder' => 'Paris'
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

        $builder->add('phoneNumber', TextType::class, array(
            'label' => 'Téléphone*',
            'attr' => array(
                'placeholder' => '0725582631'
            ),
            'required' => true
        ));

        $builder->add('siret', IntegerType::class, array(
            'label' => 'Siret*',
            'attr' => array(
                'placeholder' => '505238415'
            ),
            'required' => true
        ));

        $builder->add('creationDate', DateType::class, array(
            'label' => 'Date de création*',
            'widget' => 'single_text',
            'html5' => false,
            'attr' => [
                'class' => 'flatpickr-input',
                'placeholder' => '2009-06-18'
            ],
            'required' => true
        ));

        if($enterpriseId){
            $builder->add('save', SubmitType::class, array(
                'label' => 'Modifier',
                'attr' => array('class' => 'button black')
            ));
        }
        else{
            $builder->add('save', SubmitType::class, array(
                'label' => 'Ajouter',
                'attr' => array(
                    'class' => 'button black',
                ),
            ));
        }
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => Enterprise::class,
        ));
    }
}