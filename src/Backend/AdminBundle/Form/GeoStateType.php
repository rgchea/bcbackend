<?php

namespace Backend\AdminBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class GeoStateType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {        

        $builder
            ->add('name',  null, array('label'=>"label_name", 'required'=>true))
            ->add('timezoneOffset', ChoiceType::class, ['choices' => 
                [
                    'GMT-11:00' => "GMT-11:00", 'GMT-10:00' => "GMT-10:00", 'GMT-9:30' => "GMT-9:30",
                    'GMT-9:00' => "GMT-9:00", 'GMT-8:00' => "GMT-8:00", 'GMT-7:00' => "GMT-7:00", 'GMT-6:00' => "GMT-6:00",
                    'GMT-5:00' => "GMT-5:00", 'GMT-4:30' => "GMT-4:30", 'GMT-4:00' => "GMT-4:00", 'GMT-3:30' => "GMT-3:30",
                    'GMT-3:00' => "GMT-3:00", 'GMT-2:00' => "GMT-2:00", 'GMT-1:00' => "GMT-1:00", 'GMT' => "GMT",
                    'GMT+1:00' => "GMT+1:00", 'GMT+2:00' => "GMT+2:00", 'GMT+3:00' => "GMT+3:00", 'GMT+4:00' => "GMT+4:00",
                    'GMT+4:30' => "GMT+4:30", 'GMT+5:00' => "GMT+5:00", 'GMT+5:30' => "GMT+5:30", 'GMT+5:45' => "GMT+5:45",
                    'GMT+6:00' => "GMT+6:00", 'GMT+6:30' => "GMT+6:30", 'GMT+7:00' => "GMT+7:00", 'GMT+8:00' => "GMT+8:00",
                    'GMT+8:45' => "GMT+8:45", 'GMT+9:00' => "GMT+9:00", 'GMT+9:30' => "GMT+9:30", 'GMT+10:00' => "GMT+10:00",
                    'GMT+10:30' => "GMT+10:30", 'GMT+11:00' => "GMT+11:00", 'GMT+11:30' => "GMT+11:30", 'GMT+12:00' => "GMT+12:00",
                    'GMT+12:45' => "GMT+12:45", 'GMT+13:00' => "GMT+13:00", 'GMT+14:00' => "GMT+14:00"
                ], 'label'=>"label_timezone", 'required'=>true ])
            ->add('zipCode',  null, array('label'=>"label_zip_code", 'required'=>true))
            ->add('geoCountry',  null, array('label'=>"label_country", 'required'=>false));
           


    }
    
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Backend\AdminBundle\Entity\GeoState',
            'role' => null,
            'repository' => null,
            'userID' => null,


        ));
    }
}
