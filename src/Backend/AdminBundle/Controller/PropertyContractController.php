<?php

namespace Backend\AdminBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Translation\TranslatorInterface;
use Symfony\Component\Config\Definition\Exception\Exception;


use Backend\AdminBundle\Entity\PropertyContract;
use Backend\AdminBundle\Entity\Property;
use Backend\AdminBundle\Form\PropertyContractType;

/**
 * PropertyContract controller.
 *
 */
class PropertyContractController extends Controller
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
        $this->repository = $this->em->getRepository('BackendAdminBundle:PropertyContract');
        $this->translator = $this->get('translator');
        $this->renderer = $this->get('templating');
        $this->userLogged = $this->session->get('userLogged');
        $this->role = $this->session->get('userLogged')->getRole()->getName();


    }


    public function indexAction(Request $request)
    {


        //var_dump($this->translator->trans('label_welcome'));
        $this->get("services")->setVars('propertyContract');
        $this->initialise();

        //print $this->translator->getLocale();die;


        return $this->render('BackendAdminBundle:PropertyContract:index.html.twig');


    }


    public function listDatatablesAction(Request $request)
    {

        $this->get("services")->setVars('propertyContract');

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

        $results = $this->repository->getRequiredDTData($start, $length, $orders, $search, $columns, $dateRange =  null, $filters);
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
                            $responseTemp = $entity->getProperty()->getComplexSector()->getComplex()->getName();
                            break;
                        }

                    case 'sector':
                        {
                            $responseTemp = $entity->getProperty()->getComplexSector()->getName();
                            break;
                        }

                    case 'property':
                        {
                            $responseTemp = $entity->getProperty()->getName();
                            break;
                        }

                    case 'active':
                        {
                            $responseTemp = $entity->getIsActive() == 1 ? $this->translator->trans('label_yes') : $this->translator->trans('label_no');
                            break;
                        }

                    case 'price':
                        {
                            $responseTemp = $entity->getRentalPrice();
                            break;
                        }


                    case 'actions':
                        {
                            $urlEdit = $this->generateUrl('backend_admin_property_contract_edit', array('id' => $entity->getId()));
                            $edit = "<a href='".$urlEdit."'><i class='fa fa-pencil-square-o'></i><span class='item-label'></span></a>&nbsp;&nbsp;";

                            $urlDelete = $this->generateUrl('backend_admin_property_contract_delete', array('id' => $entity->getId()));
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


    public function listPropertyDatatablesAction(Request $request){
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
        if($this->role != "SUPER ADMIN"){// == "ADMIN"
            $arrComplex = $this->em->getRepository('BackendAdminBundle:Complex')->getComplexByUser($this->userLogged->getId());
            foreach ($arrComplex as $k =>$v) {
                $filters[$v] = $v;//the complex id
            }
        }

        // Process Parameters

        $results = $this->em->getRepository('BackendAdminBundle:Property')->getRequiredDTData($start, $length, $orders, $search, $columns, $filters);
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

                            //$select = "<a onclick='selectProperty(\"".$entity->getId()."\","."\"".$entity->getName()."\")' href='#'><i class='fas fa-check'></i><span class='item-label'></span></a>";

                            $select = "<a onclick='selectProperty(".$entity->getId().")' href='#'><i class='fas fa-check'></i><span class='item-label'></span></a>";
                            //var_dump($select);die;
                            //$responseTemp = htmlentities( $select) ;
                            $responseTemp = $select;
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
    public function newAction(Request $request)
    {
        $this->get("services")->setVars('propertyContract');
        $this->initialise();

        $entity = new PropertyContract();
        $form   = $this->createCreateForm($entity);

        return $this->render('BackendAdminBundle:PropertyContract:new.html.twig', array(
            'entity' => $entity,
            'form' => $form->createView(),


        ));
    }



    /**
     * Finds and displays a PropertyContract entity.
     *
     */
    public function showAction($entity)
    {
        $deleteForm = $this->createDeleteForm($entity);

        return $this->render('backend_admin_property_contract/show.html.twig', array(
            'entity' => $entity,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing PropertyContract entity.
     *
     */
    public function editAction(Request $request, $id)
    {
        $this->get("services")->setVars('propertyContract');
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('BackendAdminBundle:PropertyContract')->find($id);

        $deleteForm = $this->createDeleteForm($entity);
        $editForm = $this->createEditForm($entity);

        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirectToRoute('backend_admin_property_contract_edit', array('id' => $id));
        }

        return $this->render('BackendAdminBundle:PropertyContract:edit.html.twig', array(
            'entity' => $entity,
            'form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
            'edit' => 1
        ));
    }

    /**
     * Deletes a PropertyContract entity.
     *
     */
    public function deleteAction(Request $request, $id)
    {

        $this->get("services")->setVars('propertyContract');
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('BackendAdminBundle:PropertyContract')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find PropertyContract entity.');
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
            $entity = $em->getRepository('BackendAdminBundle:PropertyContract')->find($entity);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find PropertyContract entity.');
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
                        return $this->redirectToRoute('backend_admin_property_contract_index');
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
        return $this->redirectToRoute('backend_admin_property_contract_index');
    }

    /**
     * Creates a form to delete a PropertyContract entity.
     *
     * @param PropertyContract
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($entity)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('backend_admin_property_contract_delete', array('id' => $entity->getId())))
            ->setMethod('DELETE')
            ->getForm()
            ;
    }




    /**
     * Creates a new PropertyContract entity.
     *
     */
    public function createAction(Request $request)
    {

        //print "<pre>";
        //var_dump($_REQUEST);DIE;

        $this->get("services")->setVars('propertyContract');
        $this->initialise();


        $entity = new PropertyContract();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);
        /*print "<pre>";
        var_dump($form->getErrorsAsString());die;
         * */

        if ($form->isValid()) {
            $myRequest = $_REQUEST["property_contract"];

            $objProperty = $this->em->getRepository('BackendAdminBundle:Property')->find(intval($myRequest["property"]));
            $entity->setProperty($objProperty);
            $entity->setStartDate(new \DateTime($myRequest["startDate"]));
            $entity->setEndDate(new \DateTime($myRequest["endDate"]));


            $this->get("services")->blameOnMe($entity, "create");

            $this->em->persist($entity);
            $this->em->flush();

            $objProperty->setIsAvailable(0);
            $this->em->persist($objProperty);
            $this->em->flush();


            $this->em->getRepository('BackendAdminBundle:PropertyContract')->disableContracts($entity->getId(), $objProperty->getId());


            $this->get('services')->flashSuccess($request);
            return $this->redirect($this->generateUrl('backend_admin_property_contract_index'));

        }
        /*
        else{
            print "FORMULARIO NO VALIDO";DIE;
        }
         * */

        return $this->render('BackendAdminBundle:PropertyContract:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a form to create a PropertyContract entity.
     *
     * @param PropertyContract $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm($entity)
    {
        $this->get("services")->setVars('propertyContract');
        $this->initialise();
        $form = $this->createForm(PropertyContractType::class, $entity, array(
            'action' => $this->generateUrl('backend_admin_property_contract_create'),
            'method' => 'POST',
            'role' => $this->role,
            'userID' => $this->userLogged->getId(),
            'repository' => $this->em->getRepository('BackendAdminBundle:Complex'),
        ));


        return $form;
    }




    /**
     * Creates a form to edit a PropertyContract entity.
     *
     * @param PropertyContract $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm($entity)
    {
        $this->get("services")->setVars('propertyContract');
        $this->initialise();

        $form = $this->createForm(PropertyContractType::class, $entity, array(
            'action' => $this->generateUrl('backend_admin_property_contract_update', array('id' => $entity->getId())),
            'role' => $this->role,
            'userID' => $this->userLogged->getId(),
            'repository' => $this->em->getRepository('BackendAdminBundle:Complex'),
        ));


        return $form;
    }


    /**
     * Edits an existing PropertyContract entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $this->get("services")->setVars('propertyContract');
        $this->initialise();

        $entity = $this->em->getRepository('BackendAdminBundle:PropertyContract')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find PropertyContract entity.');
        }

        $deleteForm = $this->createDeleteForm($entity);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {

            $myRequest = $_REQUEST["property_contract"];

            $entity->setProperty($this->em->getRepository('BackendAdminBundle:Property')->find(intval($myRequest["property"])));
            $entity->setStartDate(new \DateTime($myRequest["startDate"]));
            $entity->setEndDate(new \DateTime($myRequest["endDate"]));


            $this->get("services")->blameOnMe($entity, "update");
            $this->em->flush();

            $this->get('services')->flashSuccess($request);
            return $this->redirect($this->generateUrl('backend_admin_property_contract_index', array('id' => $id)));

        }

        return $this->render('BackendAdminBundle:PropertyContract:edit.html.twig', array(
            'entity'      => $entity,
            'form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    public function infoAction(Request $request){

        $this->get("services")->setVars('property');
        $this->initialise();

        $propertyContractID  = $_REQUEST["id"];
        $entity = $this->repository->find($propertyContractID);

        if($entity){

            $response["id"] = $entity->getId();
            $response["start"] = $entity->getStartDate()->format("m/d/Y");
            $response["end"] = $entity->getEndDate()->format("m/d/Y");
            $response["whopaysmaintenance"] = $entity->getWhoPaysMaintenance() == "tenant" ? $this->translator->trans("label_tenant") : $this->translator->trans("label_owner");
            $response["maintenance"] = number_format(floatval($entity->getMaintenancePrice()), 2, ".", "" );
            $response["tenant"] = "";
            $response["tenantMail"] = "";
            $response["tenantPhone"] = "";

            if($entity->getMainTenantContract()){
                $tenant = $entity->getMainTenantContract()->getUser();

                if($tenant){
                    $response["tenant"] = $entity->getMainTenantContract()->getUser()->getName();
                    $response["tenantMail"] = $entity->getMainTenantContract()->getUser()->getEmail();
                    $response["tenantPhone"] = $entity->getMainTenantContract()->getUser()->getMobilePhone();

                }


            }



            //owner


            /*
             * $owner = $entity->getMainTenantContract()->geOwner();

            if($owner != null){
                $response["owner"] = $owner->getName();
                $response["owner_mail"] = $owner->getEmail();
                $response["owner_phone"] = $owner->getMobilePhone();

            }
            else{
                $response["owner"] = $entity->getMainTenantContract()->getOwnerEmail();
                $response["owner_mail"] = $entity->getMainTenantContract()->getOwnerEmail();
                $response["owner_phone"] = "";

            }
            */

            $returnResponse = new JsonResponse();
            $returnResponse->setJson(json_encode($response));

            return $returnResponse;

        }
        else{
            throw new \Exception("Invalid contract ID.");
        }



    }



}

