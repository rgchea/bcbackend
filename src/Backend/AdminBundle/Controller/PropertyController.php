<?php

namespace Backend\AdminBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Translation\TranslatorInterface;


use Backend\AdminBundle\Entity\Property;
use Backend\AdminBundle\Form\PropertyType;

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


    }


    public function indexAction(Request $request)
    {


        //var_dump($this->translator->trans('label_welcome'));
        $this->get("services")->setVars('property');
        $this->initialise();

        //print $this->translator->getLocale();die;

        return $this->render('BackendAdminBundle:Property:index.html.twig');


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

        $results = $this->repository->getRequiredDTData($start, $length, $orders, $search, $columns, $filters);
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
                    case 'name':
                        {
                            $responseTemp = $entity->getName();
                            break;
                        }
                    case 'code':
                        {
                            $responseTemp = $entity->getCode();
                            break;
                        }
                    case 'owner':
                        {
                            if($entity->getOwner()){
                                $responseTemp = $entity->getOwner()->getName();
                            }
                            else{
                                $responseTemp = "";
                            }

                            break;
                        }
                    case 'tenant':
                        {
                            if($entity->getOwner()){
                                $responseTemp = $entity->getMainTenant()->getName();
                            }
                            else{
                                $responseTemp = "";
                            }

                            break;
                        }

                    case 'actions':
                        {
                            $urlEdit = $this->generateUrl('backend_admin_property_edit', array('id' => $entity->getId()));
                            $edit = "<a href='".$urlEdit."'><div class='btn btn-sm btn-primary'><span class='fa fa-search'></span></div></a>";

                            $urlDelete = $this->generateUrl('backend_admin_property_delete', array('id' => $entity->getId()));
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

            return $this->redirectToRoute('backend_admin_property_edit', array('id' => $id));
        }

        return $this->render('BackendAdminBundle:Property:edit.html.twig', array(
            'entity' => $entity,
            'form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
            'edit' => 1
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

            //$businessLocale = $business->getGeoState()->getGeoCountry()->getLocale();
            $sectorID = $_REQUEST["property"]["complexSector"];
            $objComplexSector = $this->em->getRepository('BackendAdminBundle:ComplexSector')->find($sectorID);
            $business = $objComplexSector->getComplex()->getBusiness();
            $businessLocale = $business->getGeoState()->getGeoCountry()->getLocale();

            $myPropertyType = intval($_REQUEST["property"]["propertyType"]);
            $propertyType = $this->em->getRepository('BackendAdminBundle:PropertyType')->find($myPropertyType);

            if($myPropertyType == 0){ //OTHER
                $propertyTypeName = trim($_REQUEST["extra"]["propertyTypeName"]);
            }
            else{
                $propertyTypeName = $businessLocale == "en" ? $propertyType->getNameEN() : $propertyType->getNameES();
            }

            //BLAME ME
            $this->get("services")->blameOnMe($entity, "create");
            $entity->setCode("0000");
            $this->em->persist($entity);
            $this->em->flush();

            $entity->setCode($business->getId().$objComplexSector->getComplex()->getId().$sectorID.$entity->getId());
            $entity->setIsAvailable(1);

            $this->em->persist($entity);
            $this->em->flush();


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
            'form'   => $form->createView(),
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
            'repository' => $this->em->getRepository('BackendAdminBundle:Complex'),

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
            'userID' => $this->userLogged->getId(),
            'repository' => $this->em->getRepository('BackendAdminBundle:Complex'),
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

            $this->get("services")->blameOnMe($entity);
            $em->flush();

            $this->get('services')->flashSuccess($request);
            return $this->redirect($this->generateUrl('backend_admin_property_index', array('id' => $id)));

        }

        //$countries = $this->em->getRepository('BackendAdminBundle:GeoCountry')->findBy(array("enabled" => 1));

        return $this->render('BackendAdminBundle:Property:edit.html.twig', array(
            'entity'      => $entity,
            'form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
            //'countries' => $countries
        ));
    }


}

