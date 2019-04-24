<?php

namespace Backend\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Translation\Translator;
use GuzzleHttp\Client;



class CompanyController extends Controller
{


    protected $em;
    protected $translator;
    protected $repository;
    private  $renderer;


    // Set up all necessary variable
    protected function initialise()
    {
        $this->em = $this->getDoctrine()->getManager();
        //$this->repository = $this->em->getRepository('BackendAdminBundle:Company');
        $this->translator = $this->get('translator');
        $this->renderer = $this->get('templating');


    }



    public function indexAction()
    {

        $session = new Session();
        //$this->get("services")->setVars('company');
        $this->initialise();

        ///////TWILIO TESTING
        //$msg = $this->get('services')->serviceSendSMS("hello there monkey", "+50241550669");
        //var_dump($msg);die;

        //SPACE TOKEN
        //$token = $this->get('services')->getBCToken();
        //var_dump($token);die;

        //////GAMEBOARD.SPACE TESTING
        ///

        $getRewards = $this->get('services')->callBCSpace("GET", "rewards");
        print "<pre>";
        var_dump($getRewards);die;


        ////IBILLING TESTING
        //print "<pre>";
        //$getCustomer = $this->get('services')->callBCInfo("GET", "customer/295");
        //var_dump($getCustomer);die;


        $body = array(
                    array('name' => 'account', 'contents' => 'Alissa WhiteGluz50'),
                    array('name' => 'phone', 'contents' => '+18000006934')
                    )
            ;

        //$createCustomer = $this->get('services')->callBCInfo("POST", "customer", $body);
        //var_dump($createCustomer);die;


        return $this->render('BackendAdminBundle:Company:index.html.twig');
    }




}
