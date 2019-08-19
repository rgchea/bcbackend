<?php

namespace Backend\AdminBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Translation\TranslatorInterface;
//use Symfony\Component\Form\Extension\Core\Type\SubmitType;


use Backend\AdminBundle\Entity\PropertyContractTransaction;
use Backend\AdminBundle\Form\PropertyContractTransactionType;

/**
 * PropertyContractTransaction controller.
 *
 */
class PropertyContractTransactionController extends Controller
{

    protected $em;
    protected $translator;
    protected $repository;
    private  $renderer;
    private $session;


    // Set up all necessary variable
    protected function initialise()
    {
        $this->session = new Session();
        $this->em = $this->getDoctrine()->getManager();
        $this->repository = $this->em->getRepository('BackendAdminBundle:PropertyContractTransaction');
        $this->translator = $this->get('translator');
        $this->renderer = $this->get('templating');


    }


    public function indexAction(Request $request)
    {
        
        //var_dump($this->translator->trans('label_welcome'));
        $this->get("services")->setVars('propertyContractTransaction');
        $this->initialise();

        //print $this->translator->getLocale();die;

        return $this->render('BackendAdminBundle:PropertyContractTransaction:index.html.twig',
            array('myPath' => 'backend_admin_peropertycontractransaction_index'));


    }


    public function listDatatablesAction(Request $request)
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

        $results = $this->repository->getRequiredDTData($start, $length, $orders, $search, $columns, $filterComplex, $this->translator->getLocale());

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
     * Creates a new PropertyContractTransaction entity.
     *
     */
    public function newAction(Request $request)
    {
        $this->get("services")->setVars('propertyContractTransaction');

        $entity = new PropertyContractTransaction();
        $form   = $this->createCreateForm($entity);


        return $this->render('BackendAdminBundle:PropertyContractTransaction:new.html.twig', array(
            'entity' => $entity,
            'form' => $form->createView(),
            'myPath' => 'backend_admin_propertycontracttransaction_new',
            'new' => true

        ));
    }



    /**
     * Finds and displays a PropertyContractTransaction entity.
     *
     */
    public function showAction($entity)
    {
        $deleteForm = $this->createDeleteForm($entity);

        return $this->render('backend_admin_propertyContractTransaction/show.html.twig', array(
            'entity' => $entity,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing PropertyContractTransaction entity.
     *
     */
    public function editAction(Request $request, $id)
    {
        $this->get("services")->setVars('propertyContractTransaction');
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('BackendAdminBundle:PropertyContractTransaction')->find($id);

        $deleteForm = $this->createDeleteForm($entity);
        $editForm = $this->createEditForm($entity);
        //$editForm = $this->createForm('Backend\AdminBundle\Form\PropertyContractTransactionType', $propertyContractTransaction);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirectToRoute('backend_admin_propertycontracttransaction_edit', array('id' => $id));
        }

        return $this->render('BackendAdminBundle:PropertyContractTransaction:edit.html.twig', array(
            'entity' => $entity,
            'form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
            'edit' => $entity->getId()
        ));
    }

    /**
     * Deletes a PropertyContractTransaction entity.
     *
     */
    public function deleteAction(Request $request, $id)
    {

        $this->get("services")->setVars('propertyContractTransaction');
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('BackendAdminBundle:PropertyContractTransaction')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find PropertyContractTransaction entity.');
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
            $entity = $em->getRepository('BackendAdminBundle:PropertyContractTransaction')->find($entity);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find PropertyContractTransaction entity.');
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
                        return $this->redirectToRoute('backend_admin_propertycontracttransaction_index');
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
        return $this->redirectToRoute('backend_admin_propertycontracttransaction_index');
    }

    /**
     * Creates a form to delete a PropertyContractTransaction entity.
     *
     * @param PropertyContractTransaction $propertyContractTransaction The PropertyContractTransaction entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($entity)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('backend_admin_propertycontracttransaction_delete', array('id' => $entity->getId())))
            ->setMethod('DELETE')
            ->getForm()
            ;
    }




    /**
     * Creates a new PropertyContractTransaction entity.
     *
     */
    public function createAction(Request $request)
    {

        $this->get("services")->setVars('propertyContractTransaction');


        $entity = new PropertyContractTransaction();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);
        /*print "<pre>";
        var_dump($form->getErrorsAsString());die;
         * */

        if ($form->isValid()) {
            $myRequest = $request->request->get('propertyContractTransaction');
            //var_dump($myRequest);die;
            $em = $this->getDoctrine()->getManager();
            //var_dump($request->get('propertyContractTransaction');die;

            $this->get("services")->blameOnMe($entity, "create");

            $em->persist($entity);
            $em->flush();


            $this->get('services')->flashSuccess($request);
            return $this->redirect($this->generateUrl('backend_admin_propertycontracttransaction_index'));

        }
        /*
        else{
            print "FORMULARIO NO VALIDO";DIE;
        }
         * */

        return $this->render('BackendAdminBundle:PropertyContractTransaction:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a form to create a PropertyContractTransaction entity.
     *
     * @param PropertyContractTransaction $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm($entity)
    {
        //$this->setVars();
        $form = $this->createForm(PropertyContractTransactionType::class, $entity, array(
            'action' => $this->generateUrl('backend_admin_propertycontracttransaction_create'),
            'method' => 'POST'
        ));


        return $form;
    }




    /**
     * Creates a form to edit a PropertyContractTransaction entity.
     *
     * @param PropertyContractTransaction $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm($entity)
    {
        //$this->setVars();
        $form = $this->createForm(PropertyContractTransactionType::class, $entity, array(
            'action' => $this->generateUrl('backend_admin_propertycontracttransaction_update', array('id' => $entity->getId())),
            //'method' => 'PUT',
            //'client' => $this->userLogged,
        ));


        return $form;
    }


    /**
     * Edits an existing PropertyContractTransaction entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $this->get("services")->setVars('propertyContractTransaction');
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('BackendAdminBundle:PropertyContractTransaction')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find PropertyContractTransaction entity.');
        }

        $deleteForm = $this->createDeleteForm($entity);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $myRequest = $request->request->get('propertyContractTransaction');
            $this->get("services")->blameOnMe($entity);
            $em->flush();

            $this->get('services')->flashSuccess($request);
            return $this->redirect($this->generateUrl('backend_admin_propertycontracttransaction_index', array('id' => $id)));

        }

        return $this->render('BackendAdminBundle:PropertyContractTransaction:edit.html.twig', array(
            'entity'      => $entity,
            'form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }


}
