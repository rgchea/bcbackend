<?php

namespace Backend\AdminBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Translation\TranslatorInterface;
//use Symfony\Component\Form\Extension\Core\Type\SubmitType;


//auto login
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;


use Backend\AdminBundle\Entity\Business;
use Backend\AdminBundle\Form\BusinessType;

use Backend\AdminBundle\Lib\CloudOnex;

/**
 * Business controller.
 *
 */
class BusinessController extends Controller
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
        $this->repository = $this->em->getRepository('BackendAdminBundle:Business');
        $this->translator = $this->get('translator');
        $this->renderer = $this->get('templating');
        $this->userLogged = $this->session->get('userLogged');
        $this->role = $this->session->get('userLogged')->getRole()->getName();

    }


    public function indexAction(Request $request)
    {
        $this->get("services")->setVars('business');
        $this->initialise();



        if($this->role == "SUPER ADMIN"){
            //listar todos los business
            return $this->render('BackendAdminBundle:Business:index.html.twig');
        }
        elseif($this->role == "ADMIN"){
            //backend_admin_business_edit
            $businessID = $this->userLogged->getBusiness()->getId();
            return $this->redirectToRoute('backend_admin_business_edit', array('id' => $businessID));

        }

    }



    public function listDatatablesAction(Request $request)
    {

        $this->get("services")->setVars('business');

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


        // Process Parameters
        //$businesLocale = $this->userLogged->getBusiness()->getGeoState()->getGeoCountry()->getLocale();

        $results = $this->repository->getRequiredDTData($start, $length, $orders, $search, $columns);
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
                    case 'taxName':
                        {
                            $responseTemp = $entity->getTaxName();
                            break;
                        }
                    case 'taxIdentifier':
                        {
                            $responseTemp = $entity->getTaxIdentifier();
                            break;
                        }
                    case 'city':
                        {
                            $responseTemp = $entity->getGeoState()->getName();
                            break;
                        }

                    case 'name':
                        {
                            $responseTemp = $entity->getName();
                            break;
                        }
                    case 'zipCode':
                        {
                            $responseTemp = $entity->getZipCode();
                            break;
                        }

                    case 'actions':
                        {
                            $urlEdit = $this->generateUrl('backend_admin_business_edit', array('id' => $entity->getId()));
                            $edit = "<a href='".$urlEdit."'><i class='fa fa-pencil-square-o'></i><span class='item-label'></span></a>&nbsp;&nbsp;";

                            $urlDelete = $this->generateUrl('backend_admin_business_delete', array('id' => $entity->getId()));
                            $delete = "<a class='btn-delete' href='".$urlDelete."'><i class='fa fa-trash-o'></i><span class='item-label'></span></a>";

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





    /**
     * Creates a new Business entity.
     *
     */
    public function newAction(Request $request)
    {

        /*print "<pre>";
        var_dump($_REQUEST);DIE;*/
        $session = new Session();
        $em = $this->getDoctrine()->getManager();

        if(isset($_REQUEST["regtoken"])){

            $regToken = $_REQUEST["regtoken"];

            $objUser = $em->getRepository('BackendAdminBundle:User')->findOneByRegisterToken($regToken);

            if($objUser){


                //if user is already registered and created a business then redirect to home
                if($objUser->getBusiness() != null){
                    return $this->redirectToRoute('backend_admin_business_edit', array('id' => $objUser->getBusiness()->getId()));
                }


                $objUser->setEnabled(1);
                $em->persist($objUser);
                $em->flush();

                //AUTO LOGIN BEGINS
                //Handle getting or creating the user entity likely with a posted form
                // The third parameter "main" can change according to the name of your firewall in security.yml
                $token = new UsernamePasswordToken($objUser, null, 'main', $objUser->getRoles());
                $this->get('security.token_storage')->setToken($token);

                // If the firewall name is not main, then the set value would be instead:
                // $this->get('session')->set('_security_XXXFIREWALLNAMEXXX', serialize($token));
                $this->get('session')->set('_security_main', serialize($token));

                // Fire the login event manually
                $event = new InteractiveLoginEvent($request, $token);
                $this->get("event_dispatcher")->dispatch("security.interactive_login", $event);
                //AUTO LOGIN ENDS


                $entity = new Business();
                $form   = $this->createCreateForm($entity);
                $countries = $em->getRepository('BackendAdminBundle:GeoCountry')->findBy(array("enabled" => 1));

                $this->get("services")->setVars('business');
                $this->initialise();


                return $this->render('BackendAdminBundle:Business:new.html.twig', array(
                    'entity' => $entity,
                    'form' => $form->createView(),
                    'userID' => $objUser->getId(),
                    'countries' => $countries,
                    'new' => 1

                ));


            }
            else{
                throw new AccessDeniedException();
            }

        }
        else{
            throw new AccessDeniedException();
        }




    }


    /**
     * Displays a form to edit an existing Business entity.
     *
     */
    public function editAction(Request $request, $id)
    {
        $this->get("services")->setVars('business');
        $this->initialise();

        $entity = $this->repository->find($id);

        $deleteForm = $this->createDeleteForm($entity);
        $editForm = $this->createEditForm($entity);

        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {

            $this->em->persist($entity);
            $this->em->flush();

            return $this->redirectToRoute('backend_admin_business_edit', array('id' => $id));
        }

        $countries = $this->em->getRepository('BackendAdminBundle:GeoCountry')->findBy(array("enabled" => 1));
        return $this->render('BackendAdminBundle:Business:edit.html.twig', array(
            'entity' => $entity,
            'form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
            'countries' => $countries,
            'edit' => $entity->getId()
        ));
    }

    /**
     * Deletes a Business entity.
     *
     */
    public function deleteAction(Request $request, $id)
    {

        $this->get("services")->setVars('business');
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('BackendAdminBundle:Business')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Business entity.');
        }
        else{

            //SOFT DELETE
            $entity->setEnabled(0);
            $this->get("services")->blameOnMe($entity);
            $em->persist($entity);
            $em->flush();

        }



        $this->get('services')->flashSuccess($request);
        return $this->redirectToRoute('backend_admin_business_index');
    }

    /**
     * Creates a form to delete a Business entity.
     *
     * @param Business $ticketType The Business entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($entity)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('backend_admin_business_delete', array('id' => $entity->getId())))
            ->setMethod('DELETE')
            ->getForm()
            ;
    }




    /**
     * Creates a new Business entity.
     *
     */
    public function createAction(Request $request)
    {

        /*print "<pre>";
        var_dump($_REQUEST);DIE;*/

        $this->get("services")->setVars('business');
        $this->initialise();

        /*
        $api = new CloudOnex();
        $response = $api->get("customer/1")->response();
        $customer = json_decode($response);
        print "<pre>";
        var_dump($customer);die;
        */



        $entity = new Business();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);
        /*print "<pre>";
        var_dump($form->getErrorsAsString());die;
         * */

        $token = $this->get('services')->getBCToken();

        if($token){
            if ($form->isValid()) {
                ///code + phone
                $objCountry = $this->em->getRepository('BackendAdminBundle:GeoCountry')->findOneByShortName($_REQUEST["phone_code"]);
                $entity->setPhoneCountry($objCountry);


                $this->get("services")->blameOnMe($entity, "create");

                $this->em->persist($entity);
                $this->em->flush();


                //CREATE BUSINESS TEAM ON GAMIFICATION
                $body = array();
                $body['name'] = $entity->getName();
                $body['description'] = $entity->getPhoneCountry()->getCode()." ".$entity->getPhoneNumber()." ".$entity->getAddress();
                $body['teamType'] = 2;//business
                $body["parent"] = 4;//General

                $createTeam = $this->get('services')->callBCSpace("POST", "teams", $body);
                if($createTeam){
                    $teamID = $createTeam["id"];
                    $entity->setTeamCorrelative($teamID);
                    $this->em->persist($entity);
                    $this->em->flush();

                }


                //SET BUSINESS TO THE USER
                $userID = intval($_REQUEST["userID"]);

                $userObj= $this->em->getRepository('BackendAdminBundle:User')->find($userID);
                $userObj->setBusiness($entity);
                $this->em->persist($userObj);
                $this->em->flush();

                $countryCode = "+".$entity->getGeoState()->getGeoCountry()->getCode();

                $phone = $countryCode.$entity->getPhoneNumber();

                ////CREATE BUSINESS AND USER ON .INFO

                $billingPassword = uniqid();
                $body = array(
                    array('name' => 'account', 'contents' => $entity->getName()),
                    array('name' => 'phone', 'contents' => $phone),
                    array('name' => 'email', 'contents' => $entity->getEmail()),
                    array('name' => 'password', 'contents' => $billingPassword),
                    array('name' => 'address', 'contents' => $entity->getAddress()),
                    array('name' => 'state', 'contents' => $entity->getGeoState()->getName()),
                    array('name' => 'zip', 'contents' => $entity->getZipCode()),
                    array('name' => 'country', 'contents' => $entity->getGeoState()->getGeoCountry()->getName()),
                    array('name' => 'company', 'contents' => ""),
                    array('name' => 'owner_id', 'contents' => 0)
                )
                ;

                //$ibillingToken = $entity->getGeoState()->getGeoCountry()->getIbillingToken();
                $objCountry = $entity->getGeoState()->getGeoCountry();
                $createCustomer = $this->get('services')->callBCInfo($objCountry, "POST", "customer", $body);

                //var_dump($createCustomer);die;
                ///VALIDA DEL LADO DEL CLIENTE QUE EL NOMBRE DEL NEGOCIO NO EXISTA EL EMAIL Y PHONE NUMBER
                //on response

                $entity->setCustomerID($createCustomer["contact_id"]);
                $this->em->persist($entity);
                $this->em->flush();


                /////SEND REGISTRATION SUCCESS MAIL
                /// Usuario de acceso a Bettercondos.space
                //Usuario de acceso a bettercondos.info
                //Links
                //Sitio web bettercondos.tech
                //Sitio de soporte y documentación.
                //Datos de contacto
                ///
                //generalTemplateMail($subject, $to, $bodyHtml, $bodyText = null,  $from = null){



                //iBilling
                /*
                $bodyHtml = $this->translator->trans('label_register_bc_info')."&nbsp;<a href='www.bettercondos.info/?ng=client'>Better Condos iBilling</a>" ."<br/>";
                $bodyHtml .= "<b>Email:&nbsp;</b>".$entity->getEmail()."<br/>";
                $bodyHtml .= "<b>Password:&nbsp;</b>".$billingPassword."<br/><br/>";

                //contact
                $bodyHtml .= $this->translator->trans('label_register_contact');

                //var_dump($bodyHtml);die;

                $to = $entity->getEmail();
                //($subject, $to, $bodyHtml, $from = null){
                //$message = $this->get('services')->generalTemplateMail("BetterCondos iBilling", $to, $bodyHtml);
                */

                //new message from sendgrid

                $to = $entity->getEmail();
                if($this->translator->getLocale() == "es"){
                    $templateID = "d-1605156206fc49038df495802135b426";
                }
                else{
                    $templateID = "d-1ff29228de0441c195dab8549491a948";
                }

                //client_login_url = https://www.bettercondos.info/?ng=client”
                //client_email
                //password
                //business_name

                $domain = $entity->getBillingGeoState()->getGeoCountry()->getIbillingDomain();


                $myJson = '"business_name": "'.$entity->getName().'",';
                $myJson .= '"client_login_url": '.$domain.'"/?ng=client”",';
                $myJson .= '"client_email": "'.$to.'",';
                $myJson .= '"password": "'.$billingPassword.'"';

                $sendgridResponse = $this->get('services')->callSendgrid($myJson, $templateID, $to);


                $this->get('services')->flashSuccess($request);
                return $this->redirect($this->generateUrl('backend_admin_complex_new', array("register" => 1)));

            }

            else{
                //print "FORMULARIO NO VALIDO";DIE;
                //SYSTEM LOG
            }


        }
        else{
            ///SYSTEM LOG GAMIFICATION
            ///
        }

        $this->get('services')->flashWarning($request);

        $countries = $this->em->getRepository('BackendAdminBundle:GeoCountry')->findBy(array("enabled" => 1));

        return $this->render('BackendAdminBundle:Business:new.html.twig', array(
            'entity' => $entity,
            'form' => $form->createView(),
            'userID' => $this->userLogged->getID(),
            'countries' => $countries,
            'new' => 1

        ));
    }

    /**
     * Creates a form to create a Business entity.
     *
     * @param Business $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm($entity)
    {
        //$this->setVars();
        $form = $this->createForm(BusinessType::class, $entity, array(
            'action' => $this->generateUrl('backend_admin_business_create'),
            'method' => 'POST'
        ));


        return $form;
    }




    /**
     * Creates a form to edit a Business entity.
     *
     * @param Business $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm($entity)
    {
        //$this->setVars();
        $form = $this->createForm(BusinessType::class, $entity, array(
            'action' => $this->generateUrl('backend_admin_business_update', array('id' => $entity->getId())),
            //'method' => 'PUT',
            //'client' => $this->userLogged,
        ));


        return $form;
    }


    /**
     * Edits an existing Business entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $this->get("services")->setVars('business');
        $this->initialise();

        $entity = $this->em->getRepository('BackendAdminBundle:Business')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Business entity.');
        }

        $deleteForm = $this->createDeleteForm($entity);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $myRequest = $request->request->get('business');

            ///code + phone
            $objCountry = $this->em->getRepository('BackendAdminBundle:GeoCountry')->findOneByShortName($_REQUEST["phone_code"]);
            $entity->setPhoneCountry($objCountry);


            $this->get("services")->blameOnMe($entity);
            $this->em->flush();

            ///UPDATE BUSINESS TEAM ON GAMIFICATION
            ///
            $body = array();
            $body['name'] = $entity->getName();
            $body['description'] = $entity->getName();
            $body['teamType'] = 2;//business
            $body["parent"] = 4;//General

            $updateTeamBusiness = $this->get('services')->callBCSpace("PUT", "teams/{$entity->getTeamCorrelative()}", $body);

            $this->get('services')->flashSuccess($request);
            return $this->redirect($this->generateUrl('backend_admin_business_index', array('id' => $id)));

        }

        $countries = $this->em->getRepository('BackendAdminBundle:GeoCountry')->findBy(array("enabled" => 1));
        return $this->render('BackendAdminBundle:Business:edit.html.twig', array(
            'entity' => $entity,
            'form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
            'countries' => $countries,
            'edit' => 1
        ));
    }


    public function updateStateAction(Request $request){

        $this->get("services")->setVars('business');
        $this->initialise();

        $countryShort = $_REQUEST["countryShort"];
        $billing = intval($_REQUEST["billing"]);

        //countryShort
        $country = $this->em->getRepository('BackendAdminBundle:GeoCountry')->findByShortName($countryShort);
        $objCountry = $country[0];
        $countryID = $objCountry->getId();

        $states = $this->em->getRepository('BackendAdminBundle:GeoState')->findBy(array("geoCountry" => $countryID, "enabled" => 1), array("name" => "ASC"));
        //$states = $states[0];


        if($billing){
            $strReturn = '<select id="business_billingGeoState" name="business[billingGeoState]" class="form-control classic">';
        }
        else{
            $strReturn = '<select id="business_geoState" name="business[geoState]" class="form-control classic">';
        }



        foreach ($states as $s){

            $selected = "";
            if(isset($_REQUEST["selectedState"])){
                if($s->getId() == $_REQUEST["selectedState"]){
                    $selected = ' selected="selected" ';
                }
            }


            //var_dump($s);die
            $strReturn .= '<option '.$selected.'value="'.$s->getId().'">'.$s->getName().'</option>';
        }

        $strReturn .= '</select>';

        //return $strReturn;

        print $strReturn;
        die;

    }


    public function checkEmailAction(Request $request){

        $this->get("services")->setVars('business');
        $this->initialise();

        $email = trim($_REQUEST["email"]);
        $myEntity  = intval($_REQUEST["myEntity"]);

        $check = $this->em->getRepository('BackendAdminBundle:Business')->findOneByEmail($email);

        $arrReturn = array();
        if($myEntity == 0){
            if($check){
                $arrReturn["result"] = 0;
            }
            else{
                $arrReturn["result"] = 1;
            }

        }
        else{//EDIT
            $businessID = intval($check->getId());

            if($check){

                if($businessID == $myEntity){
                    $arrReturn["result"] = 1;
                }
                else{
                    $arrReturn["result"] = 0;
                }

            }
            else{
                $arrReturn["result"] = 1;
            }

        }

        return new JsonResponse($arrReturn) ;

    }


    public function checkPhoneAction(){

        $this->get("services")->setVars('business');
        $this->initialise();

        $phone = trim($_REQUEST["phone"]);
        $myEntity  = intval($_REQUEST["myEntity"]);

        $check = $this->em->getRepository('BackendAdminBundle:Business')->findOneByPhoneNumber($phone);


        $arrReturn = array();
        if($myEntity == 0){
            if($check){
                $arrReturn["result"] = 0;
            }
            else{
                $arrReturn["result"] = 1;
            }

        }
        else{//EDIT
            $businessID = intval($check->getId());

            if($check){

                if($businessID == $myEntity){
                    $arrReturn["result"] = 1;
                }
                else{
                    $arrReturn["result"] = 0;
                }

            }
            else{
                $arrReturn["result"] = 1;
            }

        }


        return new JsonResponse($arrReturn) ;

    }


}

