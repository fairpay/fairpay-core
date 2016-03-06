<?php

namespace Fairpay\Bundle\StudentBundle\Form;

use Fairpay\Bundle\StudentBundle\Entity\Student;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\BirthdayType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class StudentAddType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('firstName', TextType::class, array(
                'label' => 'Prénom',
            ))
            ->add('lastName', TextType::class, array(
                'label' => 'Nom de famille',
            ))
            ->add('email', TextType::class, array(
                'label' => 'Adresse email scolaire',
            ))
            ->add('schoolYear', TextType::class, array(
                'label' => 'Année scolaire',
            ))
            ->add('gender', ChoiceType::class, array(
                'label' => 'Sexe',
                'required' => false,
                'choices' => array(
                    'Homme' => Student::MALE,
                    'Femme' => Student::FEMALE,
                )
            ))
            ->add('birthday', BirthdayType::class, array(
                'label' => 'Date de naissance',
                'required' => false,
                'widget' => 'single_text',
                'format' => 'dd/MM/yyyy',
            ))
            ->add('barcode', TextType::class, array(
                'label' => 'ID étudiant',
                'required' => false,
            ))
            ->add('phone', TextType::class, array(
                'label' => 'Numéro de téléphone',
                'required' => false,
            ))
            ->add('save', SubmitType::class, array(
                'label' => 'Ok',
            ))
        ;
    }
    
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Fairpay\Bundle\StudentBundle\Form\StudentAdd'
        ));
    }
}
