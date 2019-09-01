<?php

namespace Backend\AdminBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class CommonAreaType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $role = $options["role"];
        $repository = $options["repository"];
        //var_dump($options["userID"]);die;
        $builder
            ->add('name',  null, array('label'=>"label_name", 'required'=>true))
            ->add('description',  null, array('label'=>"label_description", 'required'=>true))    
            ->add('regulation',  TextareaType::class, array('label'=>"label_regulation", 'required'=>true ))
            ->add('termCondition',  TextareaType::class, array('label'=>"label_term_condition", 'required'=>true, 'attr' => array('class' => 'ckeditor') ))
            ->add('price',  null, array('label'=>"label_price", 'required'=>true))
            //->add('reservationHourPeriod', null, array('label'=>"label_reservation_hour_period", 'required'=>true))
            ->add('requiredPayment', null, array('label'=>"label_required_payment", 'required'=>false))
            ->add('hasEquipment', null, array('label'=>"label_has_equipment", 'required'=>false))
            ->add('equipmentDescription',  TextareaType::class, array('label'=>"label_equipment_description", 'required'=>false ))
             ;    
            //->add('propertyType',  null, array('label'=>"label_type", 'required'=>true))
            
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
            'data_class' => 'Backend\AdminBundle\Entity\CommonArea',
            'role' => null,
            'repository' => null,
            'userID' => null,
        ));
    }
}
