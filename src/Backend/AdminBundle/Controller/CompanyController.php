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



    public function indexAction(Request $request)
    {

        $session = new Session();
        //$this->get("services")->setVars('company');
        $this->initialise();

        //print $this->translator->trans('label_ticket_solved');die;

        //$tmp = $this->em->getRepository('BackendAdminBundle:CommonAreaAvailability')->getCommonAreaAvailability(5, '2019-08-26');
        //die;

        //return $this->render('BackendAdminBundle:Company:index.html.twig');

        //TESTING PUSH NOTIFICATIONS
        $myUser = $this->em->getRepository("BackendAdminBundle:User")->find(34);
        //var_dump($myUser->getName());die;

        $this->get("services")->sendPushNotification($myUser, "test", "test");
        die;


        $ts1 = strtotime("2019-01-01");
        $ts2 = strtotime("2019-08-01");

        $year1 = date('Y', $ts1);
        $year2 = date('Y', $ts2);

        $month1 = date('m', $ts1);
        $month2 = date('m', $ts2);

        $diff = (($year2 - $year1) * 12) + ($month2 - $month1);

        var_dump($diff);
        die;



        $baseurl = $request->getScheme() . '://' . $request->getHttpHost() . $request->getBasePath();
        //var_dump($baseurl);die;


        $timezone  = -5; //(GMT -5:00) EST (U.S. & Canada)

        $myCreationTime = gmdate("Y-m-d H:i:s", time() + 3600*($timezone+date("I")));
        $myTime = gmdate("H:i", time() + 3600*($timezone+date("I")));
        $weekDay = date('w', strtotime($myCreationTime));
        $arrWeekDays = array(0 => 6, 1 => 0, 2 => 1, 3 => 2, 4 => 3, 5 => 4, 6 => 5);
        $weekDaySingle = $arrWeekDays[$weekDay];

        print "<pre>";
        //var_dump($myCreationTime);

        $myShift = $this->em->getRepository('BackendAdminBundle:Shift')->getUsertoAssignTicket($timezone, 4);

        var_dump($myShift);die;

        die;


        ////testing tickets
        //$this->em->getRepository('BackendAdminBundle:TicketCategory')->loadTicketCategories(12);
        //die;


        ///////TWILIO TESTING
        //$msg = $this->get('services')->serviceSendSMS("hello there monkey", "+50241550669");
        //var_dump($msg);die;


        //###SENDGRID TRANSACTIONAL TEST BEGINS


        $myJson = '';


        $templateID = "d-8c65067739ed4fd3bf79ab31650b47f8";

        //$sendgridResponse = $this->get('services')->callSendgrid($body, $templateID);
        //var_dump($sendgridResponse);die;

        //###SENDGRID TRANSACTIONAL TEST ENDS



        ## GET SPACE TOKEN
        //$token = $this->get('services')->getBCToken();
        //var_dump($token);die;


        //////GAMEBOARD.SPACE TESTING
        ///

        ## Get info for given username
        print "<pre>";

        //$getUserInfo = $this->get('services')->callBCSpace("GET", "me?_switch_user=pizote");
        //var_dump($getUserInfo);die;

        //complete INFO

        //$getUserInfo = $this->get('services')->callBCSpace("GET", "me?_switch_user=pizote&complete=true");
        //var_dump($getUserInfo);die;


        ## Create a new user
        $body = array();
        $body['email'] = "zorrillo1@pizotesoft.com";
        $body['username'] = "zorrillo1";
        $body['firstName'] = "zorrillo1";
        $body['lastName'] = "soft";
        $body['locale'] = "es";

        $createUser = $this->get('services')->callBCSpace("POST", "users", $body);
        var_dump($createUser);die;


        ## Create a new Team
        $body = array();
        $body['name'] = "Pizote Team X1.1";
        $body['description'] = "Pizote testing";
        $body['teamType'] = 5;
        $body['parent'] = 27;

        $createTeam = $this->get('services')->callBCSpace("POST", "teams", $body);
        var_dump($createTeam);die;


        ## Add user to team
        $body = array();
        //$userTeam = $this->get('services')->callBCSpace("POST", "users/pizote/teams/29", $body);
        //var_dump($userTeam);die;


        ## Pwn a task
        //Considerar que el player debe pertenecer al equipo y la tarea (play) también debe pertenecer al mismo equipo para poder ejecutarse la tarea.
        $body = array();
        $body['name'] = "Descripción de la jugada";

        //$pwnandplay = $this->get('services')->callBCSpace("POST", "teams/27/players/42/pwn/25", $body);
        //var_dump($pwnandplay);die;


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
