<?php

namespace Backend\AdminBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class GeoCountryType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name',  null, array('label'=>"label_name", 'required'=>true))
            ->add('code',  null, array('label'=>"label_code", 'required'=>false))
            ->add('shortName',  null, array('label'=>"label_short_name", 'required'=>true))
            ->add('ibillingToken',  null, array('label'=>"iBilling Token", 'required'=>true))
            
        ;
    }
    
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Backend\AdminBundle\Entity\GeoCountry'
        ));
    }
}
