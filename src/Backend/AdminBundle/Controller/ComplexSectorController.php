<?php

namespace Backend\AdminBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Translation\TranslatorInterface;


use Backend\AdminBundle\Entity\ComplexSector;
use Backend\AdminBundle\Form\ComplexSectorType;
use Backend\AdminBundle\Entity\Property;

/**
 * ComplexSector controller.
 *
 */
class ComplexSectorController extends Controller
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
        $this->repository = $this->em->getRepository('BackendAdminBundle:ComplexSector');
        $this->translator = $this->get('translator');
        $this->renderer = $this->get('templating');
        $this->userLogged = $this->session->get('userLogged');
        $this->role = $this->session->get('userLogged')->getRole()->getName();


    }


    public function indexAction(Request $request)
    {


        //var_dump($this->translator->trans('label_welcome'));
        $this->get("services")->setVars('complexSector');
        $this->initialise();

        //print $this->translator->getLocale();die;


        return $this->render('BackendAdminBundle:ComplexSector:index.html.twig');


    }


    public function listDatatablesAction(Request $request)
    {

        $this->get("services")->setVars('complexSector');

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

            $arrComplex = $this->em->getRepository('BackendAdminBundle:Complex')->getComplexByUser($this->userLogged->getId());
            foreach ($arrComplex as $k =>$v) {
                $filters[$v] = $v;//the complex id
            }

        }


        // Process Parameters
        //$businessLocale = $this->userLogged->getBusiness()->getGeoState()->getGeoCountry()->getLocale();
        $businessLocale = $this->translator->getLocale();

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
                    case 'name':
                        {
                            $responseTemp = $entity->getName();
                            break;
                        }

                    case 'sectorType':
                        {
                            $responseTemp = $entity->getComplexSectorType();
                            break;
                        }
                    case 'complex':
                        {
                            $responseTemp = $entity->getComplex()->getName();
                            break;
                        }
                    case 'actions':
                        {
                            $urlEdit = $this->generateUrl('backend_admin_complex_sector_edit', array('id' => $entity->getId()));
                            $edit = "<a href='".$urlEdit."'><i class='fa fa-pencil-square-o'></i><span class='item-label'></span></a>&nbsp;&nbsp;";

                            $urlDelete = $this->generateUrl('backend_admin_complex_sector_delete', array('id' => $entity->getId()));
                            $delete = "<a class='btn-delete'  href='".$urlDelete."'><i class='fa fa-trash-o'></i><span class='item-label'></span></a>";

                            $responseTemp = $edit.$delete;
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
     * Creates a new ComplexSector entity.
     *
     */
    public function newAction(Request $request)
    {
        $this->get("services")->setVars('complexSector');
        $this->initialise();

        $entity = new ComplexSector();
        $form   = $this->createCreateForm($entity);

        $propertyTypes = $this->em->getRepository('BackendAdminBundle:PropertyType')->findBy(array("enabled" => 1), array("id" => "DESC"));

        return $this->render('BackendAdminBundle:ComplexSector:new.html.twig', array(
            'entity' => $entity,
            'form' => $form->createView(),
            'new' => 1,
            'propertyTypes' => $propertyTypes,

        ));
    }



    /**
     * Finds and displays a ComplexSector entity.
     *
     */
    public function showAction($entity)
    {
        $deleteForm = $this->createDeleteForm($entity);

        return $this->render('backend_admin_complex_sector/show.html.twig', array(
            'entity' => $entity,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing ComplexSector entity.
     *
     */
    public function editAction(Request $request, $id)
    {
        $this->get("services")->setVars('complexSector');
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('BackendAdminBundle:ComplexSector')->find($id);

        $deleteForm = $this->createDeleteForm($entity);
        $editForm = $this->createEditForm($entity);

        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirectToRoute('backend_admin_complex_sector_edit', array('id' => $id));
        }

        return $this->render('BackendAdminBundle:ComplexSector:edit.html.twig', array(
            'entity' => $entity,
            'form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
            'edit' => $entity->getId()
        ));
    }

    /**
     * Deletes a ComplexSector entity.
     *
     */
    public function deleteAction(Request $request, $id)
    {

        $this->get("services")->setVars('complexSector');
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('BackendAdminBundle:ComplexSector')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find ComplexSector entity.');
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
            $entity = $em->getRepository('BackendAdminBundle:ComplexSector')->find($entity);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find ComplexSector entity.');
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
                        return $this->redirectToRoute('backend_admin_complex_sector_index');
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
        return $this->redirectToRoute('backend_admin_complex_sector_index');
    }

    /**
     * Creates a form to delete a ComplexSector entity.
     *
     * @param ComplexSector
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($entity)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('backend_admin_complex_sector_delete', array('id' => $entity->getId())))
            ->setMethod('DELETE')
            ->getForm()
            ;
    }




    /**
     * Creates a new ComplexSector entity.
     *
     */
    public function createAction(Request $request)
    {
        //print "<pre>";
        //var_dump($_REQUEST);DIE;
        $this->get("services")->setVars('complexSector');
        $this->initialise();


        $entity = new ComplexSector();
        //$entity->setComplex($this->em->getRepository('BackendAdminBundle:Complex')->find($_REQUEST["complex_sector"]["complex"]));
        $form = $this->createCreateForm($entity);
        //var_dump($request);die;
        $form->handleRequest($request);

        /*print "<pre>";
        var_dump($form->getErrorsAsString());die;
         * */
        $businessLocale = $this->userLogged->getBusiness()->getGeoState()->getGeoCountry()->getLocale();
        //$businessLocale = $this->translator->getLocale();

        if ($form->isValid()) {

            $entity->setComplex($this->em->getRepository('BackendAdminBundle:Complex')->find($_REQUEST["complex_sector"]["complex"]));

            //CREATE SECTORS and PROPERTIES
            //properties per section
            $propertiesPerSection = intval($_REQUEST["complex_sector"]["propertiesPerSection"]);

            //property Type
            $myPropertyType = intval($_REQUEST["extra"]["propertyType"]);
            $propertyType = $this->em->getRepository('BackendAdminBundle:PropertyType')->find($myPropertyType);

            if($myPropertyType == 0){ //OTHER
                $propertyTypeName = trim($_REQUEST["extra"]["propertyTypeName"]);
            }
            else{
                $propertyTypeName = $businessLocale == "en" ? $propertyType->getNameEN() : $propertyType->getNameES();
            }

            //sectorType

            $this->get("services")->blameOnMe($entity, "create");

            $this->em->persist($entity);
            $this->em->flush();

            $prefix = trim($_REQUEST["extra"]["prefix"]);
            //CREATE PROPERTIES
            for ($j=1; $j<=$propertiesPerSection; $j++){

                $newProperty = new Property();
                $newProperty->setPropertyType($propertyType);
                $newProperty->setComplexSector($entity);
                $newProperty->setComplex($entity->getComplex());
                //business, complex, sector, index
                //$newProperty->setCode($this->userLogged->getBusiness()->getId().$_REQUEST["complex_sector"]["complex"].$entity->getId().$j);
                $code = $this->get("services")->getToken(6);
                $newProperty->setCode($code);

                $myNumber = sprintf("%02d", $j);
                $propertyNumber = $prefix.$myNumber;
                $newProperty->setPropertyNumber($propertyNumber);
                $newProperty->setName($propertyTypeName." ".$propertyNumber);
                $newProperty->setIsAvailable(1);
                $this->get("services")->blameOnMe($newProperty, "create");
                $this->em->persist($newProperty);
                $this->em->flush();

            }

            $this->em->flush();


            $this->get('services')->flashSuccess($request);
            return $this->redirect($this->generateUrl('backend_admin_complex_sector_index'));

        }
        /*
        else{
            print "FORMULARIO NO VALIDO";DIE;
        }
         * */

        $propertyTypes = $this->em->getRepository('BackendAdminBundle:PropertyType')->findBy(array("enabled" => 1), array("id" => "DESC"));

        return $this->render('BackendAdminBundle:ComplexSector:new.html.twig', array(
            'entity' => $entity,
            'form' => $form->createView(),
            'new' => 1,
            'propertyTypes' => $propertyTypes,

        ));

    }

    /**
     * Creates a form to create a ComplexSector entity.
     *
     * @param ComplexSector $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm($entity)
    {
        $this->get("services")->setVars('complexSector');
        $this->initialise();
        $form = $this->createForm(ComplexSectorType::class, $entity, array(
            'action' => $this->generateUrl('backend_admin_complex_sector_create'),
            'method' => 'POST',
            'role' => $this->role,
            'userID' => $this->userLogged->getId(),
            'repository' => $this->em->getRepository('BackendAdminBundle:Complex'),
        ));


        return $form;
    }




    /**
     * Creates a form to edit a ComplexSector entity.
     *
     * @param ComplexSector $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm($entity)
    {
        $this->get("services")->setVars('complexSector');
        $this->initialise();

        $form = $this->createForm(ComplexSectorType::class, $entity, array(
            'action' => $this->generateUrl('backend_admin_complex_sector_update', array('id' => $entity->getId())),
            'role' => $this->role,
            'userID' => $this->userLogged->getId(),
            'repository' => $this->em->getRepository('BackendAdminBundle:Complex'),
        ));


        return $form;
    }


    /**
     * Edits an existing ComplexSector entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $this->get("services")->setVars('complexSector');
        $this->initialise();

        $entity = $this->em->getRepository('BackendAdminBundle:ComplexSector')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find ComplexSector entity.');
        }

        $deleteForm = $this->createDeleteForm($entity);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $entity->setComplex($this->em->getRepository('BackendAdminBundle:Complex')->find($_REQUEST["complex_sector"]["complex"]));

            $this->get("services")->blameOnMe($entity);
            $this->em->flush();

            $this->get('services')->flashSuccess($request);
            return $this->redirect($this->generateUrl('backend_admin_complex_sector_index', array('id' => $id)));

        }

        return $this->render('BackendAdminBundle:ComplexSector:edit.html.twig', array(
            'entity'      => $entity,
            'form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }


    public function listPropertiesAction(Request $request){

        $this->get("services")->setVars('complexSector');
        $this->initialise();

        $sectorID = intval($_REQUEST["sector_id"]);

        $properties = $this->em->getRepository('BackendAdminBundle:Property')->getPropertiesWithContract($sectorID);

        //print "<pre>";
        //var_dump($properties);die;

        return new JsonResponse($properties);


    }


}

