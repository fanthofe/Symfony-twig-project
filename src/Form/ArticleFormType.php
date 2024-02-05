<?php

// src/Form/articleFormType.php
namespace App\Form;

use App\Entity\article;
use App\Entity\Category;
use App\Repository\ArticleRepository;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityRepository;
use Nicolassing\QuillBundle\Form\Type\QuillType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Validator\Constraints\File;

class ArticleFormType extends AbstractType
{
    private $er; 

    public function __construct(CategoryRepository $er)
    {
        $this->er = $er;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $articleId = $options['data']->getId();
        $articleContent = $options['data']->getContent();
        $listCategory = [];

        foreach($this->er->findAll() as $category){
            $listCategory[] = $category->getName();
        }


        $builder->add('title', TextType::class, array(
            'label' => "Titre*",
            'attr' => array(
                'placeholder' => "Titre de l'article"
            ),
            'required' => true
        ));
        
        $builder->add('image', FileType::class, array(
            'label' => "Image*",
            'mapped' => false,
            'constraints' => [
                new File([
                    'maxSize' => '2048k',
                    'mimeTypes' => [
                        'image/jpg',
                        'image/jpeg',
                        'image/png',
                    ],
                    'mimeTypesMessage' => 'Please upload a valid picture (jpg, jpeg, png)',
                ])
            ],
            'attr' => array(
                'placeholder' => 'Choisissez une image'
            ),
            'required' => true
        ));

        $builder->add('short_description', TextType::class, array(
            'label' => 'Courte description*',
            'attr' => array(
                'placeholder' => "Ajouter une courte de description"
            ),
            'required' => true
        ));

        $builder->add('date', DateType::class, array(
            'label' => 'Date*',
            'widget' => 'single_text',
            'html5' => false,
            'attr' => array(
                'class' => 'flatpickr-input',
                'placeholder' => '2009-06-18'
            ),
            'required' => true
        ));

        $builder->add('status', ChoiceType::class, array(
            'choices' => [
                "BROUILLON" => "DRAFT",
                "PUBLIE" => "PUBLISHED"
            ],
            'label' => 'Status*',
            'attr' => array(
                'placeholder' => 'Choisir le status de publication'
            ),
            'required' => true
        ));

        $builder->add('articleCategories', EntityType::class, [
            'label' => "Types de catégories",
            'class' => Category::class,
            'choice_label' => 'Name',
            'query_builder' => function(EntityRepository $er){
                return $er->createQueryBuilder('t')
                    ->orderBy('t.name', 'ASC');
            },
            'attr' => [
                'class' => 'select-category'
            ],
            'multiple' => true,
            'mapped' => false
        ]);

        $builder->add('content', QuillType::class, array(
            'label' => 'Contenu*',
            'data' => $articleContent,
            'attr' => array(
                'placeholder' => 'Ajouter du contenu'
            ),
            'required' => true
        ));

        if($articleId){
            $builder->add('save', SubmitType::class, array(
                'label' => 'Modifier',
                'attr' => array('class' => 'button black'),
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
            'data_class' => article::class,
        ));
    }
}