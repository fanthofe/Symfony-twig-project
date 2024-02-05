<?php

// src/Form/articleFormType.php
namespace App\Form;

use App\Entity\Category;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class CategoryFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $categoryId = $options['data']->getId();

        $builder->add('name', TextType::class, array(
            'label' => "Nom*",
            'attr' => array(
                'placeholder' => "Nom de l'article"
            ),
            'required' => true
        ));

        if($categoryId){
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
            'data_class' => Category::class,
        ));
    }
}