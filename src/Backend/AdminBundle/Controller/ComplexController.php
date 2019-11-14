<?php

namespace Backend\AdminBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Translation\TranslatorInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\File\File;



use Backend\AdminBundle\Entity\Complex;
use Backend\AdminBundle\Entity\ComplexFaq;
use Backend\AdminBundle\Form\ComplexType;
use Backend\AdminBundle\Entity\UserComplex;
use Backend\AdminBundle\Entity\ComplexSector;
use Backend\AdminBundle\Entity\Property;
use Backend\AdminBundle\Entity\Shift;


/**
 * Complex controller.
 *
 */
class ComplexController extends Controller
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
        $this->repository = $this->em->getRepository('BackendAdminBundle:Complex');
        $this->translator = $this->get('translator');
        $this->renderer = $this->get('templating');
        $this->userLogged = $this->session->get('userLogged');
        $this->role = $this->session->get('userLogged')->getRole()->getName();


    }


    public function indexAction(Request $request)
    {


        //var_dump($this->translator->trans('label_welcome'));
        $this->get("services")->setVars('complex');
        $this->initialise();
        //var_dump();die;
        if($this->role != "SUPER ADMIN"){
            if(count($this->session->get("myComplexes")) == 0){
                return $this->redirectToRoute('backend_admin_complex_new');
                //throw $this->createAccessDeniedException($this->translator->trans('label_access_denied'));
            }

        }



        //print $this->translator->getLocale();die;

        return $this->render('BackendAdminBundle:Complex:index.html.twig');

    }


    public function listDatatablesAction(Request $request)
    {

        $this->get("services")->setVars('complex');

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


        ///FILTER BY ROLE
        $filters = null;
        if($this->role != "SUPER ADMIN"){
            $arrComplex = $this->repository->getComplexByUser($this->userLogged->getId());
            foreach ($arrComplex as $k =>$v) {
                $filters[$v] = $v;//the complex id
            }

        }

        // Process Parameters
        if($this->role != "SUPER ADMIN"){
            $businessLocale = $this->userLogged->getBusiness()->getGeoState()->getGeoCountry()->getLocale();
        }
        else{
            $businessLocale = $this->translator->getLocale();
        }



        $results = $this->repository->getRequiredDTData($start, $length, $orders, $search, $columns, $filters, $businessLocale, $this->role);
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
                    case 'complexType':
                        {
                            $responseTemp = $entity->getComplexType();
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

                    case 'sectors':
                        {

                            $count = $this->em->getRepository('BackendAdminBundle:ComplexSector')->getCountByComplex($entity->getId());
                            //$responseTemp = $entity->getZipCode();
                            $responseTemp = $count;
                            break;
                        }

                    case 'properties':
                        {

                            $count = $this->em->getRepository('BackendAdminBundle:Property')->getCountByComplex($entity->getId());
                            //$responseTemp = $entity->getZipCode();
                            $responseTemp = $count;
                            break;
                        }

                    case 'actions':
                        {

                            $urlAddProperty = $this->generateUrl('backend_admin_property_new');
                            $addProperty = "<a href='".$urlAddProperty."'><i class='fa fa-plus'></i><span class='item-label'></span></a>&nbsp;&nbsp;";


                            $urlEdit = $this->generateUrl('backend_admin_complex_edit', array('id' => $entity->getId()));
                            $edit = "<a href='".$urlEdit."'><i class='fa fa-pencil-square-o'></i><span class='item-label'></span></a>&nbsp;&nbsp;";

                            $urlDelete = $this->generateUrl('backend_admin_complex_delete', array('id' => $entity->getId()));
                            $delete = "<a class='btn-delete'  href='".$urlDelete."'><i class='fa fa-trash-o'></i><span class='item-label'></span></a>";

                            $responseTemp = $addProperty.$edit.$delete;
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
     * Creates a new Complex entity.
     *
     */
    public function newAction(Request $request)
    {

        //var_dump($_REQUEST);DIE;
        $this->get("services")->setVars('complex');
        $this->initialise();

        //var_dump($this->userLogged->getId());die;

        $entity = new Complex();
        $form   = $this->createCreateForm($entity);

        $countries = $this->em->getRepository('BackendAdminBundle:GeoCountry')->findBy(array("enabled" => 1));
        //complex_sector_type
        $complexSectorTypes = $this->em->getRepository('BackendAdminBundle:ComplexSectorType')->findBy(array("enabled" => 1), array("id" => "DESC"));
        ///property_type
        $propertyTypes = $this->em->getRepository('BackendAdminBundle:PropertyType')->findBy(array("enabled" => 1), array("id" => "DESC"));

        //redirected from REGISTER
        $register =  isset($_REQUEST["register"]) && intval($_REQUEST["register"]) != 0 ? $this->userLogged->getId() : 0;
        //var_dump($register);die;

        return $this->render('BackendAdminBundle:Complex:new.html.twig', array(
            'entity' => $entity,
            'form' => $form->createView(),
            'countries' => $countries,
            'complexSectorTypes' => $complexSectorTypes,
            'propertyTypes' => $propertyTypes,
            'register' => $register

        ));
    }



    /**
     * Finds and displays a Complex entity.
     *
     */
    public function showAction($entity)
    {
        $deleteForm = $this->createDeleteForm($entity);

        return $this->render('backend_admin_complex/show.html.twig', array(
            'entity' => $entity,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing Complex entity.
     *
     */
    public function editAction(Request $request, $id)
    {
        $this->get("services")->setVars('complex');
        $this->initialise();

        $id = intval($id);
        $entity = $this->em->getRepository('BackendAdminBundle:Complex')->find($id);

        if(!$entity){
            throw $this->createNotFoundException('Not found.');
        }

        //users cannot view private complexes
        $this->get('services')->checkComplexAccess($id);

        $deleteForm = $this->createDeleteForm($entity);
        $editForm = $this->createEditForm($entity);

        $editForm->handleRequest($request);

        $countries = $this->em->getRepository('BackendAdminBundle:GeoCountry')->findBy(array("enabled" => 1));

        //redirected from REGISTER
        $register =  isset($_REQUEST["register"]) ? 1 : 0;

        if ($editForm->isSubmitted() && $editForm->isValid()) {

            $this->em->persist($entity);
            $this->em->flush();

            return $this->redirectToRoute('backend_admin_complex_edit', array('id' => $id));
        }

        $shiftSchedule = $this->em->getRepository('BackendAdminBundle:Shift')->getSchedule($entity->getId());
        //print "<pre>";
        //var_dump($shiftSchedule);die;

        $complexAdmins = $this->em->getRepository('BackendAdminBundle:Complex')->getComplexAdmins($id);


        return $this->render('BackendAdminBundle:Complex:edit.html.twig', array(
            'entity' => $entity,
            'form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
            'countries' => $countries,
            'register' => $register,
            'complexAdmins' => $complexAdmins,
            'shiftSchedule' => $shiftSchedule,
            'edit' => $id,
            'role' => $this->role
        ));
    }

    /**
     * Deletes a Complex entity.
     *
     */
    public function deleteAction(Request $request, $id)
    {

        $this->get("services")->setVars('complex');
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('BackendAdminBundle:Complex')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Complex entity.');
        }
        else{

            //SOFT DELETE
            $entity->setEnabled(0);
            $this->get("services")->blameOnMe($entity);
            $em->persist($entity);
            $em->flush();

        }


        //DELETE FROM DATABASE
        /*
        $form = $this->createDeleteForm($entity);
        $form->handleRequest($request);

            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('BackendAdminBundle:Complex')->find($entity);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Complex entity.');
            }

			try{
				
	            $em->remove($entity);
	            $em->flush();        		
			
            } catch (\Doctrine\DBAL\DBALException $e) {
            	//var_dump($e->getCode());die;
                if ($e->getCode() == 0)
                {
                	//var_dump($e->getPrevious()->getCode());die;
                    if (intval($e->getPrevious()->getCode()) == 23000)
                    {
                        $this->get('services')->flashWarningForeignKey($request);
                        return $this->redirectToRoute('backend_admin_complex_index');
                    }
                    else
                    {
                        throw $e;
                    }
                }
                else
                {
                    throw $e;
                }
            }
        */


        $this->get('services')->flashSuccess($request);
        return $this->redirectToRoute('backend_admin_complex_index');
    }

    /**
     * Creates a form to delete a Complex entity.
     *
     * @param Complex
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($entity)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('backend_admin_complex_delete', array('id' => $entity->getId())))
            ->setMethod('DELETE')
            ->getForm()
            ;
    }




    /**
     * Creates a new Complex entity.
     *
     */
    public function createAction(Request $request)
    {

        //print "<pre>";
        //var_dump($_REQUEST);die;

        $this->get("services")->setVars('complex');

        $this->initialise();

        //var_dump($this->userLogged->getId());die;

        $entity = new Complex();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);
        /*print "<pre>";
        var_dump($form->getErrorsAsString());die;
         * */

        $token = $this->get('services')->getBCToken();


            if ($form->isValid()) {

                $myRequest = $request->request->get('complex');
                //var_dump($myRequest);die;
                //var_dump($request->get('complex');die;


                //AVATAR UPLOAD
                $myFile = $request->files->get("complex")["avatarPath"];
                if($myFile != NULL){

                    $file = $entity->getAvatarPath();
                    $fileName = md5(uniqid()).'.'.$file->guessExtension();
                    $file->move($this->getParameter('complex_avatars_directory'), $fileName);
                    $entity->setAvatarPath($entity->getAvatarUploadDir().$fileName);

                }

                $geoState = $this->em->getRepository('BackendAdminBundle:GeoState')->find(intval($_REQUEST["business"]["geoState"]));
                $entity->setGeoState($geoState);

                //Business
                $business = $this->em->getRepository('BackendAdminBundle:Business')->find($this->userLogged->getBusiness());
                $entity->setBusiness($business);
                $businessLocale = $business->getGeoState()->getGeoCountry()->getLocale();

                ///code + phone
                $objCountry = $this->em->getRepository('BackendAdminBundle:GeoCountry')->findOneByShortName(trim($_REQUEST["phone_code"]));
                $entity->setPhoneCountry($objCountry);

                //CREATE SECTORS and PROPERTIES
                $sectorQuantity = $_REQUEST["complex"]["sectionsQuantity"];
                $mySectorType = intval($_REQUEST["extra"]["sectorType"]);
                $sectorType = $this->em->getRepository('BackendAdminBundle:ComplexSectorType')->find($mySectorType);
                if($mySectorType == 0){ //OTHER
                    $sectorTypeName = trim($_REQUEST["extra"]["sectorTypeName"]);
                }
                else{
                    $sectorTypeName = $businessLocale == "en" ? $sectorType->getNameEN() : $sectorType->getNameES();
                }

                //properties per section
                $propertiesPerSection = intval($_REQUEST["complex"]["propertiesPerSection"]);

                //property Type
                $myPropertyType = intval($_REQUEST["extra"]["propertyType"]);
                $propertyType = $this->em->getRepository('BackendAdminBundle:PropertyType')->find($myPropertyType);

                if($myPropertyType == 0){ //OTHER
                    $propertyTypeName = trim($_REQUEST["extra"]["propertyTypeName"]);
                }
                else{
                    $propertyTypeName = $businessLocale == "en" ? $propertyType->getNameEN() : $propertyType->getNameES();
                }

                //BLAME ME
                $this->get("services")->blameOnMe($entity, "create");

                $this->em->persist($entity);
                $this->em->flush();

                //default FAQs for the complex
                $newComplexFaq = new ComplexFaq();
                $newComplexFaq->setEnabled(1);
                $newComplexFaq->setComplex($entity);
                $newComplexFaq->setDescriptionEN("<p>What is the APP for?</p>

<p>The APP helps administrate commercial and living complexes, by facilitating the communication between residents, managers, and administrators. Tenants can create tickets, book common spaces. All communication is centralized in one official channel.</p>

<p>&nbsp;</p>

<p>Is my information public in the APP?</p>

<p>No, your information is private. People can see what you share, for example tickets that you make public, your Avatar, your name.</p>

<p>&nbsp;</p>

<p>What do you mean by rewards?</p>

<p>We work with a gamified platform that allows the users to win points that later they can exchange for rewards. This way you motivate your users instead of punishing them by their actions.</p>

<p>&nbsp;</p>

<p>How do users Exchange their rewards?</p>

<p>The rewards available will be accessible from the app, on the rewards screen, where the users can see if the amount of points, they have is enough to exchange for the reward. If they have enough points, they can press redeem, and they will get a confirmation email, with the reward details.</p>

<p>&nbsp;</p>

<p>&nbsp;</p>

<p>What do I do if I don&rsquo;t get a response?</p>

<p>The response time may vary due to the shifts assigned to managers and supervisors. If you don&rsquo;t receive a response in over 24 hours, please contact the administration directly.</p>

<p>&nbsp;</p>

<p>&nbsp;</p>

<p>Is the APP a way to pay for maintenance?</p>

<p>No, you can&rsquo;t pay inside the application, the balance tab only helps you keep track of the payments the administration has assigned to the tenant and mark them as paid.</p>");

                $newComplexFaq->setDescriptionES("<p>&iquest;Para qu&eacute; es la aplicaci&oacute;n?</p>

<p>La aplicaci&oacute;n ayuda a administrar complejos comerciales y residenciales, al facilitar la comunicaci&oacute;n entre residentes, gerentes y administradores. Los inquilinos pueden crear tickets, reservar espacios comunes. Toda la comunicaci&oacute;n est&aacute; centralizada en un canal oficial.</p>

<p>&nbsp;</p>

<p>&iquest;Es mi informaci&oacute;n p&uacute;blica dentro de la aplicaci&oacute;n?</p>

<p>No, su informaci&oacute;n es privada. La gente puede ver lo que compartes, por ejemplo, los tickets que creas p&uacute;blicos, tu Avatar, tu nombre.</p>

<p>&nbsp;</p>

<p>&iquest;Qu&eacute; quieres decir con recompensas?</p>

<p>Trabajamos con una plataforma gamificada que permite a los usuarios ganar puntos que luego pueden canjear por recompensas. De esta manera usted motiva a sus usuarios en lugar de castigarlos por sus acciones.</p>

<p>&nbsp;</p>

<p>&iquest;C&oacute;mo intercambian los usuarios sus puntos por recompensas?</p>

<p>Las recompensas disponibles ser&aacute;n accesibles desde la aplicaci&oacute;n, en la pantalla de recompensas, donde los usuarios pueden ver si la cantidad de puntos que tienen es suficiente para canjear por la recompensa. Si tienen suficientes puntos, pueden presionar canjear y recibir&aacute;n un correo electr&oacute;nico de confirmaci&oacute;n con los detalles de la recompensa.</p>

<p>&nbsp;</p>

<p>&iquest;Qu&eacute; hago si no recibo respuesta?</p>

<p>El tiempo de respuesta puede variar debido a los turnos asignados a los gerentes y supervisores. Si no recibe una respuesta en m&aacute;s de 24 horas, comun&iacute;quese directamente con la administraci&oacute;n.</p>

<p>&nbsp;</p>

<p>&iquest;Se puede pagar el mantenimiento dentro de la aplicaci&oacute;n?</p>

<p>No, no puede pagar dentro de la aplicaci&oacute;n, la pesta&ntilde;a de saldo solo le ayuda a mantener un registro de los pagos que la administraci&oacute;n ha asignado al inquilino y marcarlos como pagados.</p>");

                //BLAME ME
                $this->get("services")->blameOnMe($newComplexFaq, "create");

                $this->em->persist($newComplexFaq);
                $this->em->flush();                

                ///CREATE COMPLEX TEAM ON GAMIFICATION
                ///
                $body = array();
                $body['name'] = $entity->getName();
                $body['description'] = $entity->getName()." ".$entity->getBusiness()->getName();
                $body['teamType'] = 3;//complex
                $body["parent"] = $entity->getBusiness()->getTeamCorrelative();//Business team correlative
                //
                $createTeamComplex = $this->get('services')->callBCSpace("POST", "teams", $body);

                $teamIDComplex = isset($createTeamComplex["id"]) ? intval($createTeamComplex["id"]) : 0;
                $entity->setTeamCorrelative($teamIDComplex);
                $this->em->persist($entity);

                    ///CREATE DEFAULT REWARDS
                    $token = $this->get('services')->getBCToken();
                    $arrRewards = $this->get('services')->callBCSpace( "POST", "teams/".$teamIDComplex."/rewards", array() );

                    //CREATE TEAMS -> ADMINS / TENANT

                    //TENANTS
                    $body = array();
                    $body['name'] = $entity->getName() . "TENANTS";
                    $body['description'] = $entity->getName()." ".$entity->getBusiness()->getName(). "TENANTS";
                    $body['teamType'] = 6;//tenants
                    $body["parent"] = $teamIDComplex;//Complex team correlative
                    //
                    $createTeamComplexTenant = $this->get('services')->callBCSpace("POST", "teams", $body);

                    $teamIDComplexTenant = isset($createTeamComplexTenant["id"]) ? intval($createTeamComplexTenant["id"]) : 0;
                    $entity->setTeamCorrelativeTenant($teamIDComplexTenant);
                    $this->em->persist($entity);


                    //ADMINS
                    $body = array();
                    $body['name'] = $entity->getName() . "ADMINS";
                    $body['description'] = $entity->getName()." ".$entity->getBusiness()->getName(). "ADMINS";
                    $body['teamType'] = 7;//admins
                    $body["parent"] = $teamIDComplex;//Complex team correlative
                    //
                    $createTeamComplexAdmin = $this->get('services')->callBCSpace("POST", "teams", $body);

                    $teamIDComplexAdmin = isset($createTeamComplexAdmin["id"]) ? intval($createTeamComplexAdmin["id"]) : 0;

                    $entity->setTeamCorrelativeAdmin($teamIDComplexAdmin);
                    $this->em->persist($entity);
                    //create admin user on gamification and enroll to team admins
                    $body = array();

                    $body['email'] = $this->userLogged->getEmail();
                    $body['username'] = $this->userLogged->getEmail();
                    $body['firstName'] = $this->userLogged->getName();
                    $body['lastName'] = $this->userLogged->getName();
                    $body['locale'] = $businessLocale;

                    //$createUser = $this->get('services')->callBCSpace("POST", "users", $body);

                    //Enroll user to the team admins
                    $body = array();
                    $userTeam = $this->get('services')->callBCSpace("POST", "users/{$this->userLogged->getEmail()}/teams/{$teamIDComplexAdmin}", $body);
                    $myUserTeam = isset($userTeam["id"]) ? intval($userTeam["id"]) : 0;
                    $objUser = $this->em->getRepository('BackendAdminBundle:User')->find($this->userLogged->getId());
                    $this->userLogged->setPlayerId($myUserTeam);
                    $this->em->persist($objUser);


                    $this->em->flush();

                    $emailComplex = strtolower(trim($entity->getEmail()));
                    $entity->setEmail($emailComplex);
                    $this->em->persist($entity);
                    $this->em->flush();




                //USER COMPLEX
                $userComplex = new UserComplex();
                $userComplex->setComplex($entity);
                $userComplex->setUser($this->userLogged);
                $this->get("services")->blameOnMe($userComplex, "create");
                $this->em->persist($userComplex);
                $this->em->flush();


                ///TICKET CATEGORIES FOR THE COMPLEX
                $this->em->getRepository('BackendAdminBundle:TicketCategory')->loadTicketCategories($entity->getId());


                //sectorType
                for($i=1; $i <= $sectorQuantity; $i++){

                    $newSector = new ComplexSector();
                    $newSector->setComplex($entity);
                    $newSector->setComplexSectorType($sectorType);
                    $newSector->setName($sectorTypeName. " ".$i);
                    $this->get("services")->blameOnMe($newSector, "create");

                    $this->em->persist($newSector);
                    $this->em->flush();

                    ///CREATE TEAM SECTOR
                    ///
                    //TENANTS
                    $body = array();
                    $body['name'] = $newSector->getName();
                    $body['description'] = $newSector->getName();
                    $body['teamType'] = 4;//sector
                    $body["parent"] = $teamIDComplexTenant;//Complex team correlative
                    //
                    $createTeamSector = $this->get('services')->callBCSpace("POST", "teams", $body);

                    $teamIDSector = isset($createTeamSector["id"])? intval($createTeamSector["id"]) : 0;
                    $newSector->setTeamCorrelative($teamIDSector);
                    $this->em->persist($newSector);
                    $this->em->flush();


                    //CREATE PROPERTIES
                    for ($j=1; $j<=$propertiesPerSection; $j++){

                        $newProperty = new Property();
                        $newProperty->setPropertyType($propertyType);
                        $newProperty->setComplex($entity);
                        $newProperty->setComplexSector($newSector);

                        //set temp code / then update
                        //$newProperty->setCode($business->getId().$entity->getId().$newSector->getId().$j);
                        $code = $this->get("services")->getToken(6);
                        $newProperty->setCode($code);

                        $myNumber = sprintf("%02d", $j);
                        $propertyNumber = $i.$myNumber;
                        $newProperty->setPropertyNumber($propertyNumber);
                        //$newProperty->setName($propertyTypeName." ".$propertyNumber);
                        $newProperty->setIsAvailable(1);
                        $this->get("services")->blameOnMe($newProperty, "create");
                        $this->em->persist($newProperty);
                        $this->em->flush();

                        $body = array();
                        $body['name'] = $newProperty->getName();
                        $body['description'] = $newProperty->getName();
                        $body['teamType'] = 5;//Property
                        $body["parent"] = $teamIDSector;//Sector team correlative

                        $createTeamProperty = $this->get('services')->callBCSpace("POST", "teams", $body);

                        $teamIDProperty = isset($createTeamProperty["id"])? intval($createTeamProperty["id"]) : 0;
                        $newProperty->setTeamCorrelative($teamIDProperty);
                        $this->em->persist($newProperty);
                        $this->em->flush();
                    }
                }

                //$this->em->persist($entity);
                $this->em->flush();


                //ADD POINTS
                $message = $this->translator->trans("label_register_create_complex"). ' '. $entity->getName();
                $playKey = "BC-A-00003";//create complex
                $this->get("services")->addPointsAdmin($entity, $message, $playKey);


                $this->get('services')->flashSuccess($request);
                return $this->redirect($this->generateUrl('backend_admin_complex_index'));

            }



        $this->get('services')->flashWarning($request);
        //var_dump($_REQUEST["register"]);die;

        if(isset($_REQUEST["register"])){
            if(intval($_REQUEST["register"]) != 0){
                return $this->redirect($this->generateUrl('backend_admin_complex_new', array("register" => $this->userLogged->getId())));
            }
            else{
                return $this->redirect($this->generateUrl('backend_admin_complex_new', array("register" => 0)));
            }
        }
        else{
            throw $this->createNotFoundException('Not found.');
        }



        /*
        return $this->render('BackendAdminBundle:Complex:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
        */
    }

    /**
     * Creates a form to create a Complex entity.
     *
     * @param Complex $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm($entity)
    {
        $this->get("services")->setVars('complex');
        $this->initialise();
        $form = $this->createForm(ComplexType::class, $entity, array(
            'action' => $this->generateUrl('backend_admin_complex_create'),
            'method' => 'POST',
        ));


        return $form;
    }




    /**
     * Creates a form to edit a Complex entity.
     *
     * @param Complex $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm($entity)
    {
        $this->get("services")->setVars('complex');
        $this->initialise();

        $form = $this->createForm(ComplexType::class, $entity, array(
            'action' => $this->generateUrl('backend_admin_complex_update', array('id' => $entity->getId())),
        ));


        return $form;
    }


    /**
     * Edits an existing Complex entity.
     *
     */
    public function updateAction(Request $request, $id)
    {


        //print "<pre>";
        //var_dump($_REQUEST);DIE;
        /*
        //var_dump($_REQUEST["my_schedule"]);die;

        var_dump(json_decode($_REQUEST["my_schedule"], true));
        die;
        */

        $this->get("services")->setVars('complex');
        $this->initialise();

        $entity = $this->em->getRepository('BackendAdminBundle:Complex')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Complex entity.');
        }

        $deleteForm = $this->createDeleteForm($entity);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);


        $token = $this->get('services')->getBCToken();


            $myPath = $entity->getAvatarPath();

            if ($editForm->isValid()) {
                $myRequest = $request->request->get('complex');


                $myFile = $request->files->get("complex")["avatarPath"];
                if($myFile != NULL){
                    $fileName = md5(uniqid()).'.'.$myFile->guessExtension();
                    $myFile->move($this->getParameter('complex_avatars_directory'), $fileName);
                    $entity->setAvatarPath($entity->getAvatarUploadDir().$fileName);

                }
                else{
                    $fileName = $myPath;
                    $entity->setAvatarPath($fileName);
                }

                ///code + phone
                $objCountry = $this->em->getRepository('BackendAdminBundle:GeoCountry')->findOneByShortName($_REQUEST["phone_code"]);
                $entity->setPhoneCountry($objCountry);


                $geoState = $this->em->getRepository('BackendAdminBundle:GeoState')->find(intval($_REQUEST["business"]["geoState"]));
                $entity->setGeoState($geoState);


                if($this->role == "SUPER ADMIN"){
                    if(isset($_REQUEST["complex"]["enabled"])){
                        $entity->setEnabled(true);
                    }
                    else{
                        $entity->setEnabled(false);
                    }


                    if(isset($_REQUEST["complex"]["latePayment"])){
                        $entity->setLatePayment(true);
                    }
                    else{
                        $entity->setLatePayment(false);
                    }


                }

                $this->em->persist($entity);

                $this->get("services")->blameOnMe($entity);
                $this->em->flush();

                ///UPDATE COMPLEX TEAM ON GAMIFICATION
                ///
                $body = array();
                $body['name'] = $entity->getName();
                $body['description'] = $entity->getName();
                $body['teamType'] = 3;//complex
                $body["parent"] = $entity->getBusiness()->getTeamCorrelative();//Business team correlative

                $updateTeamComplex = $this->get('services')->callBCSpace("PUT", "teams/{$entity->getTeamCorrelative()}", $body);

                /*SET THE WEEK SCHEDULE*/

                /*ERASE LAST SCHEDULE*/
                ///
                $this->em->getRepository('BackendAdminBundle:Shift')->clearShiftSchedule($id);

                $mySchedule = json_decode($_REQUEST["my_schedule"], true);

                foreach ($mySchedule as $key => $weekDay){

                    $day =  intval($weekDay["day"]);
                    $arrPeriods = $weekDay["periods"];

                    foreach ($arrPeriods as $pk => $period){
                        $start = $period["start"];
                        $end = $period["end"];

                        $newShift = new Shift();
                        $newShift->setComplex($entity);

                        $tmpUser = $this->em->getRepository('BackendAdminBundle:user')->findOneByEmail($period["title"]);
                        if(!$tmpUser){
                            continue;
                        }
                        $newShift->setAssignedTo($tmpUser);

                        $newShift->setWeekdaySingle($day);
                        $newShift->setHourFrom($start);
                        $newShift->setHourTo($end);

                        $this->get("services")->blameOnMe($newShift, "create");
                        $this->get("services")->blameOnMe($newShift, "update");

                        $this->em->persist($newShift);
                        $this->em->flush();

                    }

                }
                $this->get('services')->flashSuccess($request);
                return $this->redirect($this->generateUrl('backend_admin_complex_index', array('id' => $id)));

            }



        $this->get('services')->flashWarning($request);



        $countries = $this->em->getRepository('BackendAdminBundle:GeoCountry')->findBy(array("enabled" => 1));

        return $this->redirect($this->generateUrl('backend_admin_complex_edit', array('id' => $id)));

        /*
        return $this->render('BackendAdminBundle:Complex:edit.html.twig', array(
            'entity'      => $entity,
            'form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
            'countries' => $countries
        ));
        */
    }


    public function setSessionComplexAction(Request $request){


        $myPath = $_REQUEST["myPath"];
        $myComplex = $_REQUEST["selectComplex"];

        $this->get("services")->setSessionComplex($myComplex);

        return $this->redirect($this->generateUrl($myPath));

    }




}

