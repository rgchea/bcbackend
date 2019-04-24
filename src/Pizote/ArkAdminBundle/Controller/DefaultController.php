<?php

namespace Pizote\ArkAdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('PizoteArkAdminBundle:Default:index.html.twig');
    }
	
	
    public function userAction(){
    	return $this->render('PizoteArkAdminBundle:Partials:user.html.twig', array('user' => $this->getUser()));   
    }
	
    public function menuAction(){
    	$session = new Session();
    	$item    = $session->get('item', 'dashboard');
    	return $this->render('PizoteArkAdminBundle:Partials:menu.html.twig', array('item' => $item));   
    }

	
}
