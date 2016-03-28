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
                'label' => 'student.first_name',
            ))
            ->add('lastName', TextType::class, array(
                'label' => 'student.last_name',
            ))
            ->add('email', TextType::class, array(
                'label' => 'student.email_full',
            ))
            ->add('schoolYear', TextType::class, array(
                'label' => 'student.school_year',
            ))
            ->add('gender', ChoiceType::class, array(
                'label' => 'student.gender',
                'required' => false,
                'choices' => array(
                    'student.values.gender.male' => Student::MALE,
                    'student.values.gender.female' => Student::FEMALE,
                )
            ))
            ->add('birthday', BirthdayType::class, array(
                'label' => 'student.birthday',
                'required' => false,
                'widget' => 'single_text',
                'format' => 'dd/MM/yyyy',
            ))
            ->add('barcode', TextType::class, array(
                'label' => 'student.barcode',
                'required' => false,
            ))
            ->add('phone', TextType::class, array(
                'label' => 'student.phone',
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
            'data_class' => 'Fairpay\Bundle\StudentBundle\Form\StudentAdd',
            'translation_domain' => 'entities',
        ));
    }
}
