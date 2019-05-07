<?php

namespace Backend\AdminBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Translation\TranslatorInterface;


use Backend\AdminBundle\Entity\CommonAreaReservation;
use Backend\AdminBundle\Form\CommonAreaReservationType;

/**
 * CommonAreaReservation controller.
 *
 */
class CommonAreaReservationController extends Controller
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
        $this->repository = $this->em->getRepository('BackendAdminBundle:CommonAreaReservation');
        $this->translator = $this->get('translator');
        $this->renderer = $this->get('templating');
        $this->userLogged = $this->session->get('userLogged');
        $this->role = $this->session->get('userLogged')->getRole()->getName();


    }


    public function indexAction(Request $request)
    {


        //var_dump($this->translator->trans('label_welcome'));
        $this->get("services")->setVars('commonAreaReservation');
        $this->initialise();

        //print $this->translator->getLocale();die;

        return $this->render('BackendAdminBundle:CommonAreaReservation:index.html.twig');

    }


    public function listDatatablesAction(Request $request)
    {

        $this->get("services")->setVars('commonAreaReservation');

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

            $arrDate["start"] = $request->request->get('start_date');
            $arrDate["end"] = $request->request->get('end_date');


        }
        else // If the request is not a POST one, die hard
            die;


        ///FILTER BY ROLE
        $filters = null;
        if($this->role != "SUPER ADMIN"){
            $arrComplex = $this->em->getRepository('BackendAdminBundle:Complex')->getComplexByUser($this->userLogged->getId());
            foreach ($arrComplex as $k =>$v) {
                $filters[$v] = $v;//the complex id
            }
        }


        // Further filtering can be done in the Repository by passing necessary arguments
        if(trim($arrDate["start"]) != "" && trim($arrDate["end"]) != ""){
            $dateConditions = $arrDate;
        }
        else{
            $dateConditions = null;
        }



        // Process Parameters
        $businessLocale = $this->translator->getLocale();
        $results = $this->repository->getRequiredDTData($start, $length, $orders, $search, $columns, $filters, $dateConditions, $businessLocale);
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

                    case 'user':
                        {
                            $responseTemp = $entity->getReservedBy()->getName();
                            break;
                        }

                    case 'complex':
                        {
                            $responseTemp = $entity->getCommonArea()->getComplex()->getName();
                            break;
                        }
                    case 'commonArea':
                        {
                            $responseTemp = $entity->getCommonArea()->getName();
                            break;
                        }
                    case 'dateFrom':
                        {
                            $responseTemp = $entity->getReservationDateFrom()->format('Y-m-d  H:i:s');
                            break;
                        }
                    case 'dateTo':
                        {
                            $responseTemp = $entity->getReservationDateTo()->format('Y-m-d  H:i:s');
                            break;
                        }


                    case 'status':
                        {
                            $responseTemp = $entity->getCommonAreaReservationStatus();
                            break;
                        }

                    case 'actions':
                        {

                            $urlEdit = $this->generateUrl('backend_admin_common_area_reservation_approve', array('id' => $entity->getId()));
                            $edit = "<a href='".$urlEdit."'><i style='font-size: 20px' class='fas fa-thumbs-up'></i><span class='item-label'></span></a>&nbsp;&nbsp;&nbsp;&nbsp;";

                            $urlDelete = $this->generateUrl('backend_admin_common_area_reservation_deny', array('id' => $entity->getId()));
                            $delete = "<a href='".$urlDelete."'><i style='font-size: 20px' class='fas fa-thumbs-down'></i><span class='item-label'></span></a>";

                            $responseTemp = $edit.$delete;
                            break;
                        }


                }

                // Add the found data to the json
                $response .= $responseTemp;

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



    public function approveAction(Request $request, $id)
    {
        /*
        print "<pre>";
        var_dump(json_decode($_REQUEST["my_schedule"], true));
        die;
        */

        $this->get("services")->setVars('commonAreaReservation');
        $this->initialise();

        $entity = $this->em->getRepository('BackendAdminBundle:CommonAreaReservation')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find CommonArea entity.');
        }

        $entity->setCommonAreaReservationStatus($this->em->getRepository('BackendAdminBundle:CommonAreaReservationStatus')->find(2));

        $this->get("services")->blameOnMe($entity, "update");
        $this->em->flush();

        $this->get('services')->flashSuccess($request);
        return $this->redirect($this->generateUrl('backend_admin_common_area_reservation_index'));

    }


    public function denyAction(Request $request, $id)
    {
        /*
        print "<pre>";
        var_dump(json_decode($_REQUEST["my_schedule"], true));
        die;
        */

        $this->get("services")->setVars('commonAreaReservation');
        $this->initialise();

        $entity = $this->em->getRepository('BackendAdminBundle:CommonAreaReservation')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find CommonArea entity.');
        }

        $entity->setCommonAreaReservationStatus($this->em->getRepository('BackendAdminBundle:CommonAreaReservationStatus')->find(3));

        $this->get("services")->blameOnMe($entity, "update");
        $this->em->flush();

        $this->get('services')->flashSuccess($request);
        return $this->redirect($this->generateUrl('backend_admin_common_area_reservation_index'));

    }



    public function calendarAction(Request $request)
    {
        /*
        print "<pre>";
        var_dump(json_decode($_REQUEST["my_schedule"], true));
        die;
        */

        $this->get("services")->setVars('commonAreaReservation');
        $this->initialise();


        ///FILTER BY ROLE
        $filters = null;
        if($this->role != "SUPER ADMIN"){
            $arrComplex = $this->em->getRepository('BackendAdminBundle:Complex')->getComplexByUser($this->userLogged->getId());
            foreach ($arrComplex as $k =>$v) {
                $filters[$v] = $v;//the complex id
            }
        }


        $schedule = $this->em->getRepository('BackendAdminBundle:CommonAreaReservation')->getSchedule($filters);
        /*
        print "<pre>";
        var_dump($schedule);die;
        */


        return $this->render('BackendAdminBundle:CommonAreaReservation:calendar.html.twig', ['now' => date("Y-m-d"), 'schedule' => $schedule]);

    }




}

