<?php

namespace Backend\AdminBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class PropertyType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $role = $options["role"];
        $repository = $options["repository"];
        $complex = $options["complex"];
        //var_dump($options["userID"]);die;


        $builder
            //->add('name',  null, array('label'=>"label_name", 'required'=>true,))
            ->add('propertyNumber',  null, array('label'=>"label_property_number", 'required'=>true))
            ->add('address',  TextareaType::class, array('label'=>"label_address", 'required'=>true, ))
            /*->add('code',  null, array('label'=>"label_code", 'required'=>true,
                    'attr' => array(
                        'readonly' => true)

                    )
                )
            */
        ;
            //->add('propertyType',  null, array('label'=>"label_type", 'required'=>true))
            $builder->add('propertyType', null, array('label'=>"label_property_type", 'required' => true,
                'class' => 'Backend\AdminBundle\Entity\PropertyType',
                'query_builder' => function (\Doctrine\ORM\EntityRepository $er)  use ($options){
                    return $er->createQueryBuilder('c')
                        ->where('c.enabled = 1')
                        ->orderBy("c.id", "DESC")
                        ;
                }
            )) ;


            /*
            $array = $repository->getComplexByUser($options["userID"]);
            $builder->add('complex', ChoiceType::class, array('choices' => $array, 'label'=>"label_complex", 'required' => true, 'mapped' => false));

            $arrComplex = $repository->getComplexByUser($options["userID"]);
            $filters = array();
            foreach ($arrComplex as $k =>$v) {
                $filters[$v] = $v;//the complex id
            }
            */

            //var_dump($filters);die;

            $builder->add('complexSector', null, array('label'=>"label_complex_sector", 'required' => true,
                'class' => 'Backend\AdminBundle\Entity\ComplexSector',
                'query_builder' => function (\Doctrine\ORM\EntityRepository $er)  use ($options){
                    return $er->createQueryBuilder('s')
                        ->join("s.complex", 'c')
                        ->where('s.enabled = 1')
                        //->andWhere("c.id IN (:arrComplex)")->setParameter('arrComplex', $options)
                        ->andWhere("c.id = ". $options["complex"])
                        ->orderBy("s.id", "DESC")
                        ;
                }
            )) ;



    }
    
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Backend\AdminBundle\Entity\Property',
            'role' => null,
            'repository' => null,
            'userID' => null,
            'complex' => null,
        ));
    }
}
