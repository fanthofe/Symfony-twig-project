<?php 

// src/Form/DateEventFormType.php
namespace App\Form;

use App\Entity\DateEvent;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class DateEventFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $eventId = $options['data']->getId();

        $builder->add('name', TextType::class, array(
            'label' => "Nom de l'évènement*",
            'attr' => array(
                'placeholder' => "Ajouter un nom d'évènement"
            ),
            'required' => true
        ));
        
        $builder->add('startDate', DateType::class, array(
            'label' => "Date de début l'évènement*",
            'attr' => array(
                'class' => 'form-control input-inline flatpickr-input',
                'data-provide' => 'flatpickr',
                'placeholder' => "Sélectionner une date"
            ),
            'widget' => 'single_text',
            'html5' => false,
            'required' => true
        ));

        $builder->add('endDate', DateType::class, array(
            'label' => "Date de fin de l'évènement*",
            'attr' => array(
                'class' => 'form-control input-inline flatpickr',
                'data-provide' => 'flatpickr',
                'placeholder' => "Sélectionner une date"
            ),
            'widget' => 'single_text',
            'html5' => false,
            'required' => true
        ));
        
        $builder->add('localisation', TextType::class, array(
            'label' => 'Localisation*',
            'attr' => array(
                'placeholder' => "Localisation de l'évènement"
            ),
            'required' => true
        ));
        
        $builder->add('description', TextType::class, array(
            'label' => 'Description*',
            'attr' => array(
                'placeholder' => 'Ajouter une description'
            ),
            'required' => true
        ));

        if($eventId){
            $builder->add('save', SubmitType::class, array(
                'label' => 'Modifier',
                'attr' => array('class' => 'button black')
            ));
        } else {
            $builder->add('save', SubmitType::class, array(
                'label' => 'Ajouter',
                'attr' => array('class' => 'button black')
            ));
        }
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => DateEvent::class,
        ));
    }
}