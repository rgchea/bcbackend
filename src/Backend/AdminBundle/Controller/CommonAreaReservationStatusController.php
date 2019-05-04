<?php

namespace Backend\AdminBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Translation\TranslatorInterface;


use Backend\AdminBundle\Entity\CommonAreaReservationStatus;
use Backend\AdminBundle\Form\CommonAreaReservationStatusType;

/**
 * CommonAreaReservationStatus controller.
 *
 */
class CommonAreaReservationStatusController extends Controller
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
        $this->repository = $this->em->getRepository('BackendAdminBundle:CommonAreaReservationStatus');
        $this->translator = $this->get('translator');
        $this->renderer = $this->get('templating');
        $this->userLogged = $this->session->get('userLogged');
        $this->role = $this->session->get('userLogged')->getRole()->getName();


    }


    public function indexAction(Request $request)
    {


        //var_dump($this->translator->trans('label_welcome'));
        $this->get("services")->setVars('commonAreaReservationStatus');
        $this->initialise();

        //print $this->translator->getLocale();die;


        return $this->render('BackendAdminBundle:CommonAreaReservationStatus:index.html.twig');


    }


    public function listDatatablesAction(Request $request)
    {

        $this->get("services")->setVars('commonAreaReservationStatus');

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

        $results = $this->repository->getRequiredDTData($start, $length, $orders, $search, $columns, $dateRange =  null);
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
                    case 'nameEN':
                        {
                            $responseTemp = $entity->getNameEN();
                            break;
                        }

                    case 'nameES':
                        {
                            $responseTemp = $entity->getNameES();
                            break;
                        }
                        
                    case 'comment':
                        {
                            $responseTemp = $entity->getComment();
                            break;
                        }

                    case 'actions':
                        {
                            $urlEdit = $this->generateUrl('backend_admin_common_area_reservation_status_edit', array('id' => $entity->getId()));
                            $edit = "<a href='".$urlEdit."'><i class='fa fa-pencil-square-o'></i><span class='item-label'></span></a>&nbsp;&nbsp;";

                            $urlDelete = $this->generateUrl('backend_admin_common_area_reservation_status_delete', array('id' => $entity->getId()));
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
     * Creates a new CommonAreaReservationStatus entity.
     *
     */
    public function newAction(Request $request)
    {
        $this->get("services")->setVars('commonAreaReservationStatus');
        $this->initialise();

        $entity = new CommonAreaReservationStatus();
        $form   = $this->createCreateForm($entity);

        return $this->render('BackendAdminBundle:CommonAreaReservationStatus:new.html.twig', array(
            'entity' => $entity,
            'form' => $form->createView(),


        ));
    }



    /**
     * Finds and displays a CommonAreaReservationStatus entity.
     *
     */
    public function showAction($entity)
    {
        $deleteForm = $this->createDeleteForm($entity);

        return $this->render('backend_admin_common_area_reservation_status/show.html.twig', array(
            'entity' => $entity,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing CommonAreaReservationStatus entity.
     *
     */
    public function editAction(Request $request, $id)
    {
        $this->get("services")->setVars('commonAreaReservationStatus');
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('BackendAdminBundle:CommonAreaReservationStatus')->find($id);

        $deleteForm = $this->createDeleteForm($entity);
        $editForm = $this->createEditForm($entity);

        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirectToRoute('backend_admin_common_area_reservation_status_edit', array('id' => $id));
        }

        return $this->render('BackendAdminBundle:CommonAreaReservationStatus:edit.html.twig', array(
            'entity' => $entity,
            'form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a CommonAreaReservationStatus entity.
     *
     */
    public function deleteAction(Request $request, $id)
    {

        $this->get("services")->setVars('commonAreaReservationStatus');
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('BackendAdminBundle:CommonAreaReservationStatus')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find CommonAreaReservationStatus entity.');
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
            $entity = $em->getRepository('BackendAdminBundle:CommonAreaReservationStatus')->find($entity);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find CommonAreaReservationStatus entity.');
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
                        return $this->redirectToRoute('backend_admin_common_area_reservation_status_index');
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
        return $this->redirectToRoute('backend_admin_common_area_reservation_status_index');
    }

    /**
     * Creates a form to delete a CommonAreaReservationStatus entity.
     *
     * @param CommonAreaReservationStatus
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($entity)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('backend_admin_common_area_reservation_status_delete', array('id' => $entity->getId())))
            ->setMethod('DELETE')
            ->getForm()
            ;
    }




    /**
     * Creates a new CommonAreaReservationStatus entity.
     *
     */
    public function createAction(Request $request)
    {


        $this->get("services")->setVars('commonAreaReservationStatus');
        $this->initialise();


        $entity = new CommonAreaReservationStatus();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);
        /*print "<pre>";
        var_dump($form->getErrorsAsString());die;
         * */

        if ($form->isValid()) {

            $this->get("services")->blameOnMe($entity, "create");

            $this->em->persist($entity);
            $this->em->flush();


            $this->get('services')->flashSuccess($request);
            return $this->redirect($this->generateUrl('backend_admin_common_area_reservation_status_index'));

        }
        /*
        else{
            print "FORMULARIO NO VALIDO";DIE;
        }
         * */

        return $this->render('BackendAdminBundle:CommonAreaReservationStatus:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a form to create a CommonAreaReservationStatus entity.
     *
     * @param CommonAreaReservationStatus $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm($entity)
    {
        $this->get("services")->setVars('commonAreaReservationStatus');
        $this->initialise();
        $form = $this->createForm(CommonAreaReservationStatusType::class, $entity, array(
            'action' => $this->generateUrl('backend_admin_common_area_reservation_status_create'),
            'method' => 'POST',
        ));


        return $form;
    }




    /**
     * Creates a form to edit a CommonAreaReservationStatus entity.
     *
     * @param CommonAreaReservationStatus $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm($entity)
    {
        $this->get("services")->setVars('commonAreaReservationStatus');
        $this->initialise();

        $form = $this->createForm(CommonAreaReservationStatusType::class, $entity, array(
            'action' => $this->generateUrl('backend_admin_common_area_reservation_status_update', array('id' => $entity->getId())),
        ));


        return $form;
    }


    /**
     * Edits an existing CommonAreaReservationStatus entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $this->get("services")->setVars('commonAreaReservationStatus');
        $this->initialise();


        $entity = $this->em->getRepository('BackendAdminBundle:CommonAreaReservationStatus')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find CommonAreaReservationStatus entity.');
        }

        $deleteForm = $this->createDeleteForm($entity);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {

            $this->get("services")->blameOnMe($entity);
            $this->em->flush();

            $this->get('services')->flashSuccess($request);
            return $this->redirect($this->generateUrl('backend_admin_common_area_reservation_status_index', array('id' => $id)));

        }

        return $this->render('BackendAdminBundle:CommonAreaReservationStatus:edit.html.twig', array(
            'entity'      => $entity,
            'form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }


}

