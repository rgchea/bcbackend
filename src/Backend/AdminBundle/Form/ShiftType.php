<?php

namespace Backend\AdminBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\DateType;

class ShiftType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $role = $options["role"];
        $repository = $options["repository"];

        if($options["locale"] == "es"){
            $arrRepeat = array("Nunca" => 0, "Todos los días" => 1, "Cada semana" => 2, "Cada mes" => 3);

        }
        else{
            $arrRepeat = array("Never" => 0, "Every Day" => 1, "Every Week" => 2, "Every Month" => 3);
        }


        $arrHour = array("00" => 0, "01" => 1);


        $builder
            ->add('hourFrom',  null, array('label'=>"label_hour_from", 'required'=>true))
            ->add('hourTo',  null, array('label'=>"label_hour_to", 'required'=>true))
            ->add('repeat',  ChoiceType::class, array('choices' => $arrRepeat, 'label'=>"label_week_day", 'required'=>true))
            ->add('shiftDate',  null, array('label'=>"label_shift_date", 'required'=>true))
//            ->add('shiftDate',  DateType::class, array('widget' => 'single_text','format' => 'dd-MM-yyyy', 'label'=>"label_shift_day", 'required'=>true))
            ->add('overtime',  null, array('label'=>"label_overtime", 'required'=>true))
            ->add('flexibleHours',  null, array('label'=>"label_flexible_hours", 'required'=>true))
            ->add('flexitour',  null, array('label'=>"label_flexitour", 'required'=>true));
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
            'data_class' => 'Backend\AdminBundle\Entity\Shift',
            'role' => null,
            'repository' => null,
            'userID' => null,
            'locale' => null


        ));
    }
}
