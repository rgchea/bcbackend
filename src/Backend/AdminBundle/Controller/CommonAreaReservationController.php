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
use Backend\AdminBundle\Entity\PropertyContractTransaction;
use Backend\AdminBundle\Entity\BookingLog;
use Backend\AdminBundle\Entity\BookingComment;
use Backend\AdminBundle\Entity\UserNotification;
use Backend\AdminBundle\Entity\Ticket;
use Symfony\Component\Validator\Constraints\DateTime;

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
                                $responseTemp = "<span class='label label-warning'>".$status."</span>";
                            }

                            if($status == "Approved" || $status == "Aprobado"){
                                $responseTemp = "<span class='label label-success'>".$status."</span>";
                            }

                            if($status == "Rejected" || $status == "Rechazado"){
                                $responseTemp = "<span class='label label-default'>".$status."</span>";
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

        //$start = $entity->getReservationDateFrom()->format('Y-m-d H:i:s');
        //$end = $entity->getReservationDateTo()->format('Y-m-d H:i:s');

        //$validateSchedule = $this->em->getRepository('BackendAdminBundle:CommonAreaReservation')->validateSchedule($start, $end, $entity->getCommonArea()->getId(), $id);

        $entity->setCommonAreaReservationStatus($this->em->getRepository('BackendAdminBundle:CommonAreaReservationStatus')->find(2));
        $gtmNow = gmdate("Y-m-d H:i:s");
        $entity->setApproved(new \DateTime($gtmNow));
        $this->get("services")->blameOnMe($entity, "update");

        $this->em->persist($entity);

        ////CREATE USER NOTIFICATION
        $objUserNotification = New UserNotification();
        $objUserNotification->setCommonAreaReservation($entity);
        $type = $this->em->getRepository('BackendAdminBundle:NotificationType')->findOneById(1);//TYPE=RESERVATION
        $objUserNotification->setNotificationType($type);
        $objUserNotification->setIsRead(0);
        $objUserNotification->setEnabled(1);
        $title = $this->userLogged->getName();
        $objUserNotification->setTitle($title);
        $description = $this->translator->trans("label_booking"). " #".$entity->getId(). " ". $this->translator->trans('label_approved');
        $objUserNotification->setDescription($description);
        $objUserNotification->setNotice("");
        $objUserNotification->setSentTo($entity->getReservedBy());


        $this->get("services")->blameOnMe($objUserNotification, "create");
        $this->get("services")->blameOnMe($objUserNotification, "update");

        $this->em->persist($objUserNotification);

        ///registro a log de reservaciones
        $bookingLog = new BookingLog();
        $bookingLog->setCommonAreaReservation($entity);
        $bookingLog->setStatus("label_approved");

        //BLAME ME
        $this->get("services")->blameOnMe($bookingLog, "create");
        $this->get("services")->blameOnMe($bookingLog, "update");

        $this->em->persist($bookingLog);

        $this->em->flush();

        //ADD POINTS
        $message = $description;
        $playKey = "BC-A-00006";//approve booking
        $this->get("services")->addPointsAdmin($entity->getCommonArea()->getComplex(), $message, $playKey);

        $title = $this->translator->trans("label_booking")." #".$entity->getId();
        $description = $entity->getCommonArea()->getName().": ". $this->translator->trans("push.reservation_approved");
        $this->get("services")->sendPushNotification($entity->getCreatedBy(), $title, $description);



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
        $this->em->persist($entity);

        ////CREATE USER NOTIFICATION
        $objUserNotification = New UserNotification();
        $objUserNotification->setCommonAreaReservation($entity);
        $type = $this->em->getRepository('BackendAdminBundle:NotificationType')->findOneById(1);//TYPE=RESERVATION
        $objUserNotification->setNotificationType($type);
        $objUserNotification->setIsRead(0);
        $objUserNotification->setEnabled(1);
        $title = $this->userLogged->getName();
        $objUserNotification->setTitle($title);
        $description = $this->translator->trans("label_booking"). " #".$entity->getId(). " ". $this->translator->trans('label_rejected');
        $objUserNotification->setDescription($description);
        $objUserNotification->setNotice("");
        $objUserNotification->setSentTo($entity->getReservedBy());


        $this->get("services")->blameOnMe($objUserNotification, "create");
        $this->get("services")->blameOnMe($objUserNotification, "update");


        $this->em->persist($objUserNotification);

        ///registro a log de reservaciones
        $bookingLog = new BookingLog();
        $bookingLog->setCommonAreaReservation($entity);
        $bookingLog->setStatus("label_rejected");

        //BLAME ME
        $this->get("services")->blameOnMe($bookingLog, "create");
        $this->get("services")->blameOnMe($bookingLog, "update");

        $this->em->persist($bookingLog);

        $this->em->flush();

        $title = $this->translator->trans("label_booking")." #".$entity->getId();
        $description = $entity->getCommonArea()->getName().": ". $this->translator->trans("push.reservation_rejected");
        $this->get("services")->sendPushNotification($entity->getCreatedBy(), $title, $description);


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
        ///
        $filters = array();
        /*
        if($this->role != "SUPER ADMIN"){
            $arrComplex = $this->em->getRepository('BackendAdminBundle:Complex')->getComplexByUser($this->userLogged->getId());
            foreach ($arrComplex as $k =>$v) {
                $filters[$v] = $v;//the complex id
            }
        }
        */
        $myComplex = intval($this->get("services")->getSessionComplex());
        $filters[$myComplex] = $myComplex;


        $schedule = $this->em->getRepository('BackendAdminBundle:CommonAreaReservation')->getSchedule($filters);

        //print "<pre>";
        //var_dump($schedule);die;



        return $this->render('BackendAdminBundle:CommonAreaReservation:calendar.html.twig',
            ['now' => date("Y-m-d"),
                'schedule' => $schedule,
                'myPath' => 'backend_admin_common_area_reservation_calendar',]);

    }


    public function editAction(Request $request, $id)
    {
        $this->get("services")->setVars('commonAreaReservation');
        $this->initialise();

        $entity = $this->em->getRepository('BackendAdminBundle:CommonAreaReservation')->find($id);

        if(!$entity){
            throw $this->createNotFoundException('Not found.');
        }

        //users cannot view private complexes
        $this->get("services")->checkComplexAccess($entity->getCommonArea()->getComplex()->getId());


        $reservationID = $entity->getId();
        $payment = $this->em->getRepository('BackendAdminBundle:PropertyContractTransaction')->findOneByCommonAreaReservation($reservationID);
        $bookingComments = $this->em->getRepository('BackendAdminBundle:BookingComment')->findBy(array('commonAreaReservation' => $reservationID, 'enabled' => 1), array('id' => 'DESC'));

        return $this->render('BackendAdminBundle:CommonAreaReservation:edit.html.twig', array(
            'entity' => $entity,
            'edit' => $entity->getId(),
            'payment' => $payment,
            'bookingComments' => $bookingComments
        ));
    }



    public function newAction(Request $request)
    {

        $this->get("services")->setVars('commonAreaReservation');
        $this->initialise();

        $entity = new CommonAreaReservation();
        $form   = $this->createCreateForm($entity);

        $myComplexID = $this->get("services")->getSessionComplex();

        $commonArea = $this->em->getRepository("BackendAdminBundle:CommonArea")->findBy(array('complex'=> $myComplexID, 'enabled' => 1));
        $complexSector = $this->em->getRepository("BackendAdminBundle:ComplexSector")->findBy(array('complex'=> $myComplexID, 'enabled' => 1));

        return $this->render('BackendAdminBundle:CommonAreaReservation:new.html.twig', array(
            'entity' => $entity,
            'form' => $form->createView(),
            'new' => true,
            'myPath' => 'backend_admin_common_area_reservation_new',
            'complexSector' => $complexSector,
            'commonArea' => $commonArea

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
     * Creates a new Reservation entity.
     *
     */
    public function createAction(Request $request)
    {

        //print "<pre>";
        //var_dump($_REQUEST);DIE;

        $this->get("services")->setVars('commonAreaReservation');
        $this->initialise();

        $objProperty = $this->em->getRepository('BackendAdminBundle:Property')->find(intval($_REQUEST["property_select"]));
        $objArea = $this->em->getRepository('BackendAdminBundle:CommonArea')->find(intval($_REQUEST["area_select"]));
        $objStatus = $this->em->getRepository('BackendAdminBundle:CommonAreaReservationStatus')->find(1);

        $timeFrom = substr($_REQUEST["time"], 0, 5) ;
        $timeTo = substr($_REQUEST["time"], 6, 5) ;

        $dateFrom = $_REQUEST["event_date"]." ".$timeFrom;
        $dateTo = $_REQUEST["event_date"]." ".$timeTo;

        $entity = new CommonAreaReservation();
        $entity->setReservedBy($objProperty->getMainTenant());
        $entity->setProperty($objProperty);
        $entity->setCommonArea($objArea);
        $entity->setCommonAreaReservationStatus($objStatus);
        $entity->setReservationDateFrom(new \DateTime($dateFrom));
        $entity->setReservationDateTo(new \DateTime($dateTo));
        $entity->setEnabled(1);

        $propertyContract = $this->em->getRepository('BackendAdminBundle:PropertyContract')->findOneBy(array("property" => $objProperty->getId(), 'propertyTransactionType' => 3, "enabled" => 1, 'isActive' => 1), array("id"=> "DESC"));
        $objTenantContract = $propertyContract->getMainTenantContract();
        $entity->setTenantContract($objTenantContract);


        //BLAME ME
        $this->get("services")->blameOnMe($entity, "create");
        $this->get("services")->blameOnMe($entity, "update");

        $this->em->persist($entity);

        //ADD POINTS
        $description = $this->translator->trans("label_new")." ".$this->translator->trans("label_booking"). " #".$entity->getId();
        $message = $description;
        $playKey = "BC-A-00007";//add booking
        $this->get("services")->addPointsAdmin($entity->getCommonArea()->getComplex(), $message, $playKey);

        ///PAYMENT
        $cost = floatval($_REQUEST["cost"]);
        $discount = floatval($_REQUEST["discount"]);
        $amountPaid = floatval($_REQUEST["amount_paid"]);

        ///generar un pago si montos son diferentes a 0
        ///

        if($cost > 0){

            $transactionType = $this->em->getRepository('BackendAdminBundle:PropertyTransactionType')->find(4);//reservacion

            $payment = new PropertyContractTransaction();
            $payment->setEnabled(1);
            $payment->setComplex($objProperty->getComplex());
            $payment->setPropertyContract($propertyContract);
            $payment->setPropertyTransactionType($transactionType);
            $payment->setCommonAreaReservation($entity);
            $payment->setDescription($entity->getCommonArea()->getName()." ". number_format($cost, 2, ".", "") );
            $payment->setPaymentAmount($cost);
            $payment->setPaidAmount($amountPaid);
            $payment->setDiscount($discount);

            ///paid & paid date
            $payment->setPaidBy($propertyContract->getProperty()->getMainTenant());
            $gtmNow = gmdate("Y-m-d H:i:s");
            $payment->setPaidDate(new \DateTime($gtmNow));
            $payment->setDueDate(new \DateTime($gtmNow));
            //status
            $payment->setStatus(1);

            //BLAME ME
            $this->get("services")->blameOnMe($payment, "create");
            $this->get("services")->blameOnMe($payment, "update");

            $this->em->persist($payment);

        }

        ///registro a log de reservaciones
        $bookingLog = new BookingLog();
        $bookingLog->setCommonAreaReservation($entity);
        $bookingLog->setStatus("label_pending");

        //BLAME ME
        $this->get("services")->blameOnMe($bookingLog, "create");
        $this->get("services")->blameOnMe($bookingLog, "update");

        $bookingLog->setCreatedBy($entity->getReservedBy());

        $this->em->persist($bookingLog);

        /////CREATE TICKET


        $objTicketType = $this->em->getRepository('BackendAdminBundle:TicketType')->find(3);//RESERVATION
        $status = $this->em->getRepository('BackendAdminBundle:TicketStatus')->findOneById(3);//SOLVED
        $objComplex = $objProperty->getComplex();



        $ticket = new Ticket();
        $ticket->setCommonAreaReservation($entity);
        $ticket->setTicketType($objTicketType);
        $title = $entity->getCommonArea()->getName();
        $ticket->setTitle($title);
        $ticket->setDescription($this->translator->trans('label_reservation')." ". $entity->getId());
        $ticket->setPossibleSolution("");
        $ticket->setIsPublic(false);

        $myLocale = $objComplex->getGeoState()->getGeoCountry()->getLocale();
        $name = $myLocale == "en" ? "Common Area" : "Área común";

        $ticketCategory = $this->em->getRepository('BackendAdminBundle:TicketCategory')->findOneBy(array("complex" => $objComplex->getId(), 'name' => $name, "enabled" => 1));
        if(!$ticketCategory){
            $ticketCategory = $this->em->getRepository('BackendAdminBundle:TicketCategory')->findOneBy(array("iconUnicode" => "f78c", 'name' => $name, "enabled" => 1));
        }

        $ticket->setTicketCategory($ticketCategory);
        $ticket->setComplexSector($objProperty->getComplexSector());

        $ticket->setComplex($objComplex);
        $ticket->setProperty($objProperty);
        //$ticket->setCommonAreaReservation($commonAreaReservation);

        $tenantContract =  null;
        $propertyContract = $this->em->getRepository('BackendAdminBundle:PropertyContract')->findOneBy(array("property" => $objProperty->getId(), 'propertyTransactionType' => 3, "enabled" => 1, 'isActive' => 1), array("id"=> "DESC"));
        if($propertyContract) {
            $tenantContract = $propertyContract->getMainTenantContract();
        }

        $ticket->setTenantContract($tenantContract);
        $ticket->setTicketStatus($status);
        $ticket->setEnabled(true);


        //setAssignedTo
        //$timezone  = -5; //(GMT -5:00) EST (U.S. & Canada)
        $timezone = str_replace("GMT", '', $objComplex->getBusiness()->getGeoState()->getTimezoneOffset());
        $userToAssign = $this->em->getRepository('BackendAdminBundle:Shift')->getUsertoAssignTicket($timezone, $objComplex->getId());


        $ticket->setAssignedTo($userToAssign);

        $this->get("services")->blameOnMe($ticket, "create");
        $this->get("services")->blameOnMe($ticket, "update");

        $myMainTenant = $objProperty->getMainTenant();
        $ticket->setCreatedBy();

        $this->em->persist($ticket);

        ////CREATE USER NOTIFICATION
        $objUserNotification = New UserNotification();
        $objUserNotification->setCommonAreaReservation($entity);
        $type = $this->em->getRepository('BackendAdminBundle:NotificationType')->findOneById(1);//TYPE=RESERVATION
        $objUserNotification->setNotificationType($type);
        $objUserNotification->setIsRead(0);
        $objUserNotification->setEnabled(1);
        $title = $myMainTenant->getName();
        $objUserNotification->setTitle($title);
        $description = $this->translator->trans('label_booking')." #".$entity->getId();
        $objUserNotification->setDescription($description);
        $objUserNotification->setNotice("");
        $objUserNotification->setSentTo($userToAssign);

        $this->get("services")->blameOnMe($objUserNotification, "create");
        $this->get("services")->blameOnMe($objUserNotification, "update");
        $this->em->persist($objUserNotification);

        $this->em->flush();


        $this->get('services')->flashSuccess($request);
        return $this->redirect($this->generateUrl('backend_admin_common_area_reservation_index'));


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



    public function getAvailabilityAction(Request $request){


        $this->get("services")->setVars('commonAreaReservation');
        $this->initialise();

        $areaID = intval($_REQUEST["area_id"]);
        $date = $_REQUEST["event_date"];


        $arrAvailability = $this->em->getRepository('BackendAdminBundle:CommonAreaAvailability')->getCommonAreaAvailability($areaID, $date);

        $arrReturn = array();
        foreach ($arrAvailability as $availability){

            $arrReturn[]["hour"] = $availability["hour_from"]." ".$availability["hour_to"];

        }

        //var_dump($arrReturn);die;

        return new JsonResponse($arrReturn);



    }


    public function getCostAction(Request $request){

        $this->get("services")->setVars('commonAreaReservation');
        $this->initialise();


        $objCommonArea = $this->em->getRepository('BackendAdminBundle:CommonArea')->find(intval($_REQUEST["area_id"]));

        if($objCommonArea->getRequiredPayment()){
            $cost = floatval($objCommonArea->getPrice()) ;
        }
        else{
            $cost = "0.00";
        }


        return new JsonResponse(array("cost" => $cost));

    }


    public function updatePaymentAction(Request $request){

        $this->get("services")->setVars('commonAreaReservation');
        $this->initialise();

        $payment = $this->em->getRepository('BackendAdminBundle:PropertyContractTransaction')->find(intval($_REQUEST["payment_id"]));

        ///PAYMENT
        $cost = floatval($_REQUEST["cost"]);
        $discount = floatval($_REQUEST["discount"]);
        $amountPaid = floatval($_REQUEST["amount_paid"]);

        $payment->setPaymentAmount($cost);
        $payment->setPaidAmount($amountPaid);
        $payment->setDiscount($discount);

        //BLAME ME
        $this->get("services")->blameOnMe($payment, "create");
        $this->get("services")->blameOnMe($payment, "update");

        $this->em->persist($payment);

        $this->em->flush();


        $this->get('services')->flashSuccess($request);
        return $this->redirect($this->generateUrl('backend_admin_common_area_reservation_index'));


    }




    public function listLogAction(Request $request, $reservation)
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

        }
        else // If the request is not a POST one, die hard
            die;

        $filterComplex = $this->get("services")->getSessionComplex();

        // Process Parameters

        $results = $this->em->getRepository('BackendAdminBundle:BookingLog')->getLogDTData($start, $length, $orders, $search, $columns, $this->translator, $reservation);

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
                    case 'date':
                        {
                            $responseTemp = $entity->getCreatedAt()->format("m/d/y H:i:s");

                            break;
                        }
                    case 'description':
                        {
                            $responseTemp = $this->translator->trans("label_booking")." #". $entity->getCommonAreaReservation()->getId();
                            break;
                        }
                    case 'user':
                        {
                            $responseTemp = $entity->getCreatedBy()->getName();
                            break;
                        }

                    case 'status':
                        {
                            $responseTemp = $this->translator->trans($entity->getStatus());
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



    public function commentAction(Request $request, $id)
    {

        $this->get("services")->setVars('commonAreaReservation');
        $this->initialise();

        $entity = $this->em->getRepository('BackendAdminBundle:CommonAreaReservation')->find($id);

        $comment = new BookingComment();
        $comment->setCommonAreaReservation($entity);
        $comment->setCommentDescription(trim($_REQUEST["comment"]));
        $comment->setEnabled(1);

        $this->get("services")->blameOnMe($comment, "create");
        $this->get("services")->blameOnMe($comment, "update");

        $this->em->persist($comment);
        $this->em->flush();

        $this->get('services')->flashSuccess($request);

        return $this->redirect($this->generateUrl('backend_admin_common_area_reservation_index'));
    }




}

