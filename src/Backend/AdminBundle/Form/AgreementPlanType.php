<?php

namespace Backend\AdminBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class AgreementPlanType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('fee',  null, array('label'=>"label_fee", 'required'=>true))
            ->add('propertyLimitFrom',  null, array('label'=>"label_property_limit_from", 'required'=>true))
            ->add('propertyLimitTo',  null, array('label'=>"label_property_limit_to", 'required'=>true))    
        ;
    }
    
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Backend\AdminBundle\Entity\AgreementPlan'
        ));
    }
}
