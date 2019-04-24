<?php


namespace Backend\MyFOSUserBundle\Security;

use FOS\UserBundle\Security\LoginManagerInterface;

use FOS\UserBundle\Model\UserInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\SecurityContextInterface;
use Symfony\Component\Security\Http\RememberMe\RememberMeServicesInterface;
use Symfony\Component\Security\Http\Session\SessionAuthenticationStrategyInterface;




use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Security\Http\RememberMe\AbstractRememberMeServices;


use Doctrine\Bundle\DoctrineBundle\Registry as DoctrinePizote; // for Symfony 2.1.0+


class LoginManager implements LoginManagerInterface
{
    private $securityContext;
    private $userChecker;
    private $sessionStrategy;
    private $container;
    private $em;
	
	/*
    public function __construct(SecurityContextInterface $context, UserCheckerInterface $userChecker,
                                SessionAuthenticationStrategyInterface $sessionStrategy,
                                ContainerInterface $container,
                                DoctrinePizote $doctrine)
    {
        $this->securityContext = $context;
        $this->userChecker = $userChecker;
        $this->sessionStrategy = $sessionStrategy;
        $this->container = $container;
        $this->em = $doctrine->getManager();
    }
	 * 
	 */

	 
   /**
     * @var AbstractRememberMeServices[]
     */
     
    private $rememberMeServices;

    public function __construct(
        SecurityContextInterface $context,
        UserCheckerInterface $userChecker,
        SessionAuthenticationStrategyInterface $sessionStrategy,
        ContainerInterface $container,
        $rememberMeServices
    ) {
        $this->securityContext    = $context;
        $this->userChecker        = $userChecker;
        $this->sessionStrategy    = $sessionStrategy;
        $this->container          = $container;
        $this->rememberMeServices = $rememberMeServices;
    }	 

    final public function loginUser($firewallName, UserInterface $user, Response $response = null)
    {
    	
		//$em = $this->getDoctrine()->getManager();
		//$em = $this->getEntityManager();
		//$conn = $this->getEntityManager()->getConnection();  
		//print "entra login";die;
		
		$this->em = $this->container->get('doctrine')->getManager();
		
        $this->userChecker->checkPostAuth($user);

        $token = $this->createToken($firewallName, $user);

        if ($this->container->isScopeActive('request')) {
            $this->sessionStrategy->onAuthentication($this->container->get('request'), $token);

            if (null !== $response) {
                $rememberMeServices = null;
                if ($this->container->has('security.authentication.rememberme.services.persistent.'.$firewallName)) {
                    $rememberMeServices = $this->container->get('security.authentication.rememberme.services.persistent.'.$firewallName);
                } elseif ($this->container->has('security.authentication.rememberme.services.simplehash.'.$firewallName)) {
                    $rememberMeServices = $this->container->get('security.authentication.rememberme.services.simplehash.'.$firewallName);
                }

                if ($rememberMeServices instanceof RememberMeServicesInterface) {
                    $rememberMeServices->loginSuccess($this->container->get('request'), $response, $token);
                }
            }
        }

        $this->securityContext->setToken($token);

        // Here's the custom part, we need to get the current session and associate the user with it
        $sessionId = $this->container->get('session')->getId();
        $user->setSessionId($sessionId);
        $this->em->persist($user);
        $this->em->flush();
		
    }

    protected function createToken($firewall, UserInterface $user)
    {
        return new UsernamePasswordToken($user, null, $firewall, $user->getRoles());
    }
}
?>