<?php

namespace Backend\AdminBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Translation\TranslatorInterface;


use Backend\AdminBundle\Entity\TicketCategory;
use Backend\AdminBundle\Form\TicketCategoryType;

/**
 * TicketCategory controller.
 *
 */
class TicketCategoryController extends Controller
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
        $this->repository = $this->em->getRepository('BackendAdminBundle:TicketCategory');
        $this->translator = $this->get('translator');
        $this->renderer = $this->get('templating');
        $this->userLogged = $this->session->get('userLogged');
        $this->role = $this->session->get('userLogged')->getRole()->getName();


    }


    public function indexAction(Request $request)
    {


        //var_dump($this->translator->trans('label_welcome'));
        $this->get("services")->setVars('ticketCategory');
        $this->initialise();

        //print $this->translator->getLocale();die;


        return $this->render('BackendAdminBundle:TicketCategory:index.html.twig', array(
            'myPath' => 'backend_admin_ticket_category_index',
        ));


    }


    public function listDatatablesAction(Request $request)
    {

        $this->get("services")->setVars('ticketCategory');

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

        $filterComplex = $this->get("services")->getSessionComplex();


        $results = $this->repository->getRequiredDTData($start, $length, $orders, $search, $columns, $dateRange =  null, $filterComplex);
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

                    case 'complex':
                        {
                            $responseTemp = $entity->getComplex()->getName();
                            break;
                        }
                    case 'actions':
                        {

                            $urlEdit = $this->generateUrl('backend_admin_ticket_category_edit', array('id' => $entity->getId()));
                            $edit = "<a href='".$urlEdit."'><i class='fa fa-pencil-square-o'></i><span class='item-label'></span></a>&nbsp;&nbsp;";

                            $urlDelete = $this->generateUrl('backend_admin_ticket_category_delete', array('id' => $entity->getId()));
                            $delete = "<a class='btn-delete'  href='".$urlDelete."'><i class='fa fa-trash-o'></i><span class='item-label'></span></a>";


                            if($entity->getComplex()->getId() == 0){

                                if($this->role == "SUPER ADMIN"){
                                    $responseTemp = $edit.$delete;
                                }
                                else{
                                    $responseTemp = "&nbsp;";
                                }

                                break;
                            }
                            else{
                                $responseTemp = $edit.$delete;
                                break;
                            }


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
     * Creates a new TicketCategory entity.
     *
     */
    public function newAction(Request $request)
    {
        $this->get("services")->setVars('ticketCategory');
        $this->initialise();

        $entity = new TicketCategory();
        $form   = $this->createCreateForm($entity);

        if($this->role == "SUPER ADMIN"){
            $icons = $this->em->getRepository('BackendAdminBundle:Icon')->findBy(array("enabled" => 1));
        }
        else{
            $icons = $this->em->getRepository('BackendAdminBundle:Icon')->findBy(array("enabled" => 1, "isGeneral" => 0));
        }

        $strIcons = "";
        foreach ($icons as $i){
            $strIcons .= '{title: "'.$i->getIconClass().'", searchTerms: ["'.$i->getName().'"]},';
        }


        return $this->render('BackendAdminBundle:TicketCategory:new.html.twig', array(
            'entity' => $entity,
            'form' => $form->createView(),
            'strIcons' => $strIcons


        ));
    }



    /**
     * Finds and displays a TicketCategory entity.
     *
     */
    public function showAction($entity)
    {
        $deleteForm = $this->createDeleteForm($entity);

        return $this->render('backend_admin_ticket_category/show.html.twig', array(
            'entity' => $entity,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing TicketCategory entity.
     *
     */
    public function editAction(Request $request, $id)
    {
        $this->get("services")->setVars('ticketCategory');
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('BackendAdminBundle:TicketCategory')->find($id);

        $deleteForm = $this->createDeleteForm($entity);
        $editForm = $this->createEditForm($entity);

        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirectToRoute('backend_admin_ticket_category_edit', array('id' => $id));
        }


        if($this->role == "SUPER ADMIN"){
            $icons = $this->em->getRepository('BackendAdminBundle:Icon')->findBy(array("enabled" => 1));
        }
        else{
            $icons = $this->em->getRepository('BackendAdminBundle:Icon')->findBy(array("enabled" => 1, "isGeneral" => 0));
        }

        $strIcons = "";
        foreach ($icons as $i){
            $strIcons .= '{title: "'.$i->getIconClass().'", searchTerms: ["'.$i->getName().'"]},';
        }


        return $this->render('BackendAdminBundle:TicketCategory:edit.html.twig', array(
            'entity' => $entity,
            'form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
            'edit' => $entity->getId(),
            'strIcons' => $strIcons,
        ));
    }

    /**
     * Deletes a TicketCategory entity.
     *
     */
    public function deleteAction(Request $request, $id)
    {

        $this->get("services")->setVars('ticketCategory');
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('BackendAdminBundle:TicketCategory')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find TicketCategory entity.');
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
            $entity = $em->getRepository('BackendAdminBundle:TicketCategory')->find($entity);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find TicketCategory entity.');
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
                        return $this->redirectToRoute('backend_admin_ticket_category_index');
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
        return $this->redirectToRoute('backend_admin_ticket_category_index');
    }

    /**
     * Creates a form to delete a TicketCategory entity.
     *
     * @param TicketCategory
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($entity)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('backend_admin_ticket_category_delete', array('id' => $entity->getId())))
            ->setMethod('DELETE')
            ->getForm()
            ;
    }




    /**
     * Creates a new TicketCategory entity.
     *
     */
    public function createAction(Request $request)
    {
        //print "<pre>";
        //var_dump($_REQUEST);DIE;
        $this->get("services")->setVars('ticketCategory');
        $this->initialise();


        $entity = new TicketCategory();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);
        /*print "<pre>";
        var_dump($form->getErrorsAsString());die;
         * */



        if ($form->isValid()) {

            $complex = intval($_REQUEST["ticket_category"]["complex"]);
            if($this->role == "SUPER ADMIN" && ($complex == 0)){
                $entity->setIsGeneral(1);

            }
            else{
                $entity->setIsGeneral(0);
            }

            $iconClass = trim($_REQUEST["ticket_category"]["iconClass"]);
            $objIcon = $this->em->getRepository('BackendAdminBundle:Icon')->findOneByIconClass($iconClass);
            if($objIcon){
                $entity->setIcon($objIcon);
            }


            $entity->setColor(trim($_REQUEST["ticket_category"]["color"]));
            $entity->setComplex($this->em->getRepository('BackendAdminBundle:Complex')->find($complex));

            $this->get("services")->blameOnMe($entity, "create");

            $this->em->persist($entity);
            $this->em->flush();


            $this->get('services')->flashSuccess($request);
            return $this->redirect($this->generateUrl('backend_admin_ticket_category_index'));

        }
        /*
        else{
            print "FORMULARIO NO VALIDO";DIE;
        }
         * */

        return $this->render('BackendAdminBundle:TicketCategory:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a form to create a TicketCategory entity.
     *
     * @param TicketCategory $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm($entity)
    {
        $this->get("services")->setVars('ticketCategory');
        $this->initialise();
        $form = $this->createForm(TicketCategoryType::class, $entity, array(
            'action' => $this->generateUrl('backend_admin_ticket_category_create'),
            'method' => 'POST',
            'role' => $this->role,
            'userID' => $this->userLogged->getId(),
            'repository' => $this->em->getRepository('BackendAdminBundle:Complex'),
        ));


        return $form;
    }




    /**
     * Creates a form to edit a TicketCategory entity.
     *
     * @param TicketCategory $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm($entity)
    {
        $this->get("services")->setVars('ticketCategory');
        $this->initialise();

        $form = $this->createForm(TicketCategoryType::class, $entity, array(
            'action' => $this->generateUrl('backend_admin_ticket_category_update', array('id' => $entity->getId())),
            'role' => $this->role,
            'userID' => $this->userLogged->getId(),
            'repository' => $this->em->getRepository('BackendAdminBundle:Complex'),
        ));


        return $form;
    }


    /**
     * Edits an existing TicketCategory entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $this->get("services")->setVars('ticketCategory');
        $this->initialise();

        $entity = $this->em->getRepository('BackendAdminBundle:TicketCategory')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find TicketCategory entity.');
        }

        $deleteForm = $this->createDeleteForm($entity);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {

            $entity->setColor(trim($_REQUEST["ticket_category"]["color"]));
            $entity->setComplex($this->em->getRepository('BackendAdminBundle:Complex')->find($_REQUEST["ticket_category"]["complex"]));

            $iconClass = trim($_REQUEST["ticket_category"]["iconClass"]);
            $objIcon = $this->em->getRepository('BackendAdminBundle:Icon')->findOneByIconClass($iconClass);
            if($objIcon){
                $entity->setIcon($objIcon);
            }

            $this->get("services")->blameOnMe($entity);
            $this->em->flush();

            $this->get('services')->flashSuccess($request);
            return $this->redirect($this->generateUrl('backend_admin_ticket_category_index', array('id' => $id)));

        }

        return $this->render('BackendAdminBundle:TicketCategory:edit.html.twig', array(
            'entity'      => $entity,
            'form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }


}

