<?php

namespace Backend\AdminBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Translation\TranslatorInterface;


use Backend\AdminBundle\Entity\Complex;
use Backend\AdminBundle\Form\ComplexType;
use Backend\AdminBundle\Entity\UserComplex;
use Backend\AdminBundle\Entity\ComplexSector;
use Backend\AdminBundle\Entity\Property;


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



        $results = $this->repository->getRequiredDTData($start, $length, $orders, $search, $columns, $filters, $businessLocale);
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

                    case 'actions':
                        {
                            $urlEdit = $this->generateUrl('backend_admin_complex_edit', array('id' => $entity->getId()));
                            $edit = "<a href='".$urlEdit."'><div class='btn btn-sm btn-primary'><span class='fa fa-search'></span></div></a>";

                            $urlDelete = $this->generateUrl('backend_admin_complex_delete', array('id' => $entity->getId()));
                            $delete = "<a class='btn btn-danger btn-delete' href='".$urlDelete."'><i class='fa fa-trash-o'></i></a>";

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
     * Creates a new Complex entity.
     *
     */
    public function newAction(Request $request)
    {


        $this->get("services")->setVars('complex');
        $this->initialise();

        $entity = new Complex();
        $form   = $this->createCreateForm($entity);

        $countries = $this->em->getRepository('BackendAdminBundle:GeoCountry')->findBy(array("enabled" => 1));
        //complex_sector_type
        $complexSectorTypes = $this->em->getRepository('BackendAdminBundle:ComplexSectorType')->findBy(array("enabled" => 1), array("id" => "DESC"));
        ///property_type
        $propertyTypes = $this->em->getRepository('BackendAdminBundle:PropertyType')->findBy(array("enabled" => 1), array("id" => "DESC"));

        //redirected from REGISTER
        $register =  isset($_REQUEST["register"]) ? 1 : 0;

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
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('BackendAdminBundle:Complex')->find($id);

        $deleteForm = $this->createDeleteForm($entity);
        $editForm = $this->createEditForm($entity);

        $editForm->handleRequest($request);

        $countries = $this->em->getRepository('BackendAdminBundle:GeoCountry')->findBy(array("enabled" => 1));

        //redirected from REGISTER
        $register =  isset($_REQUEST["register"]) ? 1 : 0;

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirectToRoute('backend_admin_complex_edit', array('id' => $id));
        }

        return $this->render('BackendAdminBundle:Complex:edit.html.twig', array(
            'entity' => $entity,
            'form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
            'countries' => $countries,
            'register' => $register
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
        //var_dump($_REQUEST);DIE;

        $this->get("services")->setVars('complex');
        $this->initialise();


        $entity = new Complex();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);
        /*print "<pre>";
        var_dump($form->getErrorsAsString());die;
         * */

        if ($form->isValid()) {

            $myRequest = $request->request->get('complex');
            //var_dump($myRequest);die;
            //var_dump($request->get('complex');die;


            $geoState = $this->em->getRepository('BackendAdminBundle:GeoState')->find(intval($_REQUEST["business"]["geoState"]));
            $entity->setGeoState($geoState);

            //Business
            $business = $this->em->getRepository('BackendAdminBundle:Business')->find($this->userLogged->getBusiness());
            $entity->setBusiness($business);
            $businessLocale = $business->getGeoState()->getGeoCountry()->getLocale();




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

            //USER COMPLEX
            $userComplex = new UserComplex();
            $userComplex->setComplex($entity);
            $userComplex->setUser($this->userLogged);
            $this->get("services")->blameOnMe($userComplex, "create");
            $this->em->persist($userComplex);


            ///TICKET CATEGORIES FOR THE COMPLEX
            $this->em->getRepository('BackendAdminBundle:TicketCategory')->loadTicketCategories($entity);


            //sectorType
            for($i=1; $i <= $sectorQuantity; $i++){

                $newSector = new ComplexSector();
                $newSector->setComplex($entity);
                $newSector->setComplexSectorType($sectorType);
                $newSector->setName($sectorTypeName. " ".$i);
                $this->get("services")->blameOnMe($newSector, "create");

                $this->em->persist($newSector);
                $this->em->flush();

                //CREATE PROPERTIES
                for ($j=1; $j<=$propertiesPerSection; $j++){

                    $newProperty = new Property();
                    $newProperty->setPropertyType($propertyType);
                    $newProperty->setComplexSector($newSector);
                    $newProperty->setCode($business->getId().$entity->getId().$newSector->getId().$j);
                    $myNumber = sprintf("%02d", $j);
                    $newProperty->setName($propertyTypeName." ".$i.$myNumber);
                    $newProperty->setIsAvailable(1);
                    $this->get("services")->blameOnMe($newProperty, "create");
                    $this->em->persist($newProperty);

                }


            }



            //$this->em->persist($entity);
            $this->em->flush();


            $this->get('services')->flashSuccess($request);
            return $this->redirect($this->generateUrl('backend_admin_complex_index'));

        }
        /*
        else{
            print "FORMULARIO NO VALIDO";DIE;
        }
         * */

        return $this->render('BackendAdminBundle:Complex:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
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
        $this->get("services")->setVars('complex');
        $this->initialise();

        $entity = $this->em->getRepository('BackendAdminBundle:Complex')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Complex entity.');
        }

        $deleteForm = $this->createDeleteForm($entity);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $myRequest = $request->request->get('complex');

            $geoState = $this->em->getRepository('BackendAdminBundle:GeoState')->find(intval($_REQUEST["business"]["geoState"]));
            $entity->setGeoState($geoState);

            $this->em->persist($entity);

            $this->get("services")->blameOnMe($entity);
            $this->em->flush();

            $this->get('services')->flashSuccess($request);
            return $this->redirect($this->generateUrl('backend_admin_complex_index', array('id' => $id)));

        }

        $countries = $this->em->getRepository('BackendAdminBundle:GeoCountry')->findBy(array("enabled" => 1));

        return $this->render('BackendAdminBundle:Complex:edit.html.twig', array(
            'entity'      => $entity,
            'form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
            'countries' => $countries
        ));
    }


}

