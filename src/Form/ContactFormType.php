<?php
namespace App\Form;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
class ContactFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name',TextType::class, array(
                'label' => 'Nom',
                'attr' => array(
                    'placeholder' => 'Votre nom*'
                ),
                'required' => true
            ))
            ->add('email',EmailType::class, array(
                'label' => 'Email*',
                'attr' => array(
                    'placeholder' => 'Votre adresse mail*'
                ),
                'required' => true
            ))
            ->add('message', TextareaType::class, array(
                'label' => 'Message',
                'attr' => array(
                    'placeholder' => 'Votre message...',
                    'rows' => 6
                ),
                'required' => true
            ))
            ->add('save', SubmitType::class, array(
                'label' => 'Envoyer votre message',
                'attr' => array('class' => 'button black')
            ));
        ;
    }
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([]);
    }
}