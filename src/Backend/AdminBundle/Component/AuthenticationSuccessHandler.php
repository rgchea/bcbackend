<?php
 
namespace Backend\AdminBundle\Component;
 
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\RouterInterface;
use Doctrine\ORM\EntityManager;
use Xxx\YourBundle\Entity\User;
use Symfony\Component\Security\Core\SecurityContext;
use Doctrine\Bundle\DoctrineBundle\Registry as Doctrine;
use Symfony\Component\Security\Http\HttpUtils;
 
/**
 * Custom authentication success handler
 */
class AuthenticationSuccessHandler implements AuthenticationSuccessHandlerInterface
{
		
		
   private $securityContext;		 
   private $router;
   private $em;
 
   /**
    * Constructor
    * @param RouterInterface   $router
    * @param EntityManager     $em
    */
   public function __construct(RouterInterface $router, SecurityContext $securityContext, Doctrine $doctrine)
   {
   	
		$this->securityContextsecurityContext = $securityContext;
		$this->em              = $doctrine->getEntityManager();
		$this->router = $router;
   }
 
   /**
    * This is called when an interactive authentication attempt succeeds. This
    * is called by authentication listeners inheriting from AbstractAuthenticationListener.
    * @param Request        $request
    * @param TokenInterface $token
    * @return Response The response to return
    */
   function onAuthenticationSuccess(Request $request, TokenInterface $token)
   {
		/*
      	if ($this->securityContext->isGranted('ROLE_SUPER_ADMIN')){			
			$uri = $this->router->generate('backend_admin_homepage');            
        }
        elseif ($this->securityContext->isGranted('ROLE_CLIENT') || $this->securityContext->isGranted('ROLE_CLIENT_STAFF'))
        {
        	$uri = $this->router->generate('opera_client_homepage');
        }
        elseif ($this->securityContext->isGranted('ROLE_OPERATOR') || $this->securityContext->isGranted('ROLE_OPERATOR_STAFF'))
        {
        	$uri = $this->router->generate('opera_shipper_homepage');
        }	
		 * 
		 */
      	if ($this->securityContext->isGranted('ROLE_USER')){			
			$uri = $this->router->generate('backend_admin_homepage');            
        } 		
 		//$uri = $this->router->generate('backend_admin_homepage');    
      	return new RedirectResponse($uri);
   }
}