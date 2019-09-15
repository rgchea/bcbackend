<?php

namespace Backend\AdminBundle\Controller;


use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Translation\TranslatorInterface;


use Backend\AdminBundle\Entity\Ticket;
use Backend\AdminBundle\Entity\Property;
use Backend\AdminBundle\Entity\PropertyPhoto;
use Backend\AdminBundle\Form\PropertyType;
use Backend\AdminBundle\Entity\PropertyContract;
use Backend\AdminBundle\Entity\TenantContract;
use Backend\AdminBundle\Entity\PropertyContractTransaction;

/**
 * Property controller.
 *
 */
class PropertyController extends Controller
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
        $this->repository = $this->em->getRepository('BackendAdminBundle:Property');
        $this->translator = $this->get('translator');
        $this->renderer = $this->get('templating');
        $this->userLogged = $this->session->get('userLogged');
        $this->role = $this->session->get('userLogged')->getRole()->getName();
        $this->nowtime = strtotime(date("Y-m-d"));

    }


    public function indexAction(Request $request)
    {


        //var_dump($this->translator->trans('label_welcome'));
        $this->get("services")->setVars('property');
        $this->initialise();

        //print $this->translator->getLocale();die;

        return $this->render('BackendAdminBundle:Property:index.html.twig', array('myPath' => 'backend_admin_property_index'));


    }


    public function listDatatablesAction(Request $request)
    {

        $this->get("services")->setVars('property');

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

            $complexID = $request->request->has("complexID") ? $request->request->get('complexID') : 0;


        }
        else // If the request is not a POST one, die hard
            die;


        ///FILTER BY ROLE
        $filters = null;
        if($this->role != "SUPER ADMIN"){

            if($complexID != 0){
                $filters[$complexID] = $complexID;
            }
            else{
                $arrComplex = $this->em->getRepository('BackendAdminBundle:Complex')->getComplexByUser($this->userLogged->getId());
                foreach ($arrComplex as $k =>$v) {
                    $filters[$v] = $v;//the complex id
                }

            }

        }

        // Process Parameters

        $results = $this->repository->getRequiredDTData($start, $length, $orders, $search, $columns, $filters);
        $objects = $results["results"];
        $selected_objects_count = count($objects);

        $i = 0;
        $response = "";

        foreach ($objects as $key => $entity)
        {



            /*
             *
             *
             * $owner = "-";
            $tenant = "-";
            $propertyContract = $this->em->getRepository('BackendAdminBundle:PropertyContract')->findOneBy(array("property" => $entity->getId(), "enabled" => 1));
            if($propertyContract){
                $tenantContract = $this->em->getRepository('BackendAdminBundle:TenantContract')->findOneBy(array("propertyContract" => $propertyContract->getId(), "mainTenant" => 1, "enabled" => 1));

                if($tenantContract){
                    $tenant = $tenantContract->getUser()->getName() ." - ".$tenantContract->getUser()->getEmail() ;
                    $owner = $tenantContract->getOwnerEmail();
                }

            }
            */


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
                    case 'complex':
                        {
                            $responseTemp = $entity->getComplexSector()->getComplex()->getName();
                            break;
                        }

                    case 'sector':
                        {
                            $responseTemp = $entity->getComplexSector()->getName();
                            break;
                        }
                    case 'number':
                        {

                            $urlDetail = $this->generateUrl('backend_admin_property_detail', array('id' => $entity->getId()));
                            $property = "<a href='".$urlDetail."'>".$entity->getPropertyNumber()."</a>";

                            $responseTemp = $property;
                            break;
                        }
                        /*
                    case 'code':
                        {
                            $responseTemp = $entity->getCode();
                            break;
                        }
                        */
                    case 'owner':
                        {

                            $responseTemp = $entity->getOwnerEmail();
                            break;
                        }
                    case 'tenant':
                        {
                            $tenant = $entity->getMainTenant() ? $entity->getMainTenant()->getEmail() : "--";
                            $responseTemp = $tenant;
                            break;
                        }

                    case 'actions':
                        {

                            $urlAgreement = $this->generateUrl('backend_admin_property_new_agreement', array('property_id' => $entity->getId()));
                            $agreement = "<a href='".$urlAgreement."'><i class='fa fa-file'></i></i><span class='item-label'></span></a>&nbsp;&nbsp;";


                            $urlEdit = $this->generateUrl('backend_admin_property_edit', array('id' => $entity->getId()));
                            $edit = "<a href='".$urlEdit."'><i class='fa fa-pencil-square-o'></i><span class='item-label'></span></a>&nbsp;&nbsp;";

                            $urlDelete = $this->generateUrl('backend_admin_property_delete', array('id' => $entity->getId()));
                            $delete = "<a class='btn-delete' href='".$urlDelete."'><i class='fa fa-trash-o'></i><span class='item-label'></span></a>";

                            $responseTemp = $agreement.$edit.$delete;
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
     * Creates a new Property entity.
     *
     */
    public function newAction(Request $request)
    {

        $this->get("services")->setVars('property');
        $this->initialise();

        $entity = new Property();
        $form   = $this->createCreateForm($entity);

        return $this->render('BackendAdminBundle:Property:new.html.twig', array(
            'entity' => $entity,
            'form' => $form->createView(),
            'myPath' => 'backend_admin_property_new',
            'new' => true


        ));
    }



    /**
     * Finds and displays a Property entity.
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
     * Displays a form to edit an existing Property entity.
     *
     */
    public function editAction(Request $request, $id)
    {
        $this->get("services")->setVars('property');
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('BackendAdminBundle:Property')->find($id);

        $deleteForm = $this->createDeleteForm($entity);
        $editForm = $this->createEditForm($entity);

        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();
            //array('myPath' => 'backend_admin_property_index')
            return $this->redirectToRoute('backend_admin_property_edit', array('id' => $id));
        }


        //get shared ad ticket
        $isAdShared = $em->getRepository('BackendAdminBundle:Ticket')->findOneBy(array("ticketType" => 2, "property" => $id));
        if($isAdShared){

            $createdAt = $isAdShared->getCreatedAt()->format('Y-m-d');

            $now = time(); // or your date as well
            $your_date = strtotime($createdAt);
            $datediff = $now - $your_date;
            $days =  round($datediff / (60 * 60 * 24));
            $isAdSharedDays = 30-$days;

            $isAdShared = $isAdShared->getId();
        }
        else{
            $isAdShared = 0;
            $isAdSharedDays = 0;
        }

        $tenantContract =  null;
        $propertyContract = $this->em->getRepository('BackendAdminBundle:PropertyContract')->findOneBy(array("property" => $entity->getId(), 'propertyTransactionType' => 3, "enabled" => 1,  'isActive' => 1), array("id"=> "DESC"));
        if($propertyContract) {
            $tenantContract = $propertyContract->getMainTenantContract();
        }



        return $this->render('BackendAdminBundle:Property:edit.html.twig', array(
            'entity' => $entity,
            'form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
            'edit' => $entity->getId(),
            'isAdShared' => $isAdShared,
            'isAdSharedDays' => $isAdSharedDays,
            'propertyContract' => $propertyContract,
            'tenantContract' => $tenantContract
        ));
    }

    /**
     * Deletes a Property entity.
     *
     */
    public function deleteAction(Request $request, $id)
    {

        $this->get("services")->setVars('property');
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('BackendAdminBundle:Property')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Property entity.');
        }
        else{

            //SOFT DELETE
            $entity->setEnabled(0);
            $this->get("services")->blameOnMe($entity);
            $em->persist($entity);
            $em->flush();

        }



        $this->get('services')->flashSuccess($request);
        return $this->redirectToRoute('backend_admin_property_index');
    }

    /**
     * Creates a form to delete a Property entity.
     *
     * @param Property
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($entity)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('backend_admin_property_delete', array('id' => $entity->getId())))
            ->setMethod('DELETE')
            ->getForm()
            ;
    }




    /**
     * Creates a new Property entity.
     *
     */
    public function createAction(Request $request)
    {

        //print "<pre>";
        //var_dump($_REQUEST);DIE;


        $this->get("services")->setVars('property');
        $this->initialise();


        $entity = new Property();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);
        /*print "<pre>";
        var_dump($form->getErrorsAsString());die;
         * */

        if ($form->isValid()) {

            $myComplex = $this->get("services")->getSessionComplex();
            $objComplex = $this->em->getRepository('BackendAdminBundle:Complex')->find($myComplex);
            $entity->setComplex($objComplex);

            //$businessLocale = $business->getGeoState()->getGeoCountry()->getLocale();
            $sectorID = $_REQUEST["property"]["complexSector"];
            $objComplexSector = $this->em->getRepository('BackendAdminBundle:ComplexSector')->find($sectorID);
            $business = $objComplexSector->getComplex()->getBusiness();
            $businessLocale = $business->getGeoState()->getGeoCountry()->getLocale();

            $myPropertyType = intval($_REQUEST["property"]["propertyType"]);
            $propertyType = $this->em->getRepository('BackendAdminBundle:PropertyType')->find($myPropertyType);


            //chequear si el número de propiedad por complejo y sector ya existe
            if(isset($_REQUEST["property"]["propertyNumber"])){

                $propertyNumber = trim($_REQUEST["property"]["propertyNumber"]);
                $propertyExists  = $this->repository->findOneBy(array("complex"=>$myComplex, "complexSector"=> $sectorID, "propertyNumber"=>$propertyNumber));
                if($propertyExists){
                    $myMessage = $this->translator->trans('label_property_number') ." {$propertyNumber} ".$this->translator->trans('label_already_exists');
                    $this->get('services')->flashCustom($request, $myMessage);
                    //return $this->redirect($this->generateUrl('backend_admin_property_index'));

                    return $this->render('BackendAdminBundle:Property:new.html.twig', array(
                        'entity' => $entity,
                        'form' => $form->createView(),
                        'myPath' => 'backend_admin_property_new'

                    ));
                }

            }


            /*
            if($myPropertyType == 0){ //OTHER
                $propertyTypeName = trim($_REQUEST["extra"]["propertyTypeName"]);
            }
            else{
                $propertyTypeName = $businessLocale == "en" ? $propertyType->getNameEN() : $propertyType->getNameES();
            }
            */

            //$code = $this->get("services")->getToken(6);
            //$entity->setCode($code);
            $entity->setIsAvailable(0);

            //
            //BLAME ME
            $this->get("services")->blameOnMe($entity, "create");

            $this->em->persist($entity);
            $this->em->flush();


            //si viene el tenant email
            //se guardan datos de la propiedad
            //  *se agrega un property_contract / property_id / transaction_type = 3 / start_date / end_date / is_active = 1 / rental_price = 0 / maintenance_price / total_visible_amount = 1
            // * se agrega un tenant_contract / property_contract_id / user_id = search by email / player_id / role_id = inquilino /
            // is available 0

            //para el search by email del user, si se encuentra el user se vincula directamente, si no se encuentra se envía un correo para invitación invitation_user_email
            //se crea property contract y tenant_contract
            //cuando el usuario se registre, se debe de actualizar en tenant_contract el user_id

            $tenantEmail = trim($_REQUEST["agreement"]["tenant_email"]);
            $ownerEmail = trim($_REQUEST["agreement"]["owner_email"]);
            $maintenancePrice = $_REQUEST["agreement"]["maintenance"];
            $whopaysmaintenance = $_REQUEST["agreement"]["whopaysmaintenance"];
            $startDate =  $_REQUEST["agreement"]["start"];
            $endDate = $_REQUEST["agreement"]["end"];

            ////CREATE PROPERTY CONTRACT
            ///
            $propertyContract = $this->createPropertyContract($entity->getId(), $startDate, $endDate, $maintenancePrice, $whopaysmaintenance);

            //DISABLE OLD CONTRACTS
            $this->em->getRepository('BackendAdminBundle:PropertyContract')->disableOldContracts($propertyContract->getId(), $entity->getId());

            ///CREATE PROPERTY TEAM
            $teamIDProperty = $this->createPropertyTeam($entity->getId());

            ///CREATE TENANT CONTRACT
            $tenantContract = $this->createTenantContract($tenantEmail, $ownerEmail, $propertyContract->getId(), $teamIDProperty);

            ///CREATE CONTRACT PAYMENTS
            $this->createPayments($propertyContract->getId(), $maintenancePrice, $startDate, $endDate);


            //si no viene el tenant email
            // se guardan datos de la propiedad
            // is available 1


            $this->get('services')->flashSuccess($request);
            return $this->redirect($this->generateUrl('backend_admin_property_index'));

        }
        /*
        else{
            print "FORMULARIO NO VALIDO";DIE;
        }
         * */

        return $this->render('BackendAdminBundle:Property:new.html.twig', array(
            'entity' => $entity,
            'form' => $form->createView(),
            'myPath' => 'backend_admin_property_new'


        ));

    }

    /**
     * Creates a form to create a Property entity.
     *
     * @param Property $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm($entity)
    {
        $this->get("services")->setVars('property');
        $this->initialise();
        $form = $this->createForm(PropertyType::class, $entity, array(
            'action' => $this->generateUrl('backend_admin_property_create'),
            'method' => 'POST',
            'role' => $this->role,
            'userID' => $this->userLogged->getId(),
            //'userID' => $entity->getCreatedBy()->getId(),
            'repository' => $this->em->getRepository('BackendAdminBundle:Complex'),
            'complex' => $this->get("services")->getSessionComplex(),

        ));


        return $form;
    }




    /**
     * Creates a form to edit a Property entity.
     *
     * @param Property $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm($entity)
    {
        //print "entra";die;
        $this->get("services")->setVars('property');
        $this->initialise();

        //var_dump($this->em->getRepository('BackendAdminBundle:Complex'));die;
        /*
        $form = $this->createForm(PropertyType::class, $entity, array(
            'action' => $this->generateUrl('backend_admin_property_update',
                array('id' => $entity->getId(),
                    'role' => $this->role,
                    'userID' => $this->userLogged->getId(),
                    'repository' => $this->em->getRepository('BackendAdminBundle:Complex')
                )),
        ));
        */

        $form = $this->createForm(PropertyType::class, $entity, array(
            'action' => $this->generateUrl('backend_admin_property_update', array('id' => $entity->getId())),
            'role' => $this->role,
            //'userID' => $this->userLogged->getId(),
            'userID' => $entity->getCreatedBy()->getId(),
            'repository' => $this->em->getRepository('BackendAdminBundle:Complex'),
            //'complex' => $this->get("services")->getSessionComplex()
            'complex' => $entity->getComplex()->getId()
        ));



        return $form;
    }


    /**
     * Edits an existing Property entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $this->get("services")->setVars('property');
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('BackendAdminBundle:Property')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Property entity.');
        }

        $deleteForm = $this->createDeleteForm($entity);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $myRequest = $request->request->get('property');

            //$entity->setComplex($this->em->getRepository('BackendAdminBundle:Complex')->find($_REQUEST["property"]["complex"]));

            //$businessLocale = $business->getGeoState()->getGeoCountry()->getLocale();
            $sectorID = $_REQUEST["property"]["complexSector"];
            $objComplexSector = $this->em->getRepository('BackendAdminBundle:ComplexSector')->find($sectorID);
            $business = $objComplexSector->getComplex()->getBusiness();
            $businessLocale = $business->getGeoState()->getGeoCountry()->getLocale();

            $myPropertyType = intval($_REQUEST["property"]["propertyType"]);
            $propertyType = $this->em->getRepository('BackendAdminBundle:PropertyType')->find($myPropertyType);

            /*
            if($myPropertyType == 0){ //OTHER
                $propertyTypeName = trim($_REQUEST["extra"]["propertyTypeName"]);
            }
            else{
                $propertyTypeName = $businessLocale == "en" ? $propertyType->getNameEN() : $propertyType->getNameES();
            }
            */


            //chequear si el número de propiedad por complejo y sector ya existe
            if(isset($_REQUEST["property"]["propertyNumber"])){

                $propertyNumber = trim($_REQUEST["property"]["propertyNumber"]);
                $propertyExists  = $this->repository->findOneBy(array("complex"=>$entity->getComplex()->getId(), "complexSector"=> $sectorID, "propertyNumber"=>$propertyNumber));
                if($propertyExists){

                    if($propertyExists->getId() != $id){
                        $myMessage = $this->translator->trans('label_property_number') ." {$propertyNumber} ".$this->translator->trans('label_already_exists');
                        $this->get('services')->flashCustom($request, $myMessage);
                        //return $this->redirect($this->generateUrl('backend_admin_property_index'));

                        return $this->redirectToRoute('backend_admin_property_edit', array('id' => $id));

                    }

                }

            }


            /*
            if($myPropertyType == 0){ //OTHER
                $propertyTypeName = trim($_REQUEST["extra"]["propertyTypeName"]);
            }
            else{
                $propertyTypeName = $businessLocale == "en" ? $propertyType->getNameEN() : $propertyType->getNameES();
            }
            */

            //$code = $this->get("services")->getToken(6);
            //$entity->setCode($code);


            //BLAME ME
            $this->get("services")->blameOnMe($entity, "create");

            $this->em->persist($entity);
            $this->em->flush();

            //si viene el tenant email
            //se guardan datos de la propiedad
            //  *se agrega un property_contract / property_id / transaction_type = 3 / start_date / end_date / is_active = 1 / rental_price = 0 / maintenance_price / total_visible_amount = 1
            // * se agrega un tenant_contract / property_contract_id / user_id = search by email / player_id / role_id = inquilino /
            // is available 0

            //para el search by email del user, si se encuentra el user se vincula directamente, si no se encuentra se envía un correo para invitación invitation_user_email
            //se crea property contract y tenant_contract
            //cuando el usuario se registre, se debe de actualizar en tenant_contract el user_id

            /*
            $tenantEmail = trim($_REQUEST["agreement"]["tenant_email"]);
            $ownerEmail = trim($_REQUEST["agreement"]["owner_email"]);
            $maintenancePrice = $_REQUEST["agreement"]["maintenance"];
            $whopaysmaintenance = $_REQUEST["agreement"]["whopaysmaintenance"];
            $startDate =  $_REQUEST["agreement"]["start"];
            $endDate = $_REQUEST["agreement"]["end"];
            */

            ////CREATE PROPERTY CONTRACT
            //DISABLE OLD CONTRACTS
            ///CREATE PROPERTY TEAM
            ///CREATE TENANT CONTRACT
            ///CREATE CONTRACT PAYMENTS





            $this->get('services')->flashSuccess($request);
            return $this->redirect($this->generateUrl('backend_admin_property_index'));

        }

        //$countries = $this->em->getRepository('BackendAdminBundle:GeoCountry')->findBy(array("enabled" => 1));

        return $this->render('BackendAdminBundle:Property:edit.html.twig', array(
            'entity'      => $entity,
            'form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
            //'countries' => $countries
        ));
    }


    public function infoAction(Request $request){

        $this->get("services")->setVars('property');
        $this->initialise();

        $propertyID  = $_REQUEST["id"];
        $entity = $this->repository->findOneById($propertyID);

        $arrReturn = array();
        $arrReturn["sms_code"] = $entity->getSmsCode();
        $arrReturn["property_type_id"] = $entity->getPropertyType()->getName();
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




    public function imageSendAction(Request $request){

        $this->get("services")->setVars('property');
        $this->initialise();


        $entityID = intval($_REQUEST["property"]);
        $obj = $this->em->getRepository('BackendAdminBundle:Property')->find($entityID);

        $document = new PropertyPhoto();
        $media = $request->files->get('file');

        $fileName = md5(uniqid()).'.'.$media->guessExtension();

        $document->setFile($media);
        $document->setPhotoPath($fileName);
        //$document->setName($media->getClientOriginalName());
        $document->setProperty($obj);
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

        $this->get("services")->setVars('property');
        $this->initialise();



        $entityID = intval($_REQUEST["property"]);
        $images = $this->em->getRepository('BackendAdminBundle:PropertyPhoto')->findByProperty($entityID);

        $result  = array();
        $storeFolder = __DIR__.'/../../../../web/uploads/images/property/';

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


        $this->get("services")->setVars('property');
        $this->initialise();


        $img = $this->em->getRepository('BackendAdminBundle:PropertyPhoto')->find(intval($_REQUEST["id"]));
        $imgName =  $img->getPhotoPath();
        $this->em->remove($img);
        $this->em->flush();

        $storeFolder = __DIR__.'/../../../../web/uploads/images/property/';

        unlink($storeFolder.$imgName);

        return new JsonResponse(array('success' => true));

    }

    public function changeSectionAction(Request $request){

        $this->get("services")->setVars('property');
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

        $this->get("services")->setVars('property');
        $this->initialise();

        $propertyID = $_REQUEST["propertyID"];
        $objProperty = $this->em->getRepository('BackendAdminBundle:Property')->find($propertyID);

        $objTicketType = $this->em->getRepository('BackendAdminBundle:TicketType')->find(2);

        $entity = new Ticket();
        $entity->setProperty($objProperty);
        $entity->setComplex($objProperty->getComplex());
        $entity->setTicketType($objTicketType);
        $entity->setEnabled(1);
        $entity->setTitle("Share property ".$propertyID);
        $entity->setDescription("Share property ".$propertyID);
        $entity->setIsPublic(1);


        $this->get("services")->blameOnMe($entity, "create");
        $this->get("services")->blameOnMe($entity, "update");
        $this->em->persist($entity);
        $this->em->flush();

        return $this->redirectToRoute('backend_admin_property_edit', array('id' => $propertyID));
    }


    public function deleteAdAction(Request $request)
    {

        //print "<pre>";
        //var_dump($_REQUEST);DIE;

        $this->get("services")->setVars('property');
        $this->initialise();

        $propertyID = $_REQUEST["propertyID"];
        $ticketID = $_REQUEST["ticketID"];
        $objTicket = $this->em->getRepository('BackendAdminBundle:Ticket')->find($ticketID);

        $this->em->remove($objTicket);
        $this->em->flush();


        return $this->redirectToRoute('backend_admin_property_edit', array('id' => $propertyID));
    }




    /**
     * Creates a new PropertyContract entity.
     *
     */
    public function newAgreementAction(Request $request)
    {
        $this->get("services")->setVars('property');
        $this->initialise();


        if(isset($_REQUEST["property_id"])){
            $propertyID = intval($_REQUEST["property_id"]);
            $entity = $this->em->getRepository('BackendAdminBundle:Property')->find($propertyID);
        }
        else{

            throw $this->createNotFoundException('The Property does not exist');
        }

        if(isset($_REQUEST["submit"])){

            //print "<pre>";
            //var_dump($_REQUEST);DIE;

            //si viene el tenant email
            //se guardan datos de la propiedad
            //  *se agrega un property_contract / property_id / transaction_type = 3 / start_date / end_date / is_active = 1 / rental_price = 0 / maintenance_price / total_visible_amount = 1
            // * se agrega un tenant_contract / property_contract_id / user_id = search by email / player_id / role_id = inquilino /
            // is available 0

            //para el search by email del user, si se encuentra el user se vincula directamente, si no se encuentra se envía un correo para invitación invitation_user_email
            //se crea property contract y tenant_contract
            //cuando el usuario se registre, se debe de actualizar en tenant_contract el user_id

            $tenantEmail = trim($_REQUEST["agreement"]["tenant_email"]);
            $ownerEmail = trim($_REQUEST["agreement"]["owner_email"]);
            $maintenancePrice = $_REQUEST["agreement"]["maintenance"];
            $whopaysmaintenance = $_REQUEST["agreement"]["whopaysmaintenance"];
            $startDate =  $_REQUEST["agreement"]["start"];
            $endDate = $_REQUEST["agreement"]["end"];

            ////CREATE PROPERTY CONTRACT
            ///
            $propertyContract = $this->createPropertyContract($entity->getId(), $startDate, $endDate, $maintenancePrice, $whopaysmaintenance);

            //DISABLE OLD CONTRACTS
            $this->em->getRepository('BackendAdminBundle:PropertyContract')->disableOldContracts($propertyContract->getId(), $entity->getId());

            ///CREATE PROPERTY TEAM
            $teamIDProperty = $this->createPropertyTeam($entity->getId());


            ///CREATE TENANT CONTRACT
            $tenantContract = $this->createTenantContract($tenantEmail, $ownerEmail, $propertyContract->getId(), $teamIDProperty);

            ///CREATE CONTRACT PAYMENTS
            $this->createPayments($propertyContract->getId(), $maintenancePrice, $startDate, $endDate);


            //si no viene el tenant email
            // se guardan datos de la propiedad
            // is available 1


            $this->get('services')->flashSuccess($request);
            return $this->redirect($this->generateUrl('backend_admin_property_index'));

            //////////////


        }
        else{

            //$form   = $this->createCreateForm($entity);

            return $this->render('BackendAdminBundle:PropertyContract:newAgreement.html.twig', array(
                'entity' => $entity,
                'myPath' => 'backend_admin_property_new_agreement',
                'new' => true

                //'form' => $form->createView(),


            ));

        }


    }






    public function detailAction(Request $request, $id)
    {
        $this->get("services")->setVars('property');
        $this->initialise();
        $em = $this->getDoctrine()->getManager();

        $entity = $this->em->getRepository('BackendAdminBundle:Property')->find($id);
        $propertyContract = $this->em->getRepository('BackendAdminBundle:PropertyContract')->findOneBy(array("property" => $id, 'propertyTransactionType' => 3, "enabled" => 1,  'isActive' => 1), array("id"=> "DESC"));
        $mainContract = $propertyContract->getMainTenantContract();
        $tenantContracts = $this->em->getRepository('BackendAdminBundle:TenantContract')->findBy(array("propertyContract" => $propertyContract->getId(), "enabled" => 1), array("id" => "DESC"));


        $futuretime = strtotime($propertyContract->getEndDate()->format('Y-m-d'));
        $remainingTime = $this->get('services')->time_elapsed_A($futuretime-$this->nowtime);

        //print "<pre>";
        //var_dump($this->em->getRepository('BackendAdminBundle:Property')->getPropertyLog($id, $this->translator->getLocale()));die;
        $log = $this->em->getRepository('BackendAdminBundle:Property')->getPropertyLog($id, $this->translator->getLocale());


        return $this->render('BackendAdminBundle:Property:detail.html.twig', array(
            'entity' => $entity,
            'edit' => $entity->getId(),
            'propertyContract' => $propertyContract,
            'mainContract' => $mainContract,
            'tenantContracts' => $tenantContracts,
            'remainingTime' => $remainingTime,
            'log' => $log

        ));
    }


    //

    public function updateMaintenanceAction(Request $request, $id)
    {
        $this->get("services")->setVars('property');
        $this->initialise();

        $entity = $this->em->getRepository('BackendAdminBundle:Property')->find($id);
        $propertyContract = $this->em->getRepository('BackendAdminBundle:PropertyContract')->findOneBy(array("property" => $id, 'propertyTransactionType' => 3, "enabled" => 1,  'isActive' => 1), array("id"=> "DESC"));


        if(isset($_REQUEST["submit"])){

            //print "<pre>";
            //var_dump($_REQUEST);die;


            $propertyContract->setWhoPaysMaintenance($_REQUEST["whopaysmaintenance"]);
            $propertyContract->setMaintenancePrice(floatval($_REQUEST["newPrice"]));

            $entity->setMaintenancePrice(floatval($_REQUEST["newPrice"]));

            $this->em->persist($propertyContract);
            $this->em->persist($entity);

            $this->get("services")->blameOnMe($entity, 'update');
            $this->get("services")->blameOnMe($propertyContract, 'update');

            $this->em->flush();


            $this->get('services')->flashSuccess($request);
            return $this->redirectToRoute('backend_admin_property_detail', array('id' => $id));


        }


        $futuretime = strtotime($propertyContract->getEndDate()->format('Y-m-d'));
        $remainingTime = $this->get('services')->time_elapsed_A($futuretime-$this->nowtime);


        return $this->render('BackendAdminBundle:Property:updateMaintenance.html.twig', array(
            'entity' => $entity,
            'propertyContract' => $propertyContract,
            'remainingTime' => $remainingTime
        ));
    }

    public function contractCancelAction(Request $request, $id)
    {

        $this->get("services")->setVars('property');
        $this->initialise();


        $entity = $this->em->getRepository('BackendAdminBundle:Property')->find($id);
        $propertyContract = $this->em->getRepository('BackendAdminBundle:PropertyContract')->findOneBy(array("property" => $id, 'propertyTransactionType' => 3, "enabled" => 1,  'isActive' => 1), array("id"=> "DESC"));
        $tenantContract = $propertyContract->getMainTenantContract();

        if(isset($_REQUEST["submit"])){

            //print "<pre>";
            //var_dump($_REQUEST);die;

            $propertyContract->setIsActive(0);
            //$propertyContract->setEnabled(0);
            $this->em->persist($propertyContract);
            //$this->em->persist($entity);


            ///SEND MAIL NOTIFY THE OWNER
            if(isset($_REQUEST["notifyOwner"])){

                //new message from sendgrid
                if($this->translator->getLocale() == "en"){
                    $templateID = "d-6f2dbc6839e244758156ef7555ba8d8e";
                }
                else{
                    $templateID = "d-744e784eebb643ffa5c4a45c6143a6fc";
                }

                //tenant_name
                //property_address
                //complex_name
                $myJson = '"tenant_name": "'.$tenantContract->getUser()->getName().'",';
                $myJson .= '"property_address": "'.$entity->getPropertyNumber().' '.$entity->getAddress().'",';
                $myJson .= '"complex_name": "'.$entity->getComplex()->getName().'",';

                $sendgridResponse = $this->get('services')->callSendgrid($myJson, $templateID, $tenantContract->getUser()->getEmail());

            }

            $this->get("services")->blameOnMe($propertyContract, 'update');

            $this->em->flush();

            $this->get('services')->flashSuccess($request);
            return $this->redirectToRoute('backend_admin_property_detail', array('id' => $id));


        }


        $futuretime = strtotime($propertyContract->getEndDate()->format('Y-m-d'));
        $remainingTime = $this->get('services')->time_elapsed_A($futuretime-$this->nowtime);


        return $this->render('BackendAdminBundle:Property:contractCancel.html.twig', array(
            'entity' => $entity,
            'propertyContract' => $propertyContract,
            'tenantContract' => $tenantContract,
            'remainingTime' => $remainingTime
        ));

    }

    public function contractExtendAction(Request $request, $id)
    {
        $this->get("services")->setVars('property');
        $this->initialise();


        $entity = $this->em->getRepository('BackendAdminBundle:Property')->find($id);
        $propertyContract = $this->em->getRepository('BackendAdminBundle:PropertyContract')->findOneBy(array("property" => $id, 'propertyTransactionType' => 3, "enabled" => 1,  'isActive' => 1), array("id"=> "DESC"));
        $tenantContract = $propertyContract->getMainTenantContract();

        if(isset($_REQUEST["submit"])){

            //print "<pre>";
            //var_dump($_REQUEST);die;


            $propertyContract->setWhoPaysMaintenance($_REQUEST["whopaysmaintenance"]);

            $startDate = trim($_REQUEST["startDate"]);
            $startDate = new \DateTime($startDate);
            $propertyContract->setStartDate($startDate);

            $endDate = trim($_REQUEST["endDate"]);
            $endDate = new \DateTime($endDate);
            $propertyContract->setEndDate($endDate);


            $this->em->persist($propertyContract);
            //$this->em->persist($entity);


            ///SEND MAIL NOTIFY THE OWNER
            if(isset($_REQUEST["notifyOwner"])){

                //new message from sendgrid
                if($this->translator->getLocale() == "en"){
                    $templateID = "d-c3de70b4c3e546e1bbbdc4926ec58c87";
                }
                else{
                    $templateID = "d-888fa43845274964b59a6fdff7872c04";
                }

                //tenant_name
                //property_address
                //expiration_date
                //complex_name
                $myJson = '"tenant_name": "'.$tenantContract->getUser()->getName().'",';
                $myJson .= '"property_address": "'.$entity->getPropertyNumber().' '.$entity->getAddress().'",';
                $myJson = '"expiration_date": "'.$propertyContract->getEndDate()->format("m/d/Y") .'",';
                $myJson .= '"complex_name": "'.$entity->getComplex()->getName().'",';

                $sendgridResponse = $this->get('services')->callSendgrid($myJson, $templateID, $tenantContract->getUser()->getEmail());

            }


            $this->get("services")->blameOnMe($propertyContract, 'update');
            $this->em->flush();

            $this->get('services')->flashSuccess($request);
            return $this->redirectToRoute('backend_admin_property_detail', array('id' => $id));


        }


        $futuretime = strtotime($propertyContract->getEndDate()->format('Y-m-d'));
        $remainingTime = $this->get('services')->time_elapsed_A($futuretime-$this->nowtime);


        return $this->render('BackendAdminBundle:Property:contractExtend.html.twig', array(
            'entity' => $entity,
            'propertyContract' => $propertyContract,
            'tenantContract' => $tenantContract,
            'remainingTime' => $remainingTime
        ));
    }



    public function listTicketsAction(Request $request, $property)
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

        $results = $this->em->getRepository('BackendAdminBundle:Ticket')->getRequiredDTData($start, $length, $orders, $search, $columns, $filterComplex, $property);
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


                    case 'contract':
                        {
                            $contractID = $entity->getTenantContract()->getPropertyContract()->getId();
                            $responseTemp = "<a data-toggle='modal' id='orange-button' data-target='#orange-modal' data-color='orange' href='#' onclick='selectPropertyContract(".$contractID.")'>".$contractID."</a>";

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
                                $responseTemp = "<button type='button' class='btn btn-default btn-xs'>".$responseText."</button>";
                            }
                            else{
                                $responseText  = $this->translator->getLocale() == "en" ? "Private" : "Privado";
                                $responseTemp = "<button type='button' class='btn btn-info btn-xs'>".$responseText."</button>";
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
                                $responseTemp = "<button type='button' class='btn btn-danger btn-xs'>".$responseText."</button>";
                            }
                            elseif ($myStatus == "Closed"){
                                $responseTemp = "<button type='button' class='btn btn-success btn-xs'>".$responseText."</button>";
                            }
                            else{
                                $responseTemp = "<button type='button' class='btn btn-warning btn-xs'>".$responseText."</button>";
                            }

                            break;
                        }

                    case 'elapsed':
                        {
                            $responseTemp = "--";
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



    public function listBookingsAction(Request $request, $property)
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
        $results = $this->em->getRepository('BackendAdminBundle:CommonAreaReservation')->getRequiredDTData($start, $length, $orders, $search, $columns, $filterComplex, $dateConditions, $businessLocale, $property);
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



    public function listPaymentsAction(Request $request, $property)
    {

        $this->get("services")->setVars('propertyContractTransaction');

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

        $results = $this->em->getRepository('BackendAdminBundle:PropertyContractTransaction')->getRequiredDTData($start, $length, $orders, $search, $columns, $filterComplex, $this->translator->getLocale(), $property);

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
                    case 'description':
                        {
                            $responseTemp = $entity->getDescription();
                            break;
                        }
                    case 'property':
                        {
                            $responseTemp = $entity->getPropertyContract()->getProperty();
                            break;
                        }

                    case 'type':
                        {
                            $responseTemp = $entity->getPropertyTransactionType();
                            break;
                        }
                    case 'status':
                        {
                            $myStatus = $entity->getStatus();
                            if($myStatus == 0){
                                $status = $this->translator->getLocale() == 'en' ? "Pending" : "Pendiente";
                                $status = "<button type='button' class='btn btn-warning btn-xs'>".$status."</button>";
                            }
                            else{
                                $status= $this->translator->getLocale() == 'en' ? "Paid" : "Pagado";
                                $status = "<button type='button' class='btn btn-success btn-xs'>".$status."</button>";
                            }

                            $responseTemp = $status;
                            break;
                        }

                    case 'createdat':
                        {
                            $responseTemp = $entity->getCreatedAt()->format("m/d/y");
                            break;
                        }

                    case 'paid':
                        {
                            if($entity->getPaidDate() != NULL){
                                $responseTemp = $entity->getPaidDate()->format("m/d/y");
                            }
                            else{
                                $responseTemp = "--";
                            }

                            break;
                        }

                    case 'actions':
                        {
                            $urlEdit = $this->generateUrl('backend_admin_propertycontracttransaction_edit', array('id' => $entity->getId()));
                            $edit = "<a href='".$urlEdit."'><i class='fa fa-pencil-square-o'></i><span class='item-label'></span></a>&nbsp;&nbsp;";


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





    /**
     * Creates a new PropertyContract entity.
     *
     */
    public function invitationsAction(Request $request)
    {
        $this->get("services")->setVars('property');
        $this->initialise();

        $complex = $this->get("services")->getSessionComplex();



        return $this->render('BackendAdminBundle:Property:invitations.html.twig', array(

            'myPath' => 'backend_admin_property_invitations',
            'new' => true,

            //'form' => $form->createView(),


        ));
    }

    public function invitationsExportAction(Request $request){


        $this->get("services")->setVars('property');
        $this->initialise();

        $complex = $this->get("services")->getSessionComplex();
        $templateProperties = $this->em->getRepository('BackendAdminBundle:PropertyContract')->getPropertiesToInvite($complex);
        //print "<pre>";
        //var_dump($templateProperties);die;

        $arrTemplate = array();

        $myDateFormat = "Y-m-d";
        $myDate = gmdate($myDateFormat);

        if($this->translator->getLocale() == "en"){
            foreach ($templateProperties as $key => $val){

                $arrTemplate[] = array(
                    "id" => $val["id"],
                    "property_number" => $val["property_number"],
                    "maintenance_price" => $val["maintenance_price"] == NULL ? "0.00" : floatval($val["maintenance_price"]),
                    "who_pays_maintenance" => "tenant",
                    "start_date_{$myDateFormat}" => $myDate,
                    "end_date_{$myDateFormat}" => $myDate,
                    'tenant_email' => "",
                    'owner_email' => "",
                );
            }
        }
        else{
            foreach ($templateProperties as $key => $val){

                $arrTemplate[] = array(
                    "id" => $val["id"],
                    "numero_propiedad" => $val["property_number"],
                    "precio_mantenimiento" => $val["maintenance_price"] == NULL ? "0.00" : floatval($val["maintenance_price"]),
                    "quien_paga_mantenimiento" => "inquilino",
                    "fecha_inicio_{$myDateFormat}" => $myDate,
                    "fecha_fin_{$myDateFormat}" => $myDate,
                    'correo_inquilino' => "",
                    'correo_propietario' => "",

                );
            }
        }



        $this->array_csv_download($arrTemplate, $myDate."_export.csv");
        die;

    }

    public function array_csv_download( $array, $filename = "export.csv", $delimiter="," )
    {
        header( 'Content-Type: application/csv' );
        header( 'Content-Disposition: attachment; filename="' . $filename . '";' );

        // clean output buffer
        ob_start(); //start output buffering
        ob_end_clean();

        $handle = fopen( 'php://output', 'w' );

        // use keys as column titles
        fputcsv( $handle, array_keys( $array[0] ) );

        foreach ( $array as $value ) {
            fputcsv( $handle, $value , $delimiter );
        }

        fclose( $handle );

        // flush buffer
        if( ob_get_level() > 0 ) ob_flush();

        // use exit to get rid of unexpected output afterward
        exit();
    }



    //invitationsImport
    public function invitationsImportAction(Request $request)
    {
        $this->get("services")->setVars('property');
        $this->initialise();



        if(isset($_REQUEST["loadSubmit"])){

            $arrReturn = array();

            set_time_limit(1200);

            $errors = array();


            $csvData = array();
            $tmpName = $_FILES['file']['tmp_name'];
            //$csvAsArray = array_map('str_getcsv', file($tmpName));


            $file_handle = fopen($tmpName, 'r');
            while (!feof($file_handle) ) {
                $line_of_text[] = fgetcsv($file_handle, 1024);
            }
            fclose($file_handle);

            /*
            print "<pre>";
            var_dump($line_of_text) ;die;
             * */

            foreach ($line_of_text as $key => $value) {


                //print "<pre>";
                //var_dump($value);
                //die;


                if($key != 0){

                    if(!is_array($value)){
                        break;
                    }

                    if(count($value) != 8){
                        $errors[] = "Invalid file format";
                        break;
                    }
                    //**buscarlo en base de datos y sustituir con ID

                    //0 - property_id
                    //1 - property_number// **
                    //2 - maintenance_price// **
                    //3 - who_pays_maintenance// **
                    //4 - start_date// **
                    //5 - end_date
                    //6 - tenant_email
                    //7 - owner_email

                    $propertyID = intval($value[0]);
                    $propertyNumber = $this->get("services")->cleanString($value[1]);
                    $maintenancePrice = floatval($value[2]);
                    $whoPaysMaintenance =  $this->get("services")->cleanString($value[3]);
                    $tenantEmail = $this->get("services")->cleanString($value[6]);
                    $ownerEmail = $this->get("services")->cleanString($value[7]);

                    if($whoPaysMaintenance == "inquilino"){$whoPaysMaintenance = "tenant";}
                    if($whoPaysMaintenance == "propietario"){$whoPaysMaintenance = "owner";}


                    $startDate = new \Datetime(trim($value[4]));
                    $endDate = new \Datetime(trim($value[5]));

                    $objProperty =  $this->em->getRepository('BackendAdminBundle:Property')->find($propertyID);

                    if($objProperty == null){
                        $errors[] = "Invalid property_id {$propertyID}";
                        break;

                    }

                    ////CREATE PROPERTY CONTRACT
                    ///
                    $propertyContract = $this->createPropertyContract($objProperty->getId(), $startDate, $endDate, $maintenancePrice, $whoPaysMaintenance );

                    //DISABLE OLD CONTRACTS
                    $this->em->getRepository('BackendAdminBundle:PropertyContract')->disableOldContracts($propertyContract->getId(), $objProperty->getId());

                    ///CREATE PROPERTY TEAM
                    $teamIDProperty = $this->createPropertyTeam($objProperty->getId());


                    ///CREATE TENANT CONTRACT
                    $tenantContract = $this->createTenantContract($tenantEmail, $ownerEmail, $propertyContract->getId(), $teamIDProperty, true);

                    ///CREATE CONTRACT PAYMENTS
                    $this->createPayments($propertyContract->getId(), $maintenancePrice,  trim($value[4]), trim($value[5]));


                }
            }

            $countErrors = count($errors);
            if($countErrors > 0){
                $request->getSession()->getFlashBag()->add('warning',$countErrors . "ERROR, invalid format" );
                foreach ($errors as $error){
                    $request->getSession()->getFlashBag()->add('warning', $error );
                }

            }else{
                $this->em->flush();
                $this->get('services')->flashSuccess($request);
            }

            $newContracts = array();
            foreach ($arrReturn as $propertyContractID){
                $newContracts[] = $this->em->getRepository('BackendAdminBundle:PropertyContract')->find($propertyContractID);
            }

            return $this->render('BackendAdminBundle:Property:invitations.html.twig', array(

                'myPath' => 'backend_admin_property_invitations',
                'new' => true,
                'newContracts' => $newContracts

                //'form' => $form->createView(),


            ));

        }
        else{

            return $this->render('BackendAdminBundle:Property:invitationsImport.html.twig', array(

                'myPath' => 'backend_admin_property_invitations_import',
                'new' => true

                //'form' => $form->createView(),


            ));

        }


    }


    public function createPayments($propertyContractID, $amount, $startDate, $endDate){

        $this->get("services")->setVars('property');
        $this->initialise();

        $objPropertyContract = $this->em->getRepository('BackendAdminBundle:PropertyContract')->find($propertyContractID);
        $transactionType = $this->em->getRepository('BackendAdminBundle:PropertyTransactionType')->find(5);//maintenance

        //iterate through start and end dates

        $bdate = strtotime($startDate);
        $edate = strtotime($endDate);

        //$year1 = date('Y', $ts1);
        //$year2 = date('Y', $ts2);

        //$month1 = date('m', $ts1);
        //$month2 = date('m', $ts2);

        //$diff = (($year2 - $year1) * 12) + ($month2 - $month1);
        $months = 0;

        if($edate < $bdate) {
            //prenatal
            $months = -1;
            return false;

        } else {
            //count months.
            while($bdate < $edate) {

                $months++;
                $bdate = strtotime('+1 MONTH', $bdate);
                if ($bdate > $edate) {
                    $months--;
                    break;
                }


                $payment = new PropertyContractTransaction();
                $payment->setEnabled(1);
                $payment->setComplex($objPropertyContract->getProperty()->getComplex());
                $payment->setPropertyContract($objPropertyContract);
                $payment->setPropertyTransactionType($transactionType);
                //$payment->setCommonAreaReservation($entity);
                $description = $this->translator->trans('label_monthly_maintenance');
                $payment->setDescription($description);
                $payment->setPaymentAmount($amount);
                //$payment->setPaidAmount($amountPaid);
                $payment->setDiscount(floatval(0));
                $payment->setDueDate(new \DateTime(date("Y-m-15 h:i:s",$bdate)));

                /*
                ///paid & paid date
                $payment->setPaidBy($propertyContract->getProperty()->getMainTenant());
                $gtmNow = gmdate("Y-m-d H:i:s");
                $payment->setPaidDate(new \DateTime($gtmNow));
                */
                //status
                $payment->setStatus(0);//PENDING PAYMENT

                //BLAME ME
                $this->get("services")->blameOnMe($payment, "create");
                $this->get("services")->blameOnMe($payment, "update");


                $this->em->persist($payment);


            }


        }

        $this->em->flush();

        return true;

    }


    public function createPropertyContract($propertyID, $startDate, $endDate, $maintenancePrice, $whopaysmaintenance ){

        $this->get("services")->setVars('property');
        $this->initialise();

        $objProperty = $this->em->getRepository('BackendAdminBundle:Property')->find($propertyID);

        ////CREATE PROPERTY CONTRACT
        $propertyContract = new PropertyContract();

        $propertyContract->setProperty($objProperty);
        $propertyTransactionTye = $this->em->getRepository('BackendAdminBundle:PropertyTransactionType')->find(3);//renta
        $propertyContract->setPropertyTransactionType($propertyTransactionTye);

        $startDate = trim($startDate);
        $startDate = new \DateTime($startDate);
        $propertyContract->setStartDate($startDate);

        $endDate = trim($endDate);
        $endDate = new \DateTime($endDate);
        $propertyContract->setEndDate($endDate);

        $propertyContract->setIsActive(1);
        $propertyContract->setDuePaymentDay(15);
        $propertyContract->setRentalPrice(0.00);
        $maintenancePrice = floatval($maintenancePrice);
        $propertyContract->setMaintenancePrice($maintenancePrice);
        $objProperty->setMaintenancePrice($maintenancePrice);

        $propertyContract->setWhoPaysMaintenance($whopaysmaintenance);
        $propertyContract->setTotalVisibleAmount(1);
        $propertyContract->setEnabled(1);


        //BLAME ME propertyContract
        $this->get("services")->blameOnMe($propertyContract, "create");
        $this->get("services")->blameOnMe($propertyContract, "update");


        $this->em->persist($objProperty);
        $this->em->persist($propertyContract);
        $this->em->flush();

        return $propertyContract;

    }




    public function createTenantContract($tenantEmail, $ownerEmail, $propertyContractID, $teamIDProperty){

        $this->get("services")->setVars('property');
        $this->initialise();


        $propertyContract = $this->em->getRepository('BackendAdminBundle:PropertyContract')->find($propertyContractID);
        $property = $this->em->getRepository('BackendAdminBundle:Property')->find($propertyContract->getProperty()->getId());

        $user = $this->em->getRepository('BackendAdminBundle:User')->findOneByEmail($tenantEmail);

        $tenantContract = new TenantContract();

        //search user
        $user = $this->em->getRepository('BackendAdminBundle:User')->findOneByEmail($tenantEmail);
        if($user){
            //print "entra";die;
            $tenantContract->setUser($user);
            $property->setMainTenant($user);

        }
        else{
            //print "entra2";die;
            //enviar invitación al correo
            //invitation_user_email
            //revisar template sengrid

            //invitation_user_email
            $tenantContract->setInvitationUserEmail($tenantEmail);

        }

        //add player gamification
        $body = array();
        $userTeam = $this->get('services')->callBCSpace("POST", "users/{$tenantEmail}/teams/{$teamIDProperty}", $body);
        //print "<pre>";
        //var_dump($userTeam);die;
        if($userTeam){
            $tenantContract->setPlayerId($userTeam["id"]);
        }


        $owner = $this->em->getRepository('BackendAdminBundle:User')->findOneByEmail($ownerEmail);
        if($owner){
            $tenantContract->setOwner($owner);
        }

        $tenantContract->setOwnerEmail($ownerEmail);
        $property->setOwnerEmail($ownerEmail);
        $tenantContract->setPropertyContract($propertyContract);
        $tenantContract->setEnabled(1);
        $tenantContract->setMainTenant(1);

        $code = $this->get("services")->getToken(6);
        $tenantContract->setPropertyCode($code);


        //BLAME ME tenantContract
        $this->get("services")->blameOnMe($tenantContract, "create");
        $this->get("services")->blameOnMe($tenantContract, "update");


        $this->em->persist($property);
        $this->em->persist($tenantContract);


        $this->em->flush();

        //update propertyContract mainTenantContract
        $propertyContract->setMainTenantContract($tenantContract);

        $this->em->persist($propertyContract);

        $this->em->flush();


        //email invitation
        $myJson = '"complex_name": "'.$property->getComplex()->getName().'", "property_key": "'.$tenantContract->getPropertyCode().'"';
        $templateID = $this->translator->getLocale() == "en" ? "d-8c65067739ed4fd3bf79ab31650b47f8" : "d-2461cbbce3e64bb2a81c90d440809352";
        $sendgridResponse = $this->get('services')->callSendgrid($myJson, $templateID, $tenantEmail);


        return $tenantContract;



    }


    public function createPropertyTeam($propertyID){

        $this->get("services")->setVars('property');
        $this->initialise();


        $property = $this->em->getRepository('BackendAdminBundle:Property')->find($propertyID);


        $token = $this->get('services')->getBCToken();
        ///CREATE PROPERTY TEAM
        $body = array();
        $body['name'] = $property->getName();
        $body['description'] = $property->getName();
        $body['teamType'] = 5;//Property
        $body["parent"] = $property->getComplexSector()->getTeamCorrelative();//sector team correlative
        //


        $createTeamProperty = $this->get('services')->callBCSpace("POST", "teams", $body);
        $teamIDProperty = 0;
        if($createTeamProperty){
            $teamIDProperty = $createTeamProperty["id"];
            $property->setTeamCorrelative($teamIDProperty);
            $this->em->persist($property);
            $this->em->flush();

        }


        return $teamIDProperty;

    }


}

