<?php

namespace Backend\MyFOSUserBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\BaseType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RegistrationFormType extends AbstractType
{

    /*
    private $class;

    /**
     * @param string $class The User class name
     */
    /*
    public function __construct(string $class)
    {
    	
        $this->class = $class;
    }
    */
    
    
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        

        $builder
            ->add('email', EmailType::class, array('label' => 'form.email', 'translation_domain' => 'FOSUserBundle'))
            ->add('username', null, array('label' => 'form.username', 'translation_domain' => 'FOSUserBundle'))
            ->add('plainPassword', RepeatedType::class, array(
                'type' => PasswordType::class,
                'options' => array('translation_domain' => 'FOSUserBundle'),
                'first_options' => array('label' => 'form.password'),
                'second_options' => array('label' => 'form.password_confirmation'),
                'invalid_message' => 'fos_user.password.mismatch',
            ))
			
			
            ->add('name',  null, array('label'=>"Nombre", 'required'=>true))
            ->add('lastName',  null, array('label'=>"Apellido", 'required'=>true))
			//->add('birthdate',  null, array('label'=>"Fecha de Nacimiento", 'required'=>false))
            //->add('phone',null,array('required'=>true))
			;
			

		
		 		 
    
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Backend\AdminBundle\Entity\User',
            'intention'  => 'registration',
            'allow_extra_fields' => true,
        ));
    }    


    public function getName()
    {
        return 'backend_user_registration';
    }
}