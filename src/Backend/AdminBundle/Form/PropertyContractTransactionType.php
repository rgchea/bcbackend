<?php

namespace Backend\AdminBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PropertyContractTransactionType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('description')
            ->add('comment')
            ->add('paymentAmount')
            //->add('status')
            ->add('paymentType')
            ->add('transactionNumber')
            ->add('dueDate')
            //->add('createdAt')
            //->add('updatedAt')
            //->add('enabled')
            //->add('propertyContract')
            //->add('commonAreaReservation')
            //->add('propertyTransactionType')
            //->add('paidBy')
            //->add('createdBy')
            //->add('updatedBy')
        ;
    }/**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Backend\AdminBundle\Entity\PropertyContractTransaction'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'backend_adminbundle_propertycontracttransaction';
    }


}
