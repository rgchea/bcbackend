<?php

namespace Backend\AdminBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class PollQuestionTypeType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nameEN',  null, array('label'=>"label_name", 'required'=>true))
            ->add('nameES',  null, array('label'=>"label_nombre", 'required'=>true))
            ->add('descriptionEN',  null, array('label'=>"label_description", 'required'=>true))
            ->add('descriptionES',  null, array('label'=>"label_descripcion", 'required'=>true))
        ;
    }
    
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Backend\AdminBundle\Entity\PollQuestionType'
        ));
    }
}
