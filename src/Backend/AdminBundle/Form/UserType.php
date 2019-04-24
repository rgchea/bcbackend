<?php

namespace Backend\AdminBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Validator\Constraints\File;

class UserType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $role = $options["role"];

        $builder
            ->add('username',  null, array('label'=>"label_username", 'required'=>true))
            ->add('name',  null, array('label'=>"label_name", 'required'=>true))
            //->add('lastName',  null, array('label'=>"label_last_name", 'required'=>true))
            //->add('role',  null, array('label'=>"Rol", 'required'=>true))
            ->add('email',  null, array('label'=>"label_email", 'required'=>true))
            ->add('enabled', null, array('label'=>"label_enabled", 'required' => false))
            ->add('password', PasswordType::class, array('label'=>"label_login_password", 'required' => false))
            ->add('avatarPath', FileType::class,
                    array(
                        'data_class' => null,
                        'required' => false,
                        'label' => "Avatar",
                        'attr' => array(
                            'accept' => "image/jpeg, image/png"
                        ),
                        'constraints' => [
                            new File([
                                'maxSize' => '1M',
                                'mimeTypes' => [
                                    'image/jpeg',
                                    'image/png',
                                ],
                                'mimeTypesMessage' => 'JPG/PNG',
                            ])
                        ]
                    )
                )
            ;

            //IF ROLE IS SUPER ADMIN VIEW ALL
            if($role == "SUPER ADMIN"){
                $builder->add('role', null, array('label'=>"label_role", 'required' => true,
                    'class' => 'Backend\AdminBundle\Entity\Role',
                    'query_builder' => function (\Doctrine\ORM\EntityRepository $er)  use ($options){
                        return $er->createQueryBuilder('Role')
                            ->where('Role.enabled = 1')
                            ->andWhere('Role.endType = :param_type')
                            ->setParameter('param_type', "backend")
                            ->orderBy("Role.name", "ASC")
                            ;
                    }
                ));

                $builder->add('business', null, array('label'=>"label_business", 'required' => true,
                    'class' => 'Backend\AdminBundle\Entity\Business',
                    'query_builder' => function (\Doctrine\ORM\EntityRepository $er)  use ($options){
                        return $er->createQueryBuilder('B')
                            ->where('B.enabled = 1')
                            ->orderBy("B.name", "ASC")
                            ;
                    }
                ));


            }
            else{

                $builder->add('role', null, array('label'=>"label_role", 'required' => true,
                    'class' => 'Backend\AdminBundle\Entity\Role',
                    'query_builder' => function (\Doctrine\ORM\EntityRepository $er)  use ($options){
                        return $er->createQueryBuilder('Role')
                            ->where('Role.enabled = 1')
                            ->andWhere('Role.endType = :param_type')
                            ->andWhere('Role.name != :super_role')
                            ->setParameter('param_type', "backend")
                            ->setParameter('super_role', "SUPER ADMIN")
                            ->orderBy("Role.name", "ASC")
                            ;
                    }
                ));


            }




        
    }
    
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Backend\AdminBundle\Entity\User',
            'allow_extra_fields' => true,
            'role' => null,
            'userID' => null
        ));
    }
}
