<?php

namespace Backend\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Translation\Translator;


class DefaultController extends Controller
{
	

	
		
    public function indexAction()
    {

		$session = new Session();
        $this->get("services")->setVars('dashboard');
        
		$userRole = $session->get('user_role');
		$userLogged = $session->get('user_logged');

		/*
        $data = array(
            'my-message' => "rxie notification22",
        );
        $pusher = $this->get('mrad.pusher.notificaitons');
        $channel = 'messages';
        $pusher->trigger($data, $channel);

        // or you can keep the channel pram empty and will be broadcasted on "notifications" channel by default
        $pusher->trigger($data);
		*/
		

		$em = $this->getDoctrine()->getManager();
		//print "<pre>";var_dump($session->get("user_access"));die;
		//print  hash('sha512', 'pass');
        //$sessionId = $this->container->get('session')->getId();
		//var_dump($sessionId);die;
		///7db847bc83cc45956f400555ffc47f0d

        return $this->render('BackendAdminBundle:Default:index.html.twig', array('role' => $userRole));
    }
	
    public function menuAction(){
    	$session = new Session();
    	$item    = $session->get('item');

		//print "<pre>";
		//var_dump($session->get("user_access"));die;
		
    	return $this->render('BackendAdminBundle:Partials:menu.html.twig', 
    							array('item' => $item, 'user_access' => $session->get("user_access"))
							);
		
    }
	
		
}
