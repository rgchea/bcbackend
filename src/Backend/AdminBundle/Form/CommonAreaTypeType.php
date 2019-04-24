<?php

namespace Backend\AdminBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class CommonAreaTypeType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $role = $options["role"];
        $repository = $options["repository"];

        $builder
            ->add('name',  null, array('label'=>"label_name", 'required'=>true))
            ->add('description',  TextareaType::class, array('label'=>"label_description", 'required'=>true));
            //->add('complex',  null, array('label'=>"label_complex", 'required'=>true))


            //IF ROLE IS SUPER ADMIN VIEW ALL
            if($role == "SUPER ADMIN"){
                $builder->add('complex', null, array('label'=>"label_complex", 'required' => true,
                    'class' => 'Backend\AdminBundle\Entity\Complex',
                    'query_builder' => function (\Doctrine\ORM\EntityRepository $er)  use ($options){
                        return $er->createQueryBuilder('c')
                            ->where('c.enabled = 1')
                            ->orderBy("c.name", "ASC")
                            ;
                    }
                ));
            }
            else{
                $array = $repository->getComplexByUser($options["userID"]);
                $builder->add('complex', ChoiceType::class, array('choices' => $array, 'label'=>"label_complex", 'required' => true, 'mapped' => false));

            }



    }
    
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Backend\AdminBundle\Entity\CommonAreaType',
            'role' => null,
            'repository' => null,
            'userID' => null,


        ));
    }
}
