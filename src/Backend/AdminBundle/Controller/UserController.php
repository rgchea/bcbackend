<?php

namespace Backend\AdminBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Translation\TranslatorInterface;
//use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\File\File;


use Backend\AdminBundle\Entity\User;
use Backend\AdminBundle\Form\UserType;

/**
 * User controller.
 *
 */
class UserController extends Controller
{

    protected $em;
    protected $translator;
    protected $repository;
    private  $renderer;
    private $userLogged;
    private $role;
    private $session;



    // Set up all necessary variable
    protected function initialise()
    {
        $this->session = new Session();
        $this->em = $this->getDoctrine()->getManager();
        $this->repository = $this->em->getRepository('BackendAdminBundle:User');
        $this->translator = $this->get('translator');
        $this->renderer = $this->get('templating');
        $this->userLogged = $this->session->get('userLogged');
        $this->role = $this->session->get('userLogged')->getRole()->getName();


    }


    public function indexAction(Request $request)
    {


        //var_dump($this->translator->trans('label_welcome'));
        $this->get("services")->setVars('user');
        $this->initialise();

        //print $this->translator->getLocale();die;

        return $this->render('BackendAdminBundle:User:index.html.twig');


    }


    public function listDatatablesAction(Request $request)
    {

        $this->get("services")->setVars('user');

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
        $myArray = array();
        if($this->role != "SUPER ADMIN"){
            $myArray["business"] = $this->userLogged->getBusiness()->getId();
        }

        $results = $this->repository->getRequiredDTData($start, $length, $orders, $search, $columns, $myArray);
        $objects = $results["results"];
        $selected_objects_count = count($objects);
        //var_dump($selected_objects_count);die;

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
                    case 'role':
                        {
                            $responseTemp = $entity->getRole()->getName();
                            break;
                        }

                    case 'email':
                        {
                            $responseTemp = $entity->getEmail();
                            break;
                        }
                    case 'actions':
                        {
                            $urlEdit = $this->generateUrl('backend_admin_user_edit', array('id' => $entity->getId()));
                            $edit = "<a href='".$urlEdit."'><div class='btn btn-sm btn-primary'><span class='fa fa-search'></span></div></a>";

                            $urlDelete = $this->generateUrl('backend_admin_user_delete', array('id' => $entity->getId()));
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
        $return = $this->get("services")->serviceDataTable($request, $this->repository, $results, $myItems, $selected_objects_count);

        return $return;


    }




    /**
     * Creates a new User entity.
     *
     */
    public function newAction(Request $request)
    {
    	$this->get("services")->setVars('user');
    	$this->initialise();

        $entity = new User();
        $form   = $this->createCreateForm($entity);
		 
	
        return $this->render('BackendAdminBundle:User:new.html.twig', array(
            'entity' => $entity,
            'form' => $form->createView(),
            'new' => 1,
            'role' => $this->role,
            'userID' => $this->userLogged->getId(),

        ));
    }

 

    /**
     * Displays a form to edit an existing User entity.
     *
     */
    public function editAction(Request $request, $id)
    {



    	$this->get("services")->setVars('user');
    	$this->initialise();


        $entity = $this->repository->find($id);
				
        $deleteForm = $this->createDeleteForm($entity);
		$editForm = $this->createEditForm($entity);

        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {

            $this->em->persist($entity);
            $this->em->flush();

            return $this->redirectToRoute('backend_admin_user_edit', array('id' => $id));
        }

        return $this->render('BackendAdminBundle:User:edit.html.twig', array(
            'entity' => $entity,
            'form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
            'edit' => 1,
            'role' => $this->role
        ));
    }
	
    /**
     * Deletes a User entity.
     *
     */
    public function deleteAction(Request $request, $id)
    {
    	
		$this->get("services")->setVars('user');


        $entity = $this->repository->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find User entity.');
        }
        else{

            //SOFT DELETE
            $entity->setEnabled(0);
            $this->get("services")->blameOnMe($entity);
            $this->em->persist($entity);
            $this->em->flush();

        }


        //DELETE FROM DATABASE
        /*
        $form = $this->createDeleteForm($entity);
        $form->handleRequest($request);

            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('BackendAdminBundle:User')->find($entity);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find User entity.');
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
                        return $this->redirectToRoute('backend_admin_user_index');
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
        return $this->redirectToRoute('backend_admin_user_index');
    }

    /**
     * Creates a form to delete a User entity.
     *
     * @param User $user The User entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($entity)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('backend_admin_user_delete', array('id' => $entity->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
	
	
	
	
    /**
     * Creates a new User entity.
     *
     */
    public function createAction(Request $request)
    {

        /*
        print "<pre>";
        var_dump($_REQUEST);DIE;
        */

        if(!isset($_REQUEST["user"])){
            return $this->redirect($this->generateUrl('backend_admin_user_new'));
        }

		$this->get("services")->setVars('user');
        $this->initialise();

		//print $this->getParameter('avatars_directory');die;
		
        $entity = new User();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        $plainPassword = $form['password']->getData();

        $email = trim($_REQUEST["user"]["email"]);
        $checkExistence = $this->get('services')->checkExistence($email, 0);
        if($checkExistence != ""){
            $this->get('services')->flashCustom($request, $checkExistence);
        }


        if ($form->isValid() && ($checkExistence == "")) {


            $entity->setUsername($email);
            $entity->setPlainPassword($plainPassword);
            //var_dump($entity);die;

            $roleID = intval($_REQUEST["user"]["role"]);

            $objRole = $this->em->getRepository('BackendAdminBundle:Role')->find($roleID);
            $role = $objRole->getName();
            $entity->setRole($objRole);

            $newDate = $this->get('services')->dateUSAToMysql($_REQUEST["user"]["birthdate"]);
            $birthdate = new \DateTime($newDate);
            $entity->setBirthdate($birthdate);



            //AVATAR UPLOAD
            $myFile = $request->files->get("user")["avatarPath"];
            if($myFile != NULL){

                $file = $entity->getAvatarPath();
                $fileName = md5(uniqid()).'.'.$file->guessExtension();
                $file->move($this->getParameter('avatars_directory'), $fileName);
                $entity->setAvatarPath($entity->getAvatarUploadDir().$fileName);

            }



            //link to the business
            if($this->role != "SUPER ADMIN"){
                $entity->setBusiness($this->userLogged->getBusiness());

            }


            $this->get("services")->blameOnMe($entity, "create");
			
            $this->em->persist($entity);
            $this->em->flush();


			$this->get('services')->flashSuccess($request);
            return $this->redirect($this->generateUrl('backend_admin_user_index'));
			 
        }



        return $this->render('BackendAdminBundle:User:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
            'role' => $this->role,
            'userID' => $this->userLogged->getId(),
            'new' => 1,

        ));
    }

