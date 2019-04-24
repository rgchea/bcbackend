<?php

namespace Backend\MyFOSUserBundle\EventListener;

use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\DependencyInjection\Container;
use Doctrine\Bundle\DoctrineBundle\Registry as Doctrine; // for Symfony 2.1.0+
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

use Symfony\Component\HttpFoundation\Session\Session;

/**
 * Custom login listener.
 */
class LoginListener
{
    /** @var \Symfony\Component\Security\Core\SecurityContext */
    private $securityContext;

    /** @var \Doctrine\ORM\EntityManager */
    private $em;

    private $container;

    private $doc;

    /**
     * Constructor
     * 
     * @param SecurityContext $securityContext
     * @param Doctrine        $doctrine
     */
    public function __construct(SecurityContext $securityContext, Doctrine $doctrine, Container $container)
    {
        $this->securityContext = $securityContext;
        $this->doc = $doctrine;
        $this->em              = $doctrine->getManager();
        $this->container        = $container;
    }

    /**
     * Do the magic.
     * 
     * @param InteractiveLoginEvent $event
     */
    public function onSecurityInteractiveLogin(InteractiveLoginEvent $event)
    {

		$session = new Session();
		
        if ($this->securityContext->isGranted('IS_AUTHENTICATED_FULLY')) {
            // user has just logged in
        }

        if ($this->securityContext->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            // user has logged in using remember_me cookie
        }

        // First get that user object so we can work with it
        $user = $event->getAuthenticationToken()->getUser();
		
		
		////VALIDACION DE COLEGIO HABILITADO
		$objSchool = $user->getSchool();
		
		if($objSchool->getId() != 0){

			if($objSchool->getEnabled() == 0){
				throw new AccessDeniedException("EL COLEGIO HA SIDO DESHABILITADO TEMPORALMENTE, COMUNICARSE CON EL ADMINISTRADOR SANTILLANA");//el usuario está loggeado
			}
			
		}
		

        // Get the current session and associate the user with it
        //$user->setSessionId($this->securityContext->getToken()->getCredentials());
        //$sessionId = $this->container->get('session')->getId();
        $sessionId = $session->getId();
		//var_dump($sessionId);die;
		$userSesssion = $user->getSessionId();
		
		/*
		var_dump($userSesssion);
		var_dump($sessionId);
		die;
		 * */
		
		/*
		 * */
        /*
		$isLogged = $user->getLogged();
		if($isLogged){
			
			
			if($userSesssion != $sessionId && ($userSesssion != NULL) ){
				
				$userID = $user->getId();
				//print "no debe entrar";die;
				//throw new AccessDeniedException("EL USUARIO ESTA LOGGEADO EN OTRA COMPUTADORA");//el usuario está loggeado			
                //$this->securityContext->setToken(null);
                 //$this->get('request')->getSession()->invalidate();
            
				
			}
			else{
				//print "no chimation";
			}
			
			
		}	
		//NO ESTA LOGGEADO	
		else{
			
	        $user->setSessionId($sessionId);
			$user->setLogged(1);
	        $this->em->persist($user);
	        $this->em->flush();
			
		}
		*/
		
			
        $user->setSessionId($sessionId);
        $user->setLogged(1);
        $this->em->persist($user);
        $this->em->flush();		 
		
		/*
		 * 
		 * 
		 */

        // ...
    }
}
