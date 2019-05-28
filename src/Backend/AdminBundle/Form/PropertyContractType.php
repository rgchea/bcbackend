<?php

namespace Backend\AdminBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class PropertyContractType extends AbstractType
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
            ->add('propertyTransactionType',  null, array('label'=>"label_property_transaction_type", 'required'=>true))
            ->add('rentalPrice',  null, array('label'=>"label_price", 'required'=>true))
            ->add('maintenancePrice',  null, array('label'=>"label_maintenance", 'required'=>true))
            ->add('amenityIncluded',  null, array('label'=>"label_property_amenity_included", 'required'=>false))
            ->add('duePaymentDay',  null, array('label'=>"label_last_payment_day", 'required'=>true))
            //checkbox
            ->add('totalVisibleAmount',  null, array('label'=>"label_total_visible_amount", 'required'=>true))
            ->add('isActive',  null, array('label'=>"label_active", 'required'=>true))


            //->add('start_date',  null, array('label'=>"property_transaction_type", 'required'=>true,))
            //->add('end_date',  null, array('label'=>"property_transaction_type", 'required'=>true,))
        ;


        /*
        $arrComplex = $repository->getComplexByUser($options["userID"]);
        $filters = array();
        foreach ($arrComplex as $k =>$v) {
            $filters[$v] = $v;//the complex id
        }

        //var_dump($filters);die;

        $builder->add('complexSector', null, array('label'=>"label_complex_sector", 'required' => true,
            'class' => 'Backend\AdminBundle\Entity\ComplexSector',
            'query_builder' => function (\Doctrine\ORM\EntityRepository $er)  use ($filters){
                return $er->createQueryBuilder('s')
                    ->join("s.complex", 'c')
                    ->where('s.enabled = 1')
                    ->andWhere("c.id IN (:arrComplex)")->setParameter('arrComplex', $filters)
                    ->orderBy("s.id", "DESC")
                    ;
            }
        )) ;
        */



    }
    
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Backend\AdminBundle\Entity\PropertyContract',
            'allow_extra_fields' => true,
            'role' => null,
            'repository' => null,
            'userID' => null,
        ));
    }
}