    /**
     * Creates a form to create a User entity.
     *
     * @param User $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm($entity)
    {
        $this->get("services")->setVars('user');
        $this->initialise();
        $form = $this->createForm(UserType::class, $entity, array(
            'action' => $this->generateUrl('backend_admin_user_create'),
            'method' => 'POST',
            'role' => $this->role,
            'userID' => $this->userLogged->getId(),
        ));


        return $form;
    }	
	

	
	
    /**
    * Creates a form to edit a User entity.
    *
    * @param User $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm($entity)
    {
        $this->get("services")->setVars('user');
        $this->initialise();

        $form = $this->createForm(UserType::class, $entity, array(
            'action' => $this->generateUrl('backend_admin_user_update', array('id' => $entity->getId())),
            'role' => $this->role,
            'userID' => $this->userLogged->getId(),
        ));


        return $form;
    }
	
	
    /**
     * Edits an existing User entity.
     *
     */
    public function updateAction(Request $request, $id)
    {


    	$this->get("services")->setVars('user');
        $this->initialise();

        if(!isset($_REQUEST["user"])){
            return $this->redirect($this->generateUrl('backend_admin_user_index'));
        }

        $entity = $this->em->getRepository('BackendAdminBundle:User')->find($id);


        $myPath = $entity->getAvatarPath();

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find User entity.');
        }

        $deleteForm = $this->createDeleteForm($entity);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        //$previous_password = $entity->getPassword();
        $plainPassword = trim($editForm['password']->getData());


        $email = trim($_REQUEST["user"]["email"]);
        $checkExistence = $this->get('services')->checkExistence($email, $id);

        if($checkExistence != ""){
            $this->get('services')->flashCustom($request, $checkExistence);
        }

        ////
        if ($editForm->isValid() && ($checkExistence == "")) {

            $entity->setUsername($email);
            $roleID = intval($_REQUEST["user"]["role"]);

            $objRole = $this->em->getRepository('BackendAdminBundle:Role')->find($roleID);
            $role = $objRole->getName();
            $entity->setRole($objRole);

            $newDate = $this->get('services')->dateUSAToMysql($_REQUEST["user"]["birthdate"]);
            $birthdate = new \DateTime($newDate);
            $entity->setBirthdate($birthdate);


            $myFile = $request->files->get("user")["avatarPath"];
            if($myFile != NULL){
                $fileName = md5(uniqid()).'.'.$myFile->guessExtension();
                $myFile->move($this->getParameter('avatars_directory'), $fileName);
                $entity->setAvatarPath($entity->getAvatarUploadDir().$fileName);

            }
            else{
                $fileName = $myPath;
                $entity->setAvatarPath($fileName);
            }

            //
            if(!empty($plainPassword)){
                $entity->setPlainPassword($plainPassword);

            }else{
                /*
                $entity->setPassword($previous_password);
                print "entra2";die;
                */
            }


            $this->get("services")->blameOnMe($entity);
            $this->em->flush();

			$this->get('services')->flashSuccess($request);
            return $this->redirect($this->generateUrl('backend_admin_user_index', array('id' => $id)));
			 
        }



        return $this->render('BackendAdminBundle:User:edit.html.twig', array(
            'entity'      => $entity,
            'form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
            'role' => $this->role,
            'userID' => $this->userLogged->getId(),
        ));
    }


}
