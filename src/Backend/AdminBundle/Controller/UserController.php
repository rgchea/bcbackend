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
use Backend\AdminBundle\Entity\UserComplex;

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


        return $this->render('BackendAdminBundle:User:index.html.twig', array(
                'role' => $this->role
        ));


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
                            $edit = "<a href='".$urlEdit."'><i class='fa fa-pencil-square-o'></i><span class='item-label'></span></a>&nbsp;&nbsp;";

                            $urlDelete = $this->generateUrl('backend_admin_user_delete', array('id' => $entity->getId()));
                            $delete = "<a class='btn-delete'  href='".$urlDelete."'><i class='fa fa-trash-o'></i><span class='item-label'></span></a>";

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

        $businessID = $this->userLogged->getBusiness()->getId();
        $arrComplex = $this->em->getRepository('BackendAdminBundle:Complex')->findBy(array("business" => $businessID), array("name" => "ASC"));

        return $this->render('BackendAdminBundle:User:new.html.twig', array(
            'entity' => $entity,
            'form' => $form->createView(),
            'new' => 1,
            'role' => $this->role,
            'userID' => $this->userLogged->getId(),
            'arrComplex' => $arrComplex

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

        $arrComplexReturn = array();
        $userRole = $entity->getRole()->getName();
        if($userRole != "SUPER ADMIN" || $userRole != "ADMIN"){
            $businessID = $entity->getBusiness()->getId();

            $arrComplex = $this->em->getRepository('BackendAdminBundle:Complex')->findBy(array("business" => $businessID), array("name" => "ASC"));
            $assignedComplex = $this->em->getRepository('BackendAdminBundle:Complex')->getComplexByUser($entity->getId());
            //var_dump($assignedComplex);die;


            foreach ($arrComplex as $complex ){

                $complexID = $complex->getId();
                $arrComplexReturn[$complexID] = array();
                $arrComplexReturn[$complexID]["id"] = $complexID;
                $arrComplexReturn[$complexID]["name"] = $complex->getName();
                $arrComplexReturn[$complexID]["assigned"] = 0;


                if(array_search($complex->getId(), $assignedComplex)){
                    $arrComplexReturn[$complexID]["assigned"] = 1;
                }


            }


        }




        return $this->render('BackendAdminBundle:User:edit.html.twig', array(
            'entity' => $entity,
            'form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
            'edit' => $id,
            'role' => $this->role,
            'arrComplex' => $arrComplexReturn,
            "userLooged" => $this->userLogged->getId()
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


        //print "<pre>";
        //var_dump($_REQUEST);DIE;


        if(!isset($_REQUEST["user"])){
            return $this->redirect($this->generateUrl('backend_admin_user_new'));
        }

		$this->get("services")->setVars('user');
        $this->initialise();

		//print $this->getParameter('avatars_directory');die;
		
        $entity = new User();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        //$plainPassword = $form['password']->getData();

        $email = trim($_REQUEST["user"]["email"]);
        $checkExistence = $this->get('services')->checkExistence($email, 0);
        if($checkExistence != ""){
            $this->get('services')->flashCustom($request, $checkExistence);
        }


        if ($form->isValid() && ($checkExistence == "")) {


            $entity->setUsername($email);
            $plainPassword = uniqid();
            $entity->setPlainPassword($plainPassword);
            //var_dump($entity);die;

            $roleID = intval($_REQUEST["user"]["role"]);

            $objRole = $this->em->getRepository('BackendAdminBundle:Role')->find($roleID);
            $role = $objRole->getName(); //GETS THE NAME IN ENGLISH
            $entity->setRole($objRole);

            if(isset($_REQUEST["user"]["birthdate"])){
                $reqBirthdate = $_REQUEST["user"]["birthdate"];

                $newDate = $this->get('services')->dateUSAToMysql($reqBirthdate);
                $birthdate = new \DateTime($newDate);
                $entity->setBirthdate($birthdate);

            }





            //AVATAR UPLOAD
            $myFile = $request->files->get("user")["avatarPath"];
            if($myFile != NULL){

                $file = $entity->getAvatarPath();
                $fileName = md5(uniqid()).'.'.$file->guessExtension();
                $file->move($this->getParameter('avatars_directory'), $fileName);
                $entity->setAvatarPath($entity->getAvatarUploadDir().$fileName);

            }

            //IMPORTANT link user to the business
            $entity->setBusiness($this->userLogged->getBusiness());


            $bodyHtml =  $this->userLogged->getEmail()."&nbsp;".$this->translator->trans('label_register_invite_msg')."<br/>";
            $bodyHtml .= "<b>Email:&nbsp;</b>".$entity->getEmail()."<br/>";
            $bodyHtml .= "<b>Password:&nbsp;</b>".$plainPassword."<br/><br/>";

            //contact
            $bodyHtml .= $this->translator->trans('label_register_contact');

            //var_dump($bodyHtml);die;


            $to = $entity->getEmail();
            //($subject, $to, $bodyHtml, $from = null){
            $message = $this->get('services')->generalTemplateMail($this->translator->trans('label_welcome'), $to, $bodyHtml);



            $this->get("services")->blameOnMe($entity, "create");
			$entity->setEnabled(1);
            $this->em->persist($entity);
            $this->em->flush();


            //COMPLEX ASSIGNMENT
            if(isset($_REQUEST["complex"])){

                foreach ($_REQUEST["complex"] as $key => $cValue){

                    $userComplex = new UserComplex();
                    $userComplex->setUser($entity);
                    $objComplex = $this->em->getRepository('BackendAdminBundle:Complex')->find($key);
                    $userComplex->setComplex($objComplex);
                    $userComplex->setEnabled(1);

                    $this->get("services")->blameOnMe($userComplex, "create");
                    $this->em->persist($userComplex);

                }
            }
            $this->em->flush();


			$this->get('services')->flashSuccess($request);
            return $this->redirect($this->generateUrl('backend_admin_user_index'));
			 
        }

        $businessID = $this->userLogged->getBusiness()->getId();
        $arrComplex = $this->em->getRepository('BackendAdminBundle:Complex')->findBy(array("business" => $businessID), array("name" => "ASC"));


        return $this->render('BackendAdminBundle:User:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
            'role' => $this->role,
            'userID' => $this->userLogged->getId(),
            'new' => 1,
            'arrComplex' => $arrComplex

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


        $business = $entity->getBusiness() != null ? $entity->getBusiness()->getId() : null;

        $form = $this->createForm(UserType::class, $entity, array(
            'action' => $this->generateUrl('backend_admin_user_create'),
            'method' => 'POST',
            'role' => $this->role,
            'userID' => $this->userLogged->getId(),
            //'business' => $this->userLogged->getBusiness()->getId(),
            'business' => $business
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

        $business = $entity->getBusiness() != null ? $entity->getBusiness()->getId() : null;
        $form = $this->createForm(UserType::class, $entity, array(
            'action' => $this->generateUrl('backend_admin_user_update', array('id' => $entity->getId())),
            'role' => $this->role,
            'userID' => $this->userLogged->getId(),
            //'business' => $this->userLogged->getBusiness()->getId(),
            'business' => $business
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


            //print "<pre>";
            //var_dump($_REQUEST["complex"]);die;
            //COMPLEX ASSIGNMENT
            if(isset($_REQUEST["complex"])){

                $this->em->getRepository('BackendAdminBundle:UserComplex')->cleanUserComplex($entity->getId());

                foreach ($_REQUEST["complex"] as $key => $cValue){

                    $userComplex = new UserComplex();
                    $userComplex->setUser($entity);
                    $objComplex = $this->em->getRepository('BackendAdminBundle:Complex')->find($key);
                    $userComplex->setComplex($objComplex);
                    $userComplex->setEnabled(1);

                    $this->get("services")->blameOnMe($userComplex, "create");
                    $this->em->persist($userComplex);

                }
                $this->em->flush();
            }




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
