<?php

namespace Backend\MyFOSUserBundle\EventListener;

use FOS\UserBundle\FOSUserEvents;
use FOS\UserBundle\Event\FormEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\DependencyInjection\Container;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Container\ContainerInterface;
use Symfony\Component\Security\Core\Security;

use Backend\AdminBundle\Entity\User;


/**
 * Listener responsible for adding the default user role at registration
 */
class RegistrationListener implements EventSubscriberInterface
{

    private $em;
    protected $container;
    private $security;

    public function __construct(EntityManagerInterface $entityManager, ContainerInterface $container, Security $security){
        $this->em = $entityManager;
        $this->container = $container;
        $this->security = $security;

    }

		
    public static function getSubscribedEvents()
    {
        return array(
            FOSUserEvents::REGISTRATION_SUCCESS => 'onRegistrationSuccess',
        );
    }

    public function onRegistrationSuccess(FormEvent $event)
    {
    	
		//print "ENTRA ON REGISTRATIONSUCCESS";DIE;

        //var_dump($_REQUEST["form"]["birthdate"]);die;

        /** @var $user \FOS\UserBundle\Model\UserInterface */
        //$user = $event->getForm()->getData();
        //$user = new User();
		//$userID = $user->getId();
		//var_dump($userID);die;

        /*
        $objRole = $this->em->getRepository('BackendAdminBundle:Role')->findByName("ADMIN");
		$objRole = $objRole[0];
		//var_dump($objRole->getId());die;

        //print "<pre>";
		//var_dump($_REQUEST);DIE;
        //$newDate = date('Y-m-d', strtotime(str_replace('-', '/', $_REQUEST["form"]["birthdate"])));
        $birthdate = new \DateTime($_REQUEST["form"]["birthdate"]);
		$user->setBirthdate($birthdate);
        $user->setRole($objRole);
        $this->em->persist($user);
        var_dump($user->getID());die;

        print "here";die;
        */


		
    }
}