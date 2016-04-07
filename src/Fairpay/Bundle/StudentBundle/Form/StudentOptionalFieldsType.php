<?php

namespace Fairpay\Bundle\StudentBundle\Form;

use Fairpay\Bundle\StudentBundle\Entity\Student;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\BirthdayType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class StudentOptionalFieldsType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
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
            ->add('phone', TextType::class, array(
                'label' => 'student.phone',
                'required' => false,
            ))
            ->add('save', SubmitType::class, array(
                'label' => 'Ok',
            ))
        ;

        $builder->addEventListener(FormEvents::PRE_SET_DATA, [$this, 'preSetData']);
    }

    public function preSetData(FormEvent $event)
    {
        $builder = $event->getForm();

        foreach ($event->getData()->untouchableFields as $untouchableField) {
            if ($builder->has($untouchableField)) {
                $config  = $builder->get($untouchableField)->getConfig();
                $options = $config->getOptions();
                $options['disabled'] = true;

                $builder->add($untouchableField, get_class($config->getType()->getInnerType()), $options);
            }
        }
    }
    
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Fairpay\Bundle\StudentBundle\Form\StudentOptionalFields',
            'translation_domain' => 'entities',
        ));
    }
}
