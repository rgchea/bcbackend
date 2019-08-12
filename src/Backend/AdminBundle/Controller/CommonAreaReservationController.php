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

        return $this->render('BackendAdminBundle:CommonAreaReservation:index.html.twig', array('myPath' => 'backend_admin_common_area_reservation_index'));

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
            /*
            $arrComplex = $this->em->getRepository('BackendAdminBundle:Complex')->getComplexByUser($this->userLogged->getId());
            foreach ($arrComplex as $k =>$v) {
                $filters[$v] = $v;//the complex id
            }
            */
            $filterComplex = $this->get("services")->getSessionComplex();
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
        $results = $this->repository->getRequiredDTData($start, $length, $orders, $search, $columns, $filterComplex, $dateConditions, $businessLocale);
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
                            $responseTemp = $entity->getReservedBy()->getEmail();
                            break;
                        }

                    case 'property':
                        {
                            $responseTemp = $entity->getProperty()->getName();
                            break;
                        }
                    case 'commonArea':
                        {
                            $responseTemp = $entity->getCommonArea()->getName();
                            break;
                        }
                    case 'eventDate':
                        {
                            $responseTemp = $entity->getReservationDateFrom()->format('H:i m/d/Y')."<br>". $entity->getReservationDateTo()->format('H:i m/d/Y');
                            break;
                        }

                    case 'requested':
                        {

                            if($entity->getCreatedAt() != NULL){
                                $responseTemp = $entity->getCreatedAt()->format('m/d/Y H:i');
                            }
                            else{
                                $responseTemp = "--";
                            }

                            break;
                        }

                    case 'approved':
                        {

                            if($entity->getApproved() != NULL){
                                $responseTemp = $entity->getApproved()->format('m/d/Y H:i');
                            }
                            else{
                                $responseTemp = "--";
                            }

                            break;
                        }

                    case 'status':
                        {
                            $status = $entity->getCommonAreaReservationStatus();
                            if($status == "Pending" || $status == "Pendiente"){
                                $responseTemp = "<button type='button' class='btn btn-warning btn-xs'>".$status."</button>";
                            }

                            if($status == "Approved" || $status == "Aprobado"){
                                $responseTemp = "<button type='button' class='btn btn-success btn-xs'>".$status."</button>";
                            }

                            if($status == "Rejected" || $status == "Rechazado"){
                                $responseTemp = "<button type='button' class='btn btn-danger btn-xs'>".$status."</button>";
                            }


                            break;
                        }

                    case 'actions':
                        {


                            /*

                            if($entity->getCommonAreaReservationStatus()->getId() == 1){
                                $urlEdit = $this->generateUrl('backend_admin_common_area_reservation_approve', array('id' => $entity->getId()));
                                $edit = "<a href='".$urlEdit."'><i style='font-size: 20px' class='fas fa-thumbs-up'></i><span class='item-label'></span></a>&nbsp;&nbsp;&nbsp;&nbsp;";

                                $urlDelete = $this->generateUrl('backend_admin_common_area_reservation_deny', array('id' => $entity->getId()));
                                $delete = "<a href='".$urlDelete."'><i style='font-size: 20px' class='fas fa-thumbs-down'></i><span class='item-label'></span></a>";

                                $responseTemp = $edit.$delete;


                            }
                            else{
                                $responseTemp = "";
                            }
                            */


                            $urlEdit = $this->generateUrl('backend_admin_common_area_reservation_edit', array('id' => $entity->getId()));
                            $edit = "<a href='".$urlEdit."'><i class='fa fa-wrench'></i><span class='item-label'></span></a>&nbsp;&nbsp;";

                            $responseTemp = $edit;


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

        $start = $entity->getReservationDateFrom()->format('Y-m-d H:i:s');
        $end = $entity->getReservationDateTo()->format('Y-m-d H:i:s');

        //$validateSchedule = $this->em->getRepository('BackendAdminBundle:CommonAreaReservation')->validateSchedule($start, $end, $entity->getCommonArea()->getId(), $id);

        $entity->setCommonAreaReservationStatus($this->em->getRepository('BackendAdminBundle:CommonAreaReservationStatus')->find(2));
        $gtmNow = gmdate("Y-m-d H:i:s");
        $entity->setApproved(new \DateTime($gtmNow));
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


    public function notificationAction(Request $request){

        $this->get("services")->setVars('dashboard');
        $this->initialise();

        if($this->role == "SUPER ADMIN"){

            return new JsonResponse(array("result" => 0));
        }
        ///FILTER BY ROLE
        $complexFilters = null;
        if($this->role != "SUPER ADMIN"){
            $arrComplex = $this->em->getRepository('BackendAdminBundle:Complex')->getComplexByUser($this->userLogged->getId());
            foreach ($arrComplex as $k =>$v) {
                $complexFilters[$v] = $v;//the complex id
            }
        }



        $notifications = $this->em->getRepository('BackendAdminBundle:CommonAreaReservation')->getNotification($this->userLogged->getId(), $complexFilters);
        $arrReturn = array();
        if(!empty($notifications)){
            foreach ($notifications as $key => $notification) {

                //var_dump($key);die;
                $arrReturn[$key] = $notification;
            }
        }

        return new JsonResponse(array("result" => $arrReturn));

    }



    public function notificationReadAction(Request $request){

        $this->get("services")->setVars('dashboard');
        $this->initialise();

        //var_dump($_REQUEST);DIE;
        $notificationID = intval($_REQUEST["notificationID"]);

        $objNotification = $this->em->getRepository('BackendAdminBundle:UserNotification')->find($notificationID);
        $objNotification->setIsRead(1);

        $this->em->persist($objNotification);
        $this->em->flush();

        return $this->redirect($this->generateUrl('backend_admin_common_area_reservation_index'));

    }


    public function notificationMarkAllReadAction(Request $request){

        $this->get("services")->setVars('dashboard');
        $this->initialise();

        ///FILTER BY ROLE
        $complexFilters = null;
        if($this->role != "SUPER ADMIN"){
            $arrComplex = $this->em->getRepository('BackendAdminBundle:Complex')->getComplexByUser($this->userLogged->getId());
            foreach ($arrComplex as $k =>$v) {
                $complexFilters[$v] = $v;//the complex id
            }
        }


        $notifications = $this->em->getRepository('BackendAdminBundle:CommonAreaReservation')->markAllNotificationRead($this->userLogged->getId(), $complexFilters);

        return $this->redirect($this->generateUrl('backend_admin_homepage'));

    }



    public function editAction(Request $request, $id)
    {
        $this->get("services")->setVars('commonAreaReservation');
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('BackendAdminBundle:CommonAreaReservation')->find($id);

        $deleteForm = $this->createDeleteForm($entity);
        $editForm = $this->createEditForm($entity);

        $editForm->handleRequest($request);



        return $this->render('BackendAdminBundle:CommonAreaReservation:edit.html.twig', array(
            'entity' => $entity,
            'form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
            'edit' => $entity->getId(),
        ));
    }



    public function newAction(Request $request)
    {

        $this->get("services")->setVars('commonAreaReservation');
        $this->initialise();

        $entity = new CommonAreaReservation();
        $form   = $this->createCreateForm($entity);

        return $this->render('BackendAdminBundle:CommonAreaReservation:new.html.twig', array(
            'entity' => $entity,
            'form' => $form->createView(),
            'new' => true,
            'myPath' => 'backend_admin_common_area_reservation_new'

        ));
    }


    /**
     * Deletes a Ticket entity.
     *
     */
    public function deleteAction(Request $request, $id)
    {

        $this->get("services")->setVars('commonAreaReservation');
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('BackendAdminBundle:CommonAreaReservation')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Booking entity.');
        }
        else{

            //SOFT DELETE
            $entity->setEnabled(0);
            $this->get("services")->blameOnMe($entity);
            $em->persist($entity);
            $em->flush();

        }



        $this->get('services')->flashSuccess($request);
        return $this->redirectToRoute('backend_admin_common_area_reservation_index');
    }

    /**
     * Creates a form to delete a Ticket entity.
     *
     * @param Ticket
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($entity)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('backend_admin_common_area_reservation_delete', array('id' => $entity->getId())))
            ->setMethod('DELETE')
            ->getForm()
            ;
    }




    /**
     * Creates a new Ticket entity.
     *
     */
    public function createAction(Request $request)
    {

        //print "<pre>";
        //var_dump($_REQUEST);DIE;

        $this->get("services")->setVars('commonAreaReservation');
        $this->initialise();


        $entity = new CommonAreaReservation();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);
        /*print "<pre>";
        var_dump($form->getErrorsAsString());die;
         * */

        if ($form->isValid()) {



            //BLAME ME
            $this->get("services")->blameOnMe($entity, "create");

            $this->em->persist($entity);
            $this->em->flush();



            $this->get('services')->flashSuccess($request);
            return $this->redirect($this->generateUrl('backend_admin_common_area_reservation_index'));

        }
        /*
        else{
            print "FORMULARIO NO VALIDO";DIE;
        }
         * */

        return $this->render('BackendAdminBundle:CommonAreaReservation:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a form to create a Ticket entity.
     *
     * @param Ticket $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm($entity)
    {
        $this->get("services")->setVars('commonAreaReservation');
        $this->initialise();
        $form = $this->createForm(CommonAreaReservationType::class, $entity, array(
            'action' => $this->generateUrl('backend_admin_common_area_reservation_create'),
            'method' => 'POST',

        ));


        return $form;
    }




    /**
     * Creates a form to edit a Ticket entity.
     *
     * @param Ticket $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm($entity)
    {
        //print "entra";die;
        $this->get("services")->setVars('commonAreaReservation');
        $this->initialise();

        //var_dump($this->em->getRepository('BackendAdminBundle:Complex'));die;
        /*
        $form = $this->createForm(CommonAreaReservationType::class, $entity, array(
            'action' => $this->generateUrl('backend_admin_ticket_update',
                array('id' => $entity->getId(),
                    'role' => $this->role,
                    'userID' => $this->userLogged->getId(),
                    'repository' => $this->em->getRepository('BackendAdminBundle:Complex')
                )),
        ));
        */

        $form = $this->createForm(CommonAreaReservationType::class, $entity, array(
            'action' => $this->generateUrl('backend_admin_common_area_reservation_update', array('id' => $entity->getId())),
            //'role' => $this->role,
        ));



        return $form;
    }


    /**
     * Edits an existing Ticket entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $this->get("services")->setVars('commonAreaReservation');
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('BackendAdminBundle:CommonAreaReservation')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Ticket entity.');
        }

        $deleteForm = $this->createDeleteForm($entity);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {


            //BLAME ME
            $this->get("services")->blameOnMe($entity, "create");
            $this->em->persist($entity);
            $this->em->flush();

            $this->get('services')->flashSuccess($request);
            return $this->redirect($this->generateUrl('backend_admin_common_area_reservation_index', array('id' => $id)));

        }

        //$countries = $this->em->getRepository('BackendAdminBundle:GeoCountry')->findBy(array("enabled" => 1));

        return $this->render('BackendAdminBundle:CommonAreaReservation:edit.html.twig', array(
            'entity'      => $entity,
            'form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
            //'countries' => $countries
        ));
    }



}

