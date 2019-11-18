<?php

namespace Backend\AdminBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Validator\Constraints\File;

class ComplexType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        //var_dump($options["locale"]);die;
        /*
        $sectionQuantity = array();
        for($i=1; $i<=20; $i++){
            $sectionQuantity[$i] = $i;
        }

        $propertyQuantity = array();
        for($i=1; $i<=50; $i++){
            $propertyQuantity[$i] = $i;
        }
        */

        $propertyIdentifiers = array("101,102,103...125" => "101", "1A,1B,1C...1M" => "1A", 'label_other' => 0);


        $builder

            ->add('enabled',  null, array('label'=>"label_enabled", 'required'=>false))
            ->add('latePayment',  null, array('label'=>"label_late_payment", 'required'=>false))

            ->add('name',  null, array('label'=>"label_name", 'required'=>true))
            //->add('complexType',  null, array('label'=>"label_complex_type", 'required'=>true))
            ->add('complexType', null, array('label'=>"label_complex_type", 'required' => true,
                'class' => 'Backend\AdminBundle\Entity\ComplexType',
                'query_builder' => function (\Doctrine\ORM\EntityRepository $er)  use ($options){
                        $qb =  $er->createQueryBuilder('ct')
                        ->where('ct.enabled = 1');
                        if($options["locale"] == "en"){
                            $qb->orderBy("ct.nameEN", "ASC");
                        }
                        else{
                            $qb->orderBy("ct.nameES", "ASC");
                        }

                        return $qb;
                }
            ))
            ->add('geoState',  null, array('label'=>"label_state", 'required'=>true))
            ->add('address',  null, array('label'=>"label_address", 'required'=>true))
            ->add('zipCode',  null, array('label'=>"label_zip_code", 'required'=>true))
            ->add('phoneNumber',  null, array('label'=>"label_phone", 'required'=>true))
            ->add('email',  null, array('label'=>"label_email", 'required'=>true))
            ->add('avatarPath', FileType::class,
                array(
                    'data_class' => null,
                    'required' => false,
                    'label' => "label_complex_image",
                    'attr' => array(
                        'accept' => "image/jpeg, image/png"
                    ),
                    'constraints' => [
                        new File([
                            'maxSize' => '3M',
                            'mimeTypes' => [
                                'image/jpeg',
                                'image/png',
                            ],
                            'mimeTypesMessage' => 'JPG/PNG',
                        ])
                    ]
                )
            )

            //EXTRA FIELDS
            //->add('propertyIdentifiers', ChoiceType::class, ['choices' => $propertyIdentifiers, 'required'=>true, 'mapped' => false ])
            ->add('sectionsQuantity', null, ['required'=>true, 'mapped' => false ])
            ->add('propertiesPerSection', null, ['required'=>true, 'mapped' => false ])

        ;
    }
    
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Backend\AdminBundle\Entity\Complex',
            'allow_extra_fields' => true,
            'locale' => null
        ));
    }
}
