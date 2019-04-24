<?php

namespace Backend\AdminBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Translation\TranslatorInterface;
//use Symfony\Component\Form\Extension\Core\Type\SubmitType;


use Backend\AdminBundle\Entity\Role;
use Backend\AdminBundle\Form\RoleType;

/**
 * Role controller.
 *
 */
class RoleController extends Controller
{

    protected $em;
    protected $translator;
    protected $repository;
    private  $renderer;


    // Set up all necessary variable
    protected function initialise()
    {
        $this->em = $this->getDoctrine()->getManager();
        $this->repository = $this->em->getRepository('BackendAdminBundle:Role');
        $this->translator = $this->get('translator');
        $this->renderer = $this->get('templating');


    }


    public function indexAction(Request $request)
    {


        //var_dump($this->translator->trans('label_welcome'));
        $this->get("services")->setVars('role');
        $this->initialise();

        //print $this->translator->getLocale();die;

        return $this->render('BackendAdminBundle:Role:index.html.twig');


    }


    public function listDatatablesAction(Request $request)
    {

        $this->get("services")->setVars('role');

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
                    case 'nameES':
                        {
                            $responseTemp = $entity->getNameES();
                            break;
                        }
                    case 'nameEN':
                        {
                            $responseTemp = $entity->getName();
                            break;
                        }

                    case 'end_type':
                        {
                            $responseTemp = $entity->getEndType();
                            break;
                        }
                    case 'actions':
                        {
                            $urlEdit = $this->generateUrl('backend_admin_role_edit', array('id' => $entity->getId()));
                            $edit = "<a href='".$urlEdit."'><div class='btn btn-sm btn-primary'><span class='fa fa-search'></span></div></a>";

                            $urlDelete = $this->generateUrl('backend_admin_role_delete', array('id' => $entity->getId()));
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
     * Creates a new Role entity.
     *
     */
    public function newAction(Request $request)
    {
    	$this->get("services")->setVars('role');

        $entity = new Role();
        $form   = $this->createCreateForm($entity);
		 
	
        return $this->render('BackendAdminBundle:Role:new.html.twig', array(
            'entity' => $entity,
            'form' => $form->createView(),
        ));
    }

 

    /**
     * Finds and displays a Role entity.
     *
     */
    public function showAction($entity)
    {
        $deleteForm = $this->createDeleteForm($entity);

        return $this->render('backend_admin_role/show.html.twig', array(
            'entity' => $entity,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing Role entity.
     *
     */
    public function editAction(Request $request, $id)
    {
    	$this->get("services")->setVars('role');
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('BackendAdminBundle:Role')->find($id);
				
        $deleteForm = $this->createDeleteForm($entity);
		$editForm = $this->createEditForm($entity);
        //$editForm = $this->createForm('Backend\AdminBundle\Form\RoleType', $role);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirectToRoute('backend_admin_role_edit', array('id' => $id));
        }

        return $this->render('BackendAdminBundle:Role:edit.html.twig', array(
            'entity' => $entity,
            'form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }
	
    /**
     * Deletes a Role entity.
     *
     */
    public function deleteAction(Request $request, $id)
    {
    	
		$this->get("services")->setVars('role');
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('BackendAdminBundle:Role')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Role entity.');
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
            $entity = $em->getRepository('BackendAdminBundle:Role')->find($entity);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Role entity.');
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
                        return $this->redirectToRoute('backend_admin_role_index');
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
        return $this->redirectToRoute('backend_admin_role_index');
    }

    /**
     * Creates a form to delete a Role entity.
     *
     * @param Role $role The Role entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($entity)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('backend_admin_role_delete', array('id' => $entity->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
	
	
	
	
    /**
     * Creates a new Role entity.
     *
     */
    public function createAction(Request $request)
    {
    	
		$this->get("services")->setVars('role');

		
        $entity = new Role();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);
		/*print "<pre>";
		var_dump($form->getErrorsAsString());die;
		 * */
		 
        if ($form->isValid()) {
        	$myRequest = $request->request->get('role');
			//var_dump($myRequest);die;
			$em = $this->getDoctrine()->getManager();
			//var_dump($request->get('role');die;
			
			$entity->setName( strtoupper($this->get("services")->quitar_tildes(trim($myRequest["name"])))  );
            $this->get("services")->blameOnMe($entity, "create");
			
            $em->persist($entity);
            $em->flush();


			$this->get('services')->flashSuccess($request);
            return $this->redirect($this->generateUrl('backend_admin_role_index'));
			 
        }
		/*
		else{
			print "FORMULARIO NO VALIDO";DIE;
		}
		 * */

        return $this->render('BackendAdminBundle:Role:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a form to create a Role entity.
     *
     * @param Role $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm($entity)
    {
    	//$this->setVars();
        $form = $this->createForm(RoleType::class, $entity, array(
            'action' => $this->generateUrl('backend_admin_role_create'),
            'method' => 'POST'
        ));


        return $form;
    }	
	

	
	
    /**
    * Creates a form to edit a Role entity.
    *
    * @param Role $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm($entity)
    {
    	//$this->setVars();
        $form = $this->createForm(RoleType::class, $entity, array(
            'action' => $this->generateUrl('backend_admin_role_update', array('id' => $entity->getId())),
            //'method' => 'PUT',
            //'client' => $this->userLogged,
        ));


        return $form;
    }
	
	
    /**
     * Edits an existing Role entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
    	$this->get("services")->setVars('role');
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('BackendAdminBundle:Role')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Role entity.');
        }

        $deleteForm = $this->createDeleteForm($entity);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
        	$myRequest = $request->request->get('role');
			$entity->setName( strtoupper($this->get("services")->quitar_tildes(trim($myRequest["name"])))  );
            $this->get("services")->blameOnMe($entity);
            $em->flush();

			$this->get('services')->flashSuccess($request);
            return $this->redirect($this->generateUrl('backend_admin_role_index', array('id' => $id)));
			 
        }

        return $this->render('BackendAdminBundle:Role:edit.html.twig', array(
            'entity'      => $entity,
            'form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }


}
