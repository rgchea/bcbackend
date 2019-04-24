<?php

namespace Backend\MyFOSUserBundle\EventListener;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Logout\LogoutHandlerInterface;

use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\DependencyInjection\Container;
use Doctrine\Bundle\DoctrineBundle\Registry as Doctrine; // for Symfony 2.1.0+

class LogoutListener implements LogoutHandlerInterface {
	
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

    public function logout(Request $Request, Response $Response, TokenInterface $Token) {

        // Your handling here
        $user = $this->container->get('security.context')->getToken()->getUser();
        //$user = $event->getAuthenticationToken()->getUser();
		//var_dump($user);die;
			
	    $user->setSessionId(NULL);
		$user->setLogged(0);
	    $this->em->persist($user);
	    $this->em->flush();
	}
			


}