<?php

namespace Backend\AdminBundle\Controller;

use Backend\AdminBundle\Entity\TicketFilePhoto;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\Exception\ProviderNotFoundException;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Translation\TranslatorInterface;


use Backend\AdminBundle\Entity\Ticket;
use Backend\AdminBundle\Entity\TicketStatusLog;
use Backend\AdminBundle\Entity\TicketComment;
use Backend\AdminBundle\Form\TicketType;
use Backend\AdminBundle\Entity\UserNotification;


class TicketController extends Controller
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


        //var_dump($this->translator->trans('label_welcome'));
        $this->get("services")->setVars('ticket');
        $this->initialise();

        //print $this->translator->getLocale();die;

        //print "<pre>";
        //var_dump($this->session->get("myComplexes"));die;

        return $this->render('BackendAdminBundle:Ticket:index.html.twig',
            array('myPath' => 'backend_admin_ticket_index')
        );


    }


    public function listDatatablesAction(Request $request)
    {

        $this->get("services")->setVars('ticket');

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

        $results = $this->repository->getRequiredDTData($start, $length, $orders, $search, $columns, $filterComplex);
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




    /**
     * Creates a new Ticket entity.
     *
     */
    public function newAction(Request $request)
    {

        $this->get("services")->setVars('ticket');
        $this->initialise();

        //$entity = new Ticket();
        //$form   = $this->createCreateForm($entity);

        $myComplexID = $this->get("services")->getSessionComplex();

        $ticketCategory = $this->em->getRepository("BackendAdminBundle:TicketCategory")->findBy(array('complex'=> $myComplexID, 'enabled' => 1));
        $complexSector = $this->em->getRepository("BackendAdminBundle:ComplexSector")->findBy(array('complex'=> $myComplexID, 'enabled' => 1));

        return $this->render('BackendAdminBundle:Ticket:new.html.twig', array(
            //'entity' => $entity,
            //'form' => $form->createView(),
            'myPath' => 'backend_admin_ticket_new',
            'new' => true,
            'ticketCategory' => $ticketCategory,
            'complexSector' => $complexSector,
            'token' => md5(uniqid())

        ));
    }



    /**
     * Finds and displays a Ticket entity.
     *
     */
    public function showAction($entity)
    {
        $deleteForm = $this->createDeleteForm($entity);

        return $this->render('backend_admin_property/show.html.twig', array(
            'entity' => $entity,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing Ticket entity.
     *
     */
    public function editAction(Request $request, $id)
    {
        $this->get("services")->setVars('ticket');
        $this->initialise();

        $entity = $this->em->getRepository('BackendAdminBundle:Ticket')->find($id);

        $ticketComments = $this->em->getRepository('BackendAdminBundle:TicketComment')->findBy(array('ticket' => $id, 'enabled' => 1), array('id' => 'DESC'));

        return $this->render('BackendAdminBundle:Ticket:edit.html.twig', array(
            'entity' => $entity,
            'ticketComments' => $ticketComments
        ));
    }

    /**
     * Deletes a Ticket entity.
     *
     */
    public function deleteAction(Request $request, $id)
    {

        $this->get("services")->setVars('ticket');
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('BackendAdminBundle:Ticket')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Ticket entity.');
        }
        else{

            //SOFT DELETE
            $entity->setEnabled(0);
            $this->get("services")->blameOnMe($entity);
            $em->persist($entity);
            $em->flush();

        }



        $this->get('services')->flashSuccess($request);
        return $this->redirectToRoute('backend_admin_ticket_index');
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
            ->setAction($this->generateUrl('backend_admin_ticket_delete', array('id' => $entity->getId())))
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

        $this->get("services")->setVars('ticket');
        $this->initialise();

        $sectorID = intval($_REQUEST["sector_select"]);
        $complexID = intval($this->get("services")->getSessionComplex());
        $propertyID = intval($_REQUEST["property_select"]);
        $categoryID = intval($_REQUEST["category_select"]);
        $title = trim($_REQUEST["title"]);
        $description = trim($_REQUEST["description"]);
        $solution = isset($_REQUEST["solution"]) ? trim($_REQUEST["solution"]) : "";
        $isPublic = isset($_REQUEST["is_public"]) ? 1 : 0;

        $objComplex = $this->em->getRepository('BackendAdminBundle:Complex')->find($complexID);
        $objComplexSector = $this->em->getRepository('BackendAdminBundle:ComplexSector')->find($sectorID);
        $objProperty = $this->em->getRepository('BackendAdminBundle:Property')->find($propertyID);
        $objCategory = $this->em->getRepository('BackendAdminBundle:TicketCategory')->find($categoryID);

        if(isset($_REQUEST["notification"])){
            $objTicketType = $this->em->getRepository('BackendAdminBundle:TicketType')->find(4);//notification
        }
        else{//standard ticket
            $objTicketType = $this->em->getRepository('BackendAdminBundle:TicketType')->find(1);
        }

        $status = $this->em->getRepository('BackendAdminBundle:TicketStatus')->findOneById(1);//OPEN

        $ticket = new Ticket();
        $ticket->setTicketType($objTicketType);
        $ticket->setTitle($title);
        $ticket->setDescription($description);
        $ticket->setPossibleSolution($solution);
        $ticket->setIsPublic($isPublic);
        $ticket->setTicketCategory($objCategory);
        $ticket->setComplexSector($objComplexSector);
        $ticket->setComplex($objComplex);
        $ticket->setProperty($objProperty);
        //$ticket->setCommonAreaReservation($commonAreaReservation);


        if(!isset($_REQUEST["notification"])){//if it is not a notification

            $tenantContract =  null;
            $propertyContract = $this->em->getRepository('BackendAdminBundle:PropertyContract')->findOneBy(array("property" => $propertyID, 'propertyTransactionType' => 3, "enabled" => 1, 'isActive' => 1), array("id"=> "DESC"));
            if($propertyContract) {
                $tenantContract = $propertyContract->getMainTenantContract();
            }

            //var_dump($tenantContract->getId());die;

            $ticket->setTenantContract($tenantContract);
            $ticket->setTicketStatus($status);
            $ticket->setEnabled(true);

            //setAssignedTo
            //$timezone  = -5; //(GMT -5:00) EST (U.S. & Canada)
            $timezone = str_replace("GMT", '', $objComplex->getBusiness()->getGeoState()->getTimezoneOffset());
            $userToAssign = $this->em->getRepository('BackendAdminBundle:Shift')->getUsertoAssignTicket($timezone, $complexID);

            $ticket->setAssignedTo($userToAssign);

            $token = trim($_REQUEST["ticket"]["token"]);
            $ticket->setToken($token);

        }
        else{
            //save date
            //save time
            $myDateTime = $_REQUEST["myDate"]." ".$_REQUEST["time"].":00";
            $ticket->setNotificationDateTime(new \DateTime($myDateTime));
            $ticket->setIsPublic(true);

        }


        $this->get("services")->blameOnMe($ticket, "create");
        $this->get("services")->blameOnMe($ticket, "update");
        $this->em->persist($ticket);

        $this->em->flush();

        if(!isset($_REQUEST["notification"])) {//if it is not a notification


            ///create push notification
            $pushDescription = $this->translator->trans("push.ticket_create"). " ".$this->userLogged->getName().". " . $this->translator->trans("label_ticket")." #".$ticket->getId().", ". $ticket->getTicketCategory()->getName();
            $this->get("services")->sendPushNotification($tenantContract->getUser(), $title, $pushDescription);

            $statusLog = new TicketStatusLog();
            $statusLog->setTicketStatus($status);
            $statusLog->setTicket($ticket);
            $this->get("services")->blameOnMe($statusLog, "create");
            $this->get("services")->blameOnMe($statusLog, "update");

            $ticket->setCreatedBy($tenantContract->getUser());
            $this->em->persist($ticket);
            $this->em->persist($statusLog);

            $this->em->flush();

            //ADD POINTS
            $message = $this->translator->trans("label_new"). " ". $this->translator->trans("label_ticket"). " ". $ticket->getId();
            $playKey = "BC-A-00005";//Register ticket
            $this->get("services")->addPointsAdmin($objComplex, $message, $playKey);

            ///get all photos by token and update the commonArea
            $token = trim($_REQUEST["ticket"]["token"]);
            $photos = $this->em->getRepository('BackendAdminBundle:TicketFilePhoto')->findByToken($token);
            foreach ($photos as $photo){
                $photo->setTicket($ticket);
                $this->em->persist($photo);
            }
            $this->em->flush();

        }

        //$this->em->flush();

        $this->get('services')->flashSuccess($request);

        if(!isset($_REQUEST["notification"])) {//if it is not a notification
            return $this->redirect($this->generateUrl('backend_admin_ticket_index'));
        }
        else{
            return $this->redirect($this->generateUrl('backend_admin_usernotification_index'));
        }


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
        $this->get("services")->setVars('ticket');
        $this->initialise();
        $form = $this->createForm(TicketType::class, $entity, array(
            'action' => $this->generateUrl('backend_admin_ticket_create'),
            'method' => 'POST',
            'role' => $this->role,
            'userID' => $this->userLogged->getId(),
            //'userID' => $entity->getCreatedBy()->getId(),
            'repository' => $this->em->getRepository('BackendAdminBundle:Complex'),

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
        $this->get("services")->setVars('ticket');
        $this->initialise();

        //var_dump($this->em->getRepository('BackendAdminBundle:Complex'));die;
        /*
        $form = $this->createForm(TicketType::class, $entity, array(
            'action' => $this->generateUrl('backend_admin_ticket_update',
                array('id' => $entity->getId(),
                    'role' => $this->role,
                    'userID' => $this->userLogged->getId(),
                    'repository' => $this->em->getRepository('BackendAdminBundle:Complex')
                )),
        ));
        */

        $form = $this->createForm(TicketType::class, $entity, array(
            'action' => $this->generateUrl('backend_admin_ticket_update', array('id' => $entity->getId())),
            'role' => $this->role,
            //'userID' => $this->userLogged->getId(),
            'userID' => $entity->getCreatedBy()->getId(),
            'repository' => $this->em->getRepository('BackendAdminBundle:Complex'),
        ));



        return $form;
    }


    /**
     * Edits an existing Ticket entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $this->get("services")->setVars('ticket');
        $this->initialise();


        $entity = $this->em->getRepository('BackendAdminBundle:Ticket')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Ticket entity.');
        }

            //BLAME ME
            $this->get("services")->blameOnMe($entity, "update");
            $this->em->persist($entity);
            $this->em->flush();

            ///get all photos by token and update the commonArea
            $photos = $this->em->getRepository('BackendAdminBundle:TicketFilePhoto')->findByToken($entity->getToken());
            foreach ($photos as $photo){
                $photo->setTicket($entity);
                $this->em->persist($photo);
            }
            $this->em->flush();

            $this->get('services')->flashSuccess($request);
            return $this->redirect($this->generateUrl('backend_admin_ticket_index', array('id' => $id)));

        //$countries = $this->em->getRepository('BackendAdminBundle:GeoCountry')->findBy(array("enabled" => 1));
        /*
        return $this->render('BackendAdminBundle:Ticket:edit.html.twig', array(
            'entity'      => $entity,
            'form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
            //'countries' => $countries
        ));
        */
    }


    public function infoAction(Request $request){

        $this->get("services")->setVars('ticket');
        $this->initialise();

        $propertyID  = $_REQUEST["id"];
        $entity = $this->repository->findOneById($propertyID);

        $arrReturn = array();
        $arrReturn["sms_code"] = $entity->getSmsCode();
        $arrReturn["property_type_id"] = $entity->getTicketType()->getName();
        $arrReturn["complex_sector_id"] = $entity->getComplexSector()->getName();
        $arrReturn["team_correlative"] = $entity->getTeamCorrelative();
        $arrReturn["name"] = $entity->getName();
        $arrReturn["address"] = $entity->getAddress();
        $arrReturn["code"] = $entity->getCode();
        $arrReturn["is_available"] = $entity->getIsAvailable();


        //return new JsonResponse($arrReturn);
        $returnResponse = new JsonResponse();
        $returnResponse->setJson(json_encode($arrReturn));

        return $returnResponse;


    }




    public function changeSectionAction(Request $request){

        $this->get("services")->setVars('ticket');
        $this->initialise();

        $complex = intval($_REQUEST["complexID"]);

        //countryShort
        $sections = $this->em->getRepository('BackendAdminBundle:ComplexSector')->findByComplex($complex);
        $strReturn = "";

        foreach ($sections as $s){

            $selected = "";

            if($s->getId() == intval($_REQUEST["selectedSection"])){
                $selected = ' selected="selected" ';
            }


            //var_dump($s);die
            $strReturn .= '<option '.$selected.'value="'.$s->getId().'">'.$s->getName().'</option>';
        }


        //return $strReturn;

        print $strReturn;
        die;

    }


    public function shareAdAction(Request $request)
    {

        //print "<pre>";
        //var_dump($_REQUEST);DIE;

        $this->get("services")->setVars('ticket');
        $this->initialise();

        $propertyID = $_REQUEST["propertyID"];
        $objTicket = $this->em->getRepository('BackendAdminBundle:Ticket')->find($propertyID);

        $objTicketType = $this->em->getRepository('BackendAdminBundle:TicketType')->find(2);

        $entity = new Ticket();
        $entity->setTicket($objTicket);
        $entity->setComplex($objTicket->getComplex());
        $entity->setTicketType($objTicketType);
        $entity->setEnabled(1);
        $entity->setTitle("Share property ".$propertyID);
        $entity->setDescription("Share property ".$propertyID);
        $entity->setIsPublic(1);


        $this->get("services")->blameOnMe($entity, "create");
        $this->get("services")->blameOnMe($entity, "update");
        $this->em->persist($entity);
        $this->em->flush();

        return $this->redirectToRoute('backend_admin_ticket_edit', array('id' => $propertyID));
    }


    public function deleteAdAction(Request $request)
    {

        //print "<pre>";
        //var_dump($_REQUEST);DIE;

        $this->get("services")->setVars('ticket');
        $this->initialise();

        $propertyID = $_REQUEST["propertyID"];
        $ticketID = $_REQUEST["ticketID"];
        $objTicket = $this->em->getRepository('BackendAdminBundle:Ticket')->find($ticketID);

        $this->em->remove($objTicket);
        $this->em->flush();


        return $this->redirectToRoute('backend_admin_ticket_edit', array('id' => $propertyID));
    }




    /**
     * Creates a new TicketContract entity.
     *
     */
    public function newAgreementAction(Request $request)
    {
        $this->get("services")->setVars('ticket');
        $this->initialise();


        if(isset($_REQUEST["property_id"])){
            $propertyID = intval($_REQUEST["property_id"]);
            $entity = $this->em->getRepository('BackendAdminBundle:Ticket')->find($propertyID);
        }
        else{

            throw $this->createNotFoundException('The Ticket does not exist');
        }

        //$form   = $this->createCreateForm($entity);

        return $this->render('BackendAdminBundle:TicketContract:newAgreement.html.twig', array(
            'entity' => $entity,
            //'form' => $form->createView(),


        ));
    }


    public function closeAction(Request $request, $id)//when a manager closes a ticket
    {

        $this->get("services")->setVars('ticket');
        $this->initialise();

        $entity = $this->em->getRepository('BackendAdminBundle:Ticket')->find($id);

        $status = $this->em->getRepository('BackendAdminBundle:TicketStatus')->findOneById(3);//SOLVED
        $entity->setTicketStatus($status);


        $statusLog = new TicketStatusLog();
        $statusLog->setTicketStatus($status);
        $statusLog->setTicket($entity);


        ////CREATE USER NOTIFICATION
        $objUserNotification = New UserNotification();
        $objUserNotification->setTicket($entity);
        $type = $this->em->getRepository('BackendAdminBundle:NotificationType')->findOneById(2);//TYPE=TICKET
        $objUserNotification->setNotificationType($type);
        $objUserNotification->setIsRead(0);
        $objUserNotification->setEnabled(1);
        $title = $this->userLogged->getName();
        $objUserNotification->setTitle($title);
        $description = "Ticket #".$entity->getId(). " ". $this->translator->trans('label_ticket_solved');
        $objUserNotification->setDescription($description);
        $objUserNotification->setNotice($this->translator->trans('label_ticket_close_72'));
        $objUserNotification->setSentTo($entity->getCreatedBy());

        $this->get("services")->blameOnMe($statusLog, "create");
        $this->get("services")->blameOnMe($statusLog, "update");
        $this->get("services")->blameOnMe($entity, "update");
        $this->get("services")->blameOnMe($objUserNotification, "create");
        $this->get("services")->blameOnMe($objUserNotification, "update");

        $this->em->persist($entity);
        $this->em->persist($statusLog);
        $this->em->persist($objUserNotification);

        $this->em->flush();

        $this->get("services")->sendPushNotification($entity->getCreatedBy(), $title, $description);


        $this->get('services')->flashSuccess($request);
        return $this->redirectToRoute('backend_admin_ticket_index');


    }



    public function commentAction(Request $request, $id)
    {

        $this->get("services")->setVars('ticket');
        $this->initialise();

        $entity = $this->em->getRepository('BackendAdminBundle:Ticket')->find($id);

        $comment = new TicketComment();
        $comment->setTicket($entity);
        $comment->setCommentDescription(trim($_REQUEST["comment"]));
        $comment->setEnabled(1);

        $this->get("services")->blameOnMe($comment, "create");
        $this->get("services")->blameOnMe($comment, "update");

        $this->em->persist($comment);
        $this->em->flush();

        $title = $this->translator->trans("label_new")." ".$this->translator->trans("label_comment");
        $description = $this->translator->trans("label_ticket")." #". $entity->getId(). substr(trim($_REQUEST["comment"]),0,50);
        $this->get("services")->sendPushNotification($entity->getCreatedBy(), $title, $description);


        $this->get('services')->flashSuccess($request);
        return $this->redirectToRoute('backend_admin_ticket_index');
    }


    public function ratingTenantAction(Request $request, $id)
    {

        $this->get("services")->setVars('ticket');
        $this->initialise();

        $rating = intval($_REQUEST["rating"]);

        $entity = $this->em->getRepository('BackendAdminBundle:Ticket')->find($id);
        $entity->setRatingToTenant($rating);

        $this->get("services")->blameOnMe($entity, "update");

        $this->em->persist($entity);
        $this->em->flush();

        $title = $this->userLogged->getName(). " ". $this->translator->trans("push.ticket_rating").$entity->getId().", ".$this->translator->trans("push.ticket_rating3"). ":".$rating ;
        $description = $this->translator->trans("push.ticket_rating2");

        //$this->get("services")->sendPushNotification($entity->getCreatedBy(), $title, $description);

        //$this->get('services')->flashSuccess($request);

        return new JsonResponse(array('success' => true));
    }



    public function imageSendAction(Request $request){

        $this->get("services")->setVars('ticket');
        $this->initialise();
        //var_dump($_REQUEST);die;

        //AVATAR UPLOAD
        /*
        if($myFile != NULL){

            $file = $entity->getAvatarPath();
            $fileName = md5(uniqid()).'.'.$file->guessExtension();
            $file->move($this->getParameter('avatars_directory'), $fileName);
            $entity->setAvatarPath($entity->getAvatarUploadDir().$fileName);

        }
        */


        $ticketID = trim($_REQUEST["ticket"]);//TOKEN
        //$objCommonArea = $this->em->getRepository('BackendAdminBundle:CommonArea')->find($commonAreaID);

        $document = new TicketFilePhoto();
        $media = $request->files->get('file');

        $fileName = md5(uniqid()).'.'.$media->guessExtension();

        $document->setFile($media);
        $document->setPhotoPath($fileName);
        //$document->setName($media->getClientOriginalName());
        //$document->setCommonArea($objCommonArea);
        $document->setToken($ticketID);
        $document->upload($fileName);

        $this->get("services")->blameOnMe($document, "create");
        $this->get("services")->blameOnMe($document, "update");

        $this->em->persist($document);
        $this->em->flush();

        //infos sur le document envoyé
        //var_dump($request->files->get('file'));die;
        return new JsonResponse(array('success' => $document->getId()));

    }



    public function imageGetAction(Request $request){



        $this->get("services")->setVars('ticket');
        $this->initialise();



        $ticketID = intval($_REQUEST["ticket"]);
        $images = $this->em->getRepository('BackendAdminBundle:TicketFilePhoto')->findByTicket($ticketID);

        $result  = array();
        $storeFolder = __DIR__.'/../../../../web/uploads/images/tickets/';

        $files = scandir($storeFolder);                 //1

        //var_dump($files);die;

        if ( false!==$files ) {
            foreach ( $images as $file ) {

                $obj['id'] = $file->getId();
                $obj['name'] = $file->getPhotoPath();
                $obj['size'] = 0;
                $result[] = $obj;
            }
        }

        header('Content-type: text/json');              //3
        header('Content-type: application/json');
        echo json_encode($result);die;

    }




    public function imageRemoveAction(Request $request){


        $this->get("services")->setVars('ticket');
        $this->initialise();
        if(isset($_REQUEST["id"])){

            $img = $this->em->getRepository('BackendAdminBundle:TicketFilePhoto')->find(intval($_REQUEST["id"]));
            if($img){
                $imgName =  $img->getPhotoPath();
                $this->em->remove($img);
                $this->em->flush();

                $storeFolder = __DIR__.'/../../../../web/uploads/images/tickets/';

                unlink($storeFolder.$imgName);

            }
            else{

            }

        }

        return new JsonResponse(array('success' => true));

    }


}

