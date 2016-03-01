<?php

namespace Fairpay\Bundle\SchoolBundle\Form;

use Fairpay\Util\Form\DataTransformer\ArrayToStringTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SchoolEmailPolicyType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('allowUnregisteredEmails', CheckboxType::class, array(
                'label' => 'Autoriser l\'inscription avec une adresse de l\'école',
                'required' => false,
            ))
            ->add('allowedEmailDomains', TextType::class, array(
                'required' => false,
            ))
        ;

        $builder->get('allowedEmailDomains')
            ->addModelTransformer(new ArrayToStringTransformer());
    }
    
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Fairpay\Bundle\SchoolBundle\Form\SchoolEmailPolicy'
        ));
    }
}
