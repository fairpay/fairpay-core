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
use Symfony\Component\Security\Core\Authorization\AuthorizationChecker;

class StudentDataType extends AbstractType
{
    /** @var  AuthorizationChecker */
    private $authorizationChecker;

    /**
     * StudentDataType constructor.
     * @param AuthorizationChecker  $authorizationChecker
     */
    public function __construct(AuthorizationChecker $authorizationChecker)
    {
        $this->authorizationChecker = $authorizationChecker;
    }

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
                'format' => 'yyyy-MM-dd',
            ))
            ->add('barcode', TextType::class, array(
                'label' => 'student.barcode',
                'required' => false,
            ))
            ->add('phone', TextType::class, array(
                'label' => 'student.phone',
                'required' => false,
            ))
            ->add('isSub', ChoiceType::class, array(
                'label' => 'student.is_sub',
                'expanded' => true,
                'choices' => array(
                    'student.values.is_sub.1' => true,
                    'student.values.is_sub.0' => false,
                )
            ))
            ->add('save', SubmitType::class, array(
                'label' => 'Ok',
            ))
        ;

        if (!$this->authorizationChecker->isGranted('SUBSCRIBERS_MANAGE')) {
            $builder->remove('isSub');
        }
    }
    
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Fairpay\Bundle\StudentBundle\Form\StudentData',
            'translation_domain' => 'entities',
        ));
    }
}
