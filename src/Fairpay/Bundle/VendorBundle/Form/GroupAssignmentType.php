<?php

namespace Fairpay\Bundle\VendorBundle\Form;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class GroupAssignmentType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('user', EntityType::class, array(
                'class' => 'FairpayUserBundle:User',
            ))
            ->add('group', EntityType::class, array(
                'class' => 'FairpayVendorBundle:Group',
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
            'data_class' => 'Fairpay\Bundle\VendorBundle\Form\GroupAssignment',
            'translation_domain' => 'entities',
        ));
    }
}
