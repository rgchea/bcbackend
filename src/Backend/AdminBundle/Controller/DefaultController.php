<?php

namespace Backend\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Translation\Translator;


class DefaultController extends Controller
{


    protected $em;
    protected $translator;
    protected $repository;
    private  $renderer;
    private $session;
    private $userLogged;
    private $role;


    // Set up all necessary variable
    protected function initialise()
    {
        $this->session = new Session();
        $this->em = $this->getDoctrine()->getManager();
        $this->repository = $this->em->getRepository('BackendAdminBundle:Ticket');
        $this->translator = $this->get('translator');
        $this->renderer = $this->get('templating');
        $this->userLogged = $this->session->get('userLogged');
        $this->role = $this->session->get('userLogged')->getRole()->getName();


    }

    public function indexAction(Request $request)
    {


        $this->get("services")->setVars('dashboard');
        $this->initialise();

        $arrStats = array();

        if($this->translator->getLocale() == "es"){
            $arrMonths = array("Ene", "Feb", "Mar", "Abr", "May", "Jun", "Jul", "Ago", "Sep", "Oct", 'Nov', "Dic");
        }
        else{
            $arrMonths = array("Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", 'Nov', "Dec");
        }

        if($this->role == "ADMIN"){

            $complexID = $this->get("services")->getSessionComplex();
            $arrStats["tb_month"] = $this->em->getRepository("BackendAdminBundle:Ticket")->getTicketByYear($complexID);
            $arrStats["tb_current_month"] = $this->em->getRepository("BackendAdminBundle:Ticket")->getTicketByMonth($complexID);

            //var_dump($arrStats["tb_current_month"]);die;
            $arrStats["tb_manager"] = $this->em->getRepository("BackendAdminBundle:Ticket")->getTicketByManager($complexID);
            $arrStats["tb_category"] = $this->em->getRepository("BackendAdminBundle:Ticket")->getTicketByCategory($complexID);


            ///get points
            $player_id = $this->userLogged->getPlayerId();
            $token = $this->get('services')->getBCToken();

            $year = date("Y");
            $month = date("m");

            $stats =  $this->get('services')->callBCSpace( "GET", "players/".$player_id."/stats", array() );

            $currentLevel = intval($stats["current_level"]);
            $availablePoints = intval($stats["available_points"]);


            $arrStats["gxp"] = array();
            $arrStats["gxp"]["level"] = $currentLevel;
            $arrStats["gxp"]["points"] = $availablePoints;

            $arrStats["properties"] = $this->em->getRepository("BackendAdminBundle:Property")->getStats($complexID);
            $arrStats["users"] = $this->em->getRepository("BackendAdminBundle:User")->getStats($complexID);
            $arrStats["tickets"] = $this->em->getRepository("BackendAdminBundle:Ticket")->getStats($complexID);
            $arrStats["reservations"] = $this->em->getRepository("BackendAdminBundle:CommonAreaReservation")->getStats($complexID);

        }


        return $this->render('BackendAdminBundle:Default:index.html.twig', array(
            'role' => $this->role,
            'user' => $this->userLogged,
            'arrMonths' => $arrMonths,
            'arrStats' => $arrStats,
            'myPath' => 'backend_admin_homepage'

        ));
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


    public function listTickettablesAction(Request $request)
    {

        $this->get("services")->setVars('dashboard');

        // Set up required variables
        $this->initialise();


        // Get the parameters from DataTable Ajax Call
        if ($request->getMethod() == 'POST')
        {
            $draw = intval($request->request->get('draw'));
            $start = $request->request->get('start');
            $length = $request->request->get('length');
            $search = $request->request->get('search');
            $orders = $request->request->get('order');
            $columns = $request->request->get('columns');

        }
        else // If the request is not a POST one, die hard
            die;


        $filterDates = array();
        $filterDates["start"] =  date("Y-m-d", strtotime("-3 months"));
        $filterDates["end"] =  date("Y-m-d");


        $filterComplex = $this->get("services")->getSessionComplex();

        // Process Parameters

        $results = $this->em->getRepository("BackendAdminBundle:Ticket")->getRequiredDTData($start, $length, $orders, $search, $columns, $filterComplex, null, $filterDates);
        $objects = $results["results"];
        $selected_objects_count = count($objects);

        $i = 0;
        $response = "";

        foreach ($objects as $key => $entity)
        {
            $response .= '["';

            $j = 0;
            $nbColumn = count($columns);
            foreach ($columns as $key => $column)
            {
                // In all cases where something does not exist or went wrong, return -
                $responseTemp = "-";

                switch($column['name'])
                {
                    case 'id':
                    {
                        $responseTemp = $entity->getId();

                        break;
                    }

                    case 'title':
                    {
                        $responseTemp = $entity->getTitle();
                        break;
                    }
                    case 'type':
                    {
                        if($entity->getIsPublic()){
                            $responseText  = $this->translator->getLocale() == "en" ? "Public" : "Público";
                            $responseTemp = "<span class='label label-primary'>".$responseText."</span>";
                        }
                        else{
                            $responseText  = $this->translator->getLocale() == "en" ? "Private" : "Privado";


                            $responseTemp = "<span class='label label-info'>".$responseText."</span>";
                        }



                        break;
                    }

                    case 'category':
                    {
                        $responseTemp = $entity->getTicketCategory()->getName();
                        break;
                    }
                    case 'status':
                    {
                        $responseText = $this->translator->getLocale() == "en" ? $entity->getTicketStatus()->getNameEN() : $entity->getTicketStatus()->getNameES();

                        $myStatus = $entity->getTicketStatus()->getNameEN();

                        if($myStatus == "Open"){
                            $responseTemp = "<span class='label label-warning'>".$responseText."</span>";
                        }
                        elseif ($myStatus == "Closed"){
                            $responseTemp = "<span class='label label-default'>".$responseText."</span>";
                        }
                        else{
                            $responseTemp = "<span class='label label-success'>".$responseText."</span>";
                        }

                        break;
                    }

                    case 'elapsed':
                    {

                        //$nowtime = date("Y-m-d");
                        $oldtime = $entity->getCreatedAt()->format('Y-m-d');
                        //$secs = $nowtime - $oldtime;
                        $elapsed = $this->get('services')->time_elapsed_A($oldtime);
                        $responseTemp = $elapsed;
                        break;
                    }

                    case 'actions':
                    {

                        $urlEdit = $this->generateUrl('backend_admin_ticket_edit', array('id' => $entity->getId()));
                        $edit = "<a href='".$urlEdit."'><i class='fa fa-wrench'></i><span class='item-label'></span></a>&nbsp;&nbsp;";

                        $responseTemp = $edit;
                        break;
                    }

                }

                // Add the found data to the json
                $response .= $this->get("services")->escapeJsonString($responseTemp);

                if(++$j !== $nbColumn)
                    $response .='","';
            }

            $response .= '"]';

            // Not on the last item
            if(++$i !== $selected_objects_count)
                $response .= ',';
        }
        $myItems = $response;
        //($request, $repository, $results, $myItems){
        $return = $this->get("services")->serviceDataTable($request, $this->repository, $results, $myItems);

        return $return;


    }







}
