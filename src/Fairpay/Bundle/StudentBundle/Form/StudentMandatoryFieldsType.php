<?php

namespace Fairpay\Bundle\StudentBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class StudentMandatoryFieldsType extends AbstractType
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
            ->add('schoolYear', TextType::class, array(
                'label' => 'student.school_year',
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
                $options = $builder->get($untouchableField)->getConfig()->getOptions();
                $options['disabled'] = true;
                $builder->add($untouchableField, TextType::class, $options);
            }
        }
    }
    
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Fairpay\Bundle\StudentBundle\Form\StudentMandatoryFields',
            'translation_domain' => 'entities',
        ));
    }
}
