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

        $baseurl = $request->getScheme() . '://' . $request->getHttpHost() . $request->getBasePath();
        //var_dump($baseurl);die;

        ///////TWILIO TESTING
        //$msg = $this->get('services')->serviceSendSMS("hello there monkey", "+50241550669");
        //var_dump($msg);die;


        //###SENDGRID TRANSACTIONAL TEST BEGINS


        $myJson = '{
                       "from":{
                          "email":"renato@pizotesoft.com"
                       },
                       "personalizations":[
                          {
                             "to":[
                                        {
                                           "email":"cheametal@gmail.com"
                                        }
                                     ],                              
                             "dynamic_template_data":{
                                "news":
                                    [
                                        {"article": "test", "text": "otro test"},
                                        {"article": "test1", "text": "otro test1"}
                                    ]
                    
                    
                             }
                          }
                       ],
                       "template_id":"d-38e4355a007149c983fbd19ee2f9856b"
                    }';


        $body = json_decode($myJson, true);
        //print "<pre>";
        //var_dump($body);die;
        $templateID = "d-38e4355a007149c983fbd19ee2f9856b";

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
