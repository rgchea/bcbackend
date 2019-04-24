<?php

namespace Backend\AdminBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class BusinessType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name',  null, array('label'=>"label_name", 'required'=>true))
            ->add('geoState',  null, array('label'=>"label_name", 'required'=>true))
            ->add('billingGeoState',  null, array('label'=>"label_state", 'required'=>true))
            ->add('taxName',  null, array('label'=>"label_tax_name", 'required'=>true))
            ->add('taxIdentifier',  null, array('label'=>"label_tax_id", 'required'=>true))
            ->add('address',  null, array('label'=>"label_address", 'required'=>true))
            ->add('billingAddress',  null, array('label'=>"label_address", 'required'=>true))
            ->add('zipCode',  null, array('label'=>"label_zip_code", 'required'=>true))
            ->add('billingZipCode',  null, array('label'=>"label_zip_code", 'required'=>true))
            ->add('phoneNumber',  null, array('label'=>"label_phone", 'required'=>true))
            ->add('email',  null, array('label'=>"label_email", 'required'=>true))

        ;
    }
    
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Backend\AdminBundle\Entity\Business',
            'allow_extra_fields' => true
        ));
    }
}
