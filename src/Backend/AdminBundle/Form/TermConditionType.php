<?php

namespace Backend\AdminBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class TermConditionType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            //->add('descriptionES',  TextareaType::class, array('label'=>"Español", 'required'=>true))
            ->add('descriptionES', TextareaType::class, array('label'=> 'Español', 'attr' => array('class' => 'ckeditor')))
            ->add('descriptionEN', TextareaType::class, array('label'=> 'English', 'attr' => array('class' => 'ckeditor')))

        ;
    }
    
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Backend\AdminBundle\Entity\TermCondition'
        ));
    }
}
