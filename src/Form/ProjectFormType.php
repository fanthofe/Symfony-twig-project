<?php

// src/Form/ProjectFormType.php
namespace App\Form;

use App\Entity\Enterprise;
use App\Entity\Project;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class ProjectFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $projectId = $options['data']->getId();

        $builder->add('name', TextType::class, array(
            'label' => 'Nom du projet*',
            'attr' => array(
                'placeholder' => 'Busard'
            ),
            'required' => true
        ));
        
        $builder->add('ressource', IntegerType::class, array(
            'label' => 'Ressources* (nombre de personnes)',
            'attr' => array(
                'placeholder' => 'min: 3 - max:20'
            ),
            'required' => true
        ));

        $builder->add('estimationDuration', IntegerType::class, array(
            'label' => 'Estimation du temps de projet* (en jours)',
            'attr' => array(
                'placeholder' => 'min: 2 - max: 730'
            ),
            'required' => true
        ));

        $builder->add('enterpriseId', EntityType::class, [
            'label' => "Lier à l'entreprise",
            'class' => Enterprise::class,
            'choice_label' => 'Name',
            'query_builder' => function(EntityRepository $er){
                return $er->createQueryBuilder('t')
                    ->orderBy('t.name', 'ASC');
            },
            'attr' => [
                'class' => 'select-enterprise'
            ],
            'placeholder' => "Choisissez une entreprise"
        ]);

        if($projectId){
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
            'data_class' => Project::class,
        ));
    }
}