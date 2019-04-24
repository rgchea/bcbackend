<?php

namespace Backend\AdminBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Validator\Constraints\File;

class RegisterType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {


        $builder
        	->add('username',  null, array('label'=>"label_username", 'required'=>true))
            ->add('name',  null, array('label'=>"label_name", 'required'=>true))
            //->add('lastName',  null, array('label'=>"label_last_name", 'required'=>true))
            //->add('role',  null, array('label'=>"Rol", 'required'=>true))
            ->add('email',  null, array('label'=>"label_email", 'required'=>true))
            //->add('enabled', null, array('label'=>"label_enabled", 'required' => false))
			->add('password', PasswordType::class, array('label'=>"label_login_password", 'required' => false))


            ;


        
    }
    
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Backend\AdminBundle\Entity\User',
            'allow_extra_fields' => true,
            'csrf_protection' => false,
        ));
    }
}
