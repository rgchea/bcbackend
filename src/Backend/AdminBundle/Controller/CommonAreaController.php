<?php

namespace Backend\AdminBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Translation\TranslatorInterface;


use Backend\AdminBundle\Entity\CommonArea;
use Backend\AdminBundle\Entity\CommonAreaAvailability;
use Backend\AdminBundle\Entity\CommonAreaPhoto;
use Backend\AdminBundle\Form\CommonAreaType;

/**
 * CommonArea controller.
 *
 */
class CommonAreaController extends Controller
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
        $this->repository = $this->em->getRepository('BackendAdminBundle:CommonArea');
        $this->translator = $this->get('translator');
        $this->renderer = $this->get('templating');
        $this->userLogged = $this->session->get('userLogged');
        $this->role = $this->session->get('userLogged')->getRole()->getName();


    }


    public function indexAction(Request $request)
    {


        //var_dump($this->translator->trans('label_welcome'));
        $this->get("services")->setVars('commonArea');
        $this->initialise();

        if($this->role != "SUPER ADMIN"){
            if(count($this->session->get("myComplexes")) == 0){
                return $this->redirectToRoute('backend_admin_complex_new');
                //throw $this->createAccessDeniedException($this->translator->trans('label_access_denied'));
            }

        }

        //print $this->translator->getLocale();die;

        return $this->render('BackendAdminBundle:CommonArea:index.html.twig');


    }


    public function listDatatablesAction(Request $request)
    {

        $this->get("services")->setVars('commonArea');

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
                            $urlEdit = $this->generateUrl('backend_admin_common_area_edit', array('id' => $entity->getId()));
                            $edit = "<a href='".$urlEdit."'><i class='fa fa-pencil-square-o'></i><span class='item-label'></span></a>&nbsp;&nbsp;";

                            $urlDelete = $this->generateUrl('backend_admin_common_area_delete', array('id' => $entity->getId()));
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
     * Creates a new CommonArea entity.
     *
     */
    public function newAction(Request $request)
    {


        $this->get("services")->setVars('commonArea');
        $this->initialise();

        $entity = new CommonArea();
        $form   = $this->createCreateForm($entity);

        return $this->render('BackendAdminBundle:CommonArea:new.html.twig', array(
            'entity' => $entity,
            'form' => $form->createView(),
            'token' => md5(uniqid())



        ));
    }



    /**
     * Finds and displays a CommonArea entity.
     *
     */
    public function showAction($entity)
    {
        $deleteForm = $this->createDeleteForm($entity);

        return $this->render('backend_admin_common_area/show.html.twig', array(
            'entity' => $entity,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing CommonArea entity.
     *
     */
    public function editAction(Request $request, $id)
    {
        $this->get("services")->setVars('commonArea');
        $this->initialise();

        $entity = $this->em->getRepository('BackendAdminBundle:CommonArea')->find($id);
        if(!$entity){
            throw $this->createNotFoundException('Not found.');
        }

        //users cannot view private complexes
        $this->get('services')->checkComplexAccess($entity->getComplex()->getId());


        $deleteForm = $this->createDeleteForm($entity);
        $editForm = $this->createEditForm($entity);

        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->em = $this->getDoctrine()->getManager();
            $this->em->persist($entity);
            $this->em->flush();

            return $this->redirectToRoute('backend_admin_common_area_edit', array('id' => $id));
        }

        $availability = $this->em->getRepository('BackendAdminBundle:CommonAreaAvailability')->getSchedule($entity->getId());


        return $this->render('BackendAdminBundle:CommonArea:edit.html.twig', array(
            'entity' => $entity,
            'form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
            'edit' => $id,
            'availability' => $availability,
            'token' => $entity->getToken()
        ));
    }

    /**
     * Deletes a CommonArea entity.
     *
     */
    public function deleteAction(Request $request, $id)
    {

        $this->get("services")->setVars('commonArea');
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('BackendAdminBundle:CommonArea')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find CommonArea entity.');
        }
        else{

            //SOFT DELETE
            $entity->setEnabled(0);
            $this->get("services")->blameOnMe($entity);
            $em->persist($entity);
            $em->flush();

        }



        $this->get('services')->flashSuccess($request);
        return $this->redirectToRoute('backend_admin_common_area_index');
    }

    /**
     * Creates a form to delete a CommonArea entity.
     *
     * @param CommonArea
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($entity)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('backend_admin_common_area_delete', array('id' => $entity->getId())))
            ->setMethod('DELETE')
            ->getForm()
            ;
    }




    /**
     * Creates a new CommonArea entity.
     *
     */
    public function createAction(Request $request)
    {

        $this->get("services")->setVars('commonArea');
        $this->initialise();


        $entity = new CommonArea();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);
        /*print "<pre>";
        var_dump($form->getErrorsAsString());die;
         * */

        if ($form->isValid()) {


            $entity->setComplex($this->em->getRepository('BackendAdminBundle:Complex')->find($_REQUEST["common_area"]["complex"]));
            $token = trim($_REQUEST["common_area"]["token"]);
            $entity->setToken($token);
            $this->get("services")->blameOnMe($entity, "create");


            $this->em->persist($entity);
            $this->em->flush();


            ///get all photos by token and update the commonArea
            $photos = $this->em->getRepository('BackendAdminBundle:CommonAreaPhoto')->findByToken($token);
            foreach ($photos as $photo){
                $photo->setCommonArea($entity);
                $this->em->persist($photo);
            }
            $this->em->flush();

            /*SET THE WEEK SCHEDULE*/
            $mySchedule = json_decode($_REQUEST["my_schedule"], true);

            if(is_array($mySchedule)) {
                if(count($mySchedule) > 0){

                    foreach ($mySchedule as $key => $weekDay){

                        $day =  intval($weekDay["day"]);
                        $arrPeriods = $weekDay["periods"];

                        foreach ($arrPeriods as $pk => $period){
                            $start = $period["start"];
                            $end = $period["end"];

                            $newAvailability = new CommonAreaAvailability();
                            $newAvailability->setCommonArea($entity);
                            $newAvailability->setWeekdaySingle($day);
                            $newAvailability->setHourFrom($start);
                            $newAvailability->setHourTo($end);

                            $this->get("services")->blameOnMe($newAvailability, "create");

                            $this->em->persist($newAvailability);
                            $this->em->flush();

                        }

                    }


                }
            }





            $this->get('services')->flashSuccess($request);
            //return $this->redirect($this->generateUrl('backend_admin_common_area_index'));
            return $this->redirectToRoute('backend_admin_common_area_edit', array('id' => $entity->getId()));

        }

        else{
            print "FORMULARIO NO VALIDO";DIE;
        }


        return $this->render('BackendAdminBundle:CommonArea:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a form to create a CommonArea entity.
     *
     * @param CommonArea $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm($entity)
    {
        $this->get("services")->setVars('commonArea');
        $this->initialise();
        $form = $this->createForm(CommonAreaType::class, $entity, array(
            'action' => $this->generateUrl('backend_admin_common_area_create'),
            'method' => 'POST',
            'role' => $this->role,
            'userID' => $this->userLogged->getId(),
            'repository' => $this->em->getRepository('BackendAdminBundle:Complex'),

        ));


        return $form;
    }




    /**
     * Creates a form to edit a CommonArea entity.
     *
     * @param CommonArea $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm($entity)
    {
        //print "entra";die;
        $this->get("services")->setVars('commonArea');
        $this->initialise();

        //var_dump($this->em->getRepository('BackendAdminBundle:Complex'));die;
        /*
        $form = $this->createForm(CommonAreaType::class, $entity, array(
            'action' => $this->generateUrl('backend_admin_commonArea_update',
                array('id' => $entity->getId(),
                    'role' => $this->role,
                    'userID' => $this->userLogged->getId(),
                    'repository' => $this->em->getRepository('BackendAdminBundle:Complex')
                )),
        ));
        */

        $form = $this->createForm(CommonAreaType::class, $entity, array(
            'action' => $this->generateUrl('backend_admin_common_area_update', array('id' => $entity->getId())),
            'role' => $this->role,
            'userID' => $this->userLogged->getId(),
            'repository' => $this->em->getRepository('BackendAdminBundle:Complex'),
        ));



        return $form;
    }


    /**
     * Edits an existing CommonArea entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        /*
        print "<pre>";
        var_dump(json_decode($_REQUEST["my_schedule"], true));
        die;
        */

        $this->get("services")->setVars('commonArea');
        $this->initialise();

        $entity = $this->em->getRepository('BackendAdminBundle:CommonArea')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find CommonArea entity.');
        }

        $deleteForm = $this->createDeleteForm($entity);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {

            $entity->setComplex($this->em->getRepository('BackendAdminBundle:Complex')->find($_REQUEST["common_area"]["complex"]));

            $this->get("services")->blameOnMe($entity, "update");
            $this->em->flush();

            ///get all photos by token and update the commonArea
            $photos = $this->em->getRepository('BackendAdminBundle:CommonAreaPhoto')->findByToken($entity->getToken());
            foreach ($photos as $photo){
                $photo->setCommonArea($entity);
                $this->em->persist($photo);
            }
            $this->em->flush();


            /*SET THE WEEK SCHEDULE*/

            /*ERASE LAST SCHEDULE*/
            ///
            $this->em->getRepository('BackendAdminBundle:CommonAreaAvailability')->clearSchedule($entity->getId());

            $mySchedule = json_decode($_REQUEST["my_schedule"], true);

            if(is_array($mySchedule)){
                if(count($mySchedule) > 0){

                    foreach ($mySchedule as $key => $weekDay){

                        $day =  intval($weekDay["day"]);
                        $arrPeriods = $weekDay["periods"];

                        foreach ($arrPeriods as $pk => $period){
                            $start = $period["start"];
                            $end = $period["end"];

                            $newAvailability = new CommonAreaAvailability();
                            $newAvailability->setCommonArea($entity);
                            $newAvailability->setWeekdaySingle($day);
                            $newAvailability->setHourFrom($start);
                            $newAvailability->setHourTo($end);

                            $this->get("services")->blameOnMe($newAvailability, "create");
                            $this->get("services")->blameOnMe($newAvailability, "update");

                            $this->em->persist($newAvailability);
                            $this->em->flush();

                        }

                    }

                }
            }






            $this->get('services')->flashSuccess($request);
            return $this->redirect($this->generateUrl('backend_admin_common_area_index', array('id' => $id)));

        }

        //$countries = $this->em->getRepository('BackendAdminBundle:GeoCountry')->findBy(array("enabled" => 1));

        return $this->render('BackendAdminBundle:CommonArea:edit.html.twig', array(
            'entity'      => $entity,
            'form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
            //'countries' => $countries
        ));
    }


    public function imageSendAction(Request $request, $token){

        //var_dump($_REQUEST);die;//

        $this->get("services")->setVars('commonArea');
        $this->initialise();


        //AVATAR UPLOAD
        /*
        if($myFile != NULL){

            $file = $entity->getAvatarPath();
            $fileName = md5(uniqid()).'.'.$file->guessExtension();
            $file->move($this->getParameter('avatars_directory'), $fileName);
            $entity->setAvatarPath($entity->getAvatarUploadDir().$fileName);

        }
        */


        //$commonAreaID = trim($_REQUEST["common_area"]);//TOKEN
        //$objCommonArea = $this->em->getRepository('BackendAdminBundle:CommonArea')->find($commonAreaID);

        $document = new CommonAreaPhoto();
        $media = $request->files->get('file');

        $fileName = md5(uniqid()).'.'.$media->guessExtension();

        $document->setFile($media);
        $document->setPhotoPath($fileName);
        //$document->setName($media->getClientOriginalName());
        //$document->setCommonArea($objCommonArea);
        $document->setToken($token);
        $document->upload($fileName);

        $this->get("services")->blameOnMe($document, "create");
        $this->get("services")->blameOnMe($document, "update");

        $this->em->persist($document);
        $this->em->flush();

        //infos sur le document envoyé
        //var_dump($request->files->get('file'));die;
        return new JsonResponse(array('success' => $document->getId()));

    }



    public function imageGetAction(Request $request){



        $this->get("services")->setVars('commonArea');
        $this->initialise();



        $commonAreaID = intval($_REQUEST["common_area"]);
        $images = $this->em->getRepository('BackendAdminBundle:CommonAreaPhoto')->findByCommonArea($commonAreaID);

        $result  = array();
        $storeFolder = __DIR__.'/../../../../web/uploads/images/common_area/';

        $files = scandir($storeFolder);                 //1

        //var_dump($files);die;

        if ( false!==$files ) {
            foreach ( $images as $file ) {

                $obj['id'] = $file->getId();
                $obj['name'] = $file->getPhotoPath();
                $obj['size'] = 0;
                $result[] = $obj;
            }
        }

        header('Content-type: text/json');              //3
        header('Content-type: application/json');
        echo json_encode($result);die;

    }




    public function imageRemoveAction(Request $request){


        $this->get("services")->setVars('commonArea');
        $this->initialise();
        if(isset($_REQUEST["id"])){

            $img = $this->em->getRepository('BackendAdminBundle:CommonAreaPhoto')->find(intval($_REQUEST["id"]));
            if($img){
                $imgName =  $img->getPhotoPath();
                $this->em->remove($img);
                $this->em->flush();

                $storeFolder = __DIR__.'/../../../../web/uploads/images/common_area/';

                unlink($storeFolder.$imgName);

            }
            else{

            }

        }


        return new JsonResponse(array('success' => true));

    }



}

