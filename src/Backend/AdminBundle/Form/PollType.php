<?php

namespace Backend\AdminBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PollType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name',  null, array('label'=>"label_name", 'required'=>true))
            ->add('description',  null, array('label'=>"label_description", 'required'=>true))
            ->add('extraPoints',  null, array('label'=>"label_poll_extra_points", 'required'=>true))
            ->add('enabled',  null, array('label'=>"label_poll_active", 'required'=>false))


        ;
    }
    
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Backend\AdminBundle\Entity\Poll',
            'allow_extra_fields' => true
        ));
    }
}
