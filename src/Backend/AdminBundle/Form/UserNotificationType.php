<?php

namespace Backend\AdminBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserNotificationType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('isRead')->add('title')->add('description')->add('notice')->add('reminder')->add('createdAt')->add('updatedAt')->add('isScheduled')->add('scheduledTime')->add('enabled')->add('sentTo')->add('createdBy')->add('updatedBy')->add('notificationType')->add('ticket')->add('commonAreaReservation')->add('tenantContract')->add('complexToNotify')->add('sectorToNotify')->add('propertyToNotify');
    }/**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Backend\AdminBundle\Entity\UserNotification'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'backend_adminbundle_usernotification';
    }


}
