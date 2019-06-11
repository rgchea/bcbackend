<?php

namespace Backend\AdminBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Translation\TranslatorInterface;
//use Symfony\Component\Form\Extension\Core\Type\SubmitType;


use Backend\AdminBundle\Entity\Poll;
use Backend\AdminBundle\Entity\ComplexPoll;
use Backend\AdminBundle\Form\PollType;
use Backend\AdminBundle\Entity\PollQuestion;
use Backend\AdminBundle\Entity\PollQuestionOption;

/**
 * Poll controller.
 *
 */
class PollController extends Controller
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
        $this->repository = $this->em->getRepository('BackendAdminBundle:Poll');
        $this->translator = $this->get('translator');
        $this->renderer = $this->get('templating');
        $this->userLogged = $this->session->get('userLogged');
        $this->role = $this->session->get('userLogged')->getRole()->getName();


    }


    public function indexAction(Request $request)
    {
        $this->get("services")->setVars('poll');
        $this->initialise();
        //var_dump($this->translator->trans('label_welcome'));


        //print $this->translator->getLocale();die;

        return $this->render('BackendAdminBundle:Poll:index.html.twig');


    }


    public function listDatatablesAction(Request $request)
    {

        $this->get("services")->setVars('poll');
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

        $results = $this->repository->getRequiredDTData($start, $length, $orders, $search, $columns);
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
                    case 'enabled':
                        {
                            $responseTemp = $entity->getEnabled() ? $this->translator->trans('label_yes') : $this->translator->trans('label_no');
                            break;
                        }


                    case 'actions':
                        {
                            $urlEdit = $this->generateUrl('backend_admin_poll_edit', array('id' => $entity->getId()));
                            $edit = "<a href='".$urlEdit."'><i class='fa fa-pencil-square-o'></i><span class='item-label'></span></a>&nbsp;&nbsp;";

                            $urlDelete = $this->generateUrl('backend_admin_poll_delete', array('id' => $entity->getId()));
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
     * Creates a new Poll entity.
     *
     */
    public function newAction(Request $request)
    {
        $this->get("services")->setVars('poll');
        $this->initialise();

        $entity = new Poll();
        $form   = $this->createCreateForm($entity);

        $businessID = $this->userLogged->getBusiness()->getId();
        $arrComplex = $this->em->getRepository('BackendAdminBundle:Complex')->findBy(array("business" => $businessID), array("name" => "ASC"));


        return $this->render('BackendAdminBundle:Poll:new.html.twig', array(
            'entity' => $entity,
            'form' => $form->createView(),
            'arrComplex' => $arrComplex

        ));
    }



    /**
     * Finds and displays a Poll entity.
     *
     */
    public function showAction($entity)
    {
        $deleteForm = $this->createDeleteForm($entity);

        return $this->render('backend_admin_poll/show.html.twig', array(
            'entity' => $entity,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing Poll entity.
     *
     */
    public function editAction(Request $request, $id)
    {
        $this->get("services")->setVars('poll');
        $this->initialise();


        $entity = $this->em->getRepository('BackendAdminBundle:Poll')->find($id);

        $deleteForm = $this->createDeleteForm($entity);
        $editForm = $this->createEditForm($entity);

        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {

            $this->em->persist($entity);
            $this->em->flush();

            return $this->redirectToRoute('backend_admin_poll_edit', array('id' => $id));
        }


        $arrComplexReturn = array();
        $userRole = $this->role;

        //if($userRole != "ADMIN"){//$userRole != "SUPER ADMIN" ||
            $businessID = $entity->getCreatedBy()->getBusiness()->getId();

            $arrComplex = $this->em->getRepository('BackendAdminBundle:Complex')->findBy(array("business" => $businessID), array("name" => "ASC"));
            $assignedComplex = $this->em->getRepository('BackendAdminBundle:ComplexPoll')->getComplexPoll($entity->getId());
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

        //}


        return $this->render('BackendAdminBundle:Poll:edit.html.twig', array(
            'entity' => $entity,
            'form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
            'arrComplex' => $arrComplexReturn,
            'edit' => true
        ));
    }

    /**
     * Deletes a Poll entity.
     *
     */
    public function deleteAction(Request $request, $id)
    {

        $this->get("services")->setVars('poll');
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('BackendAdminBundle:Poll')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Poll entity.');
        }
        else{

            //SOFT DELETE
            $entity->setEnabled(0);
            $this->get("services")->blameOnMe($entity);
            $em->persist($entity);
            $em->flush();

        }



        $this->get('services')->flashSuccess($request);
        return $this->redirectToRoute('backend_admin_poll_index');
    }

    /**
     * Creates a form to delete a Poll entity.
     *
     * @param Poll $ticketType The Poll entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($entity)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('backend_admin_poll_delete', array('id' => $entity->getId())))
            ->setMethod('DELETE')
            ->getForm()
            ;
    }




    /**
     * Creates a new Poll entity.
     *
     */
    public function createAction(Request $request)
    {
        //print "<pre>";
        //var_dump($_REQUEST);DIE;
        $this->get("services")->setVars('poll');
        $this->initialise();


        $entity = new Poll();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);
        /*print "<pre>";
        var_dump($form->getErrorsAsString());die;
         * */

        if ($form->isValid()) {
            $myRequest = $request->request->get('poll');
            //var_dump($myRequest);die;
            $em = $this->getDoctrine()->getManager();
            //var_dump($request->get('poll');die;

            $this->get("services")->blameOnMe($entity, "create");

            $em->persist($entity);
            $em->flush();

            //COMPLEX ASSIGNMENT
            if(isset($_REQUEST["complex"])){

                foreach ($_REQUEST["complex"] as $key => $cValue){

                    $complexPoll = new ComplexPoll();
                    $complexPoll->setPoll($entity);
                    $objComplex = $this->em->getRepository('BackendAdminBundle:Complex')->find($key);
                    $complexPoll->setComplex($objComplex);

                    $this->get("services")->blameOnMe($complexPoll, "create");
                    $this->em->persist($complexPoll);

                }
            }
            $this->em->flush();


            $this->get('services')->flashSuccess($request);
            return $this->redirect($this->generateUrl('backend_admin_poll_index'));

        }
        /*
        else{
            print "FORMULARIO NO VALIDO";DIE;
        }
         * */

        return $this->render('BackendAdminBundle:Poll:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a form to create a Poll entity.
     *
     * @param Poll $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm($entity)
    {
        $this->get("services")->setVars('poll');
        $this->initialise();


        //$this->setVars();
        $form = $this->createForm(PollType::class, $entity, array(
            'action' => $this->generateUrl('backend_admin_poll_create'),
            'method' => 'POST'
        ));


        return $form;
    }




    /**
     * Creates a form to edit a Poll entity.
     *
     * @param Poll $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm($entity)
    {
        $this->get("services")->setVars('poll');
        $this->initialise();

        //$this->setVars();
        $form = $this->createForm(PollType::class, $entity, array(
            'action' => $this->generateUrl('backend_admin_poll_update', array('id' => $entity->getId())),
            //'method' => 'PUT',
            //'client' => $this->userLogged,
        ));


        return $form;
    }


    /**
     * Edits an existing Poll entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $this->get("services")->setVars('poll');
        $this->initialise();

        $entity = $this->em->getRepository('BackendAdminBundle:Poll')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Poll entity.');
        }

        $deleteForm = $this->createDeleteForm($entity);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $myRequest = $request->request->get('poll');

            $this->get("services")->blameOnMe($entity);
            $this->em->flush();

            //COMPLEX ASSIGNMENT
            if(isset($_REQUEST["complex"])){

                $this->em->getRepository('BackendAdminBundle:ComplexPoll')->cleanComplexPoll($entity->getId());

                foreach ($_REQUEST["complex"] as $key => $cValue){

                    $complexPoll = new ComplexPoll();
                    $complexPoll->setPoll($entity);
                    $objComplex = $this->em->getRepository('BackendAdminBundle:Complex')->find($key);
                    $complexPoll->setComplex($objComplex);

                    $this->get("services")->blameOnMe($complexPoll, "create");
                    $this->em->persist($complexPoll);

                }
                $this->em->flush();
            }



            $this->get('services')->flashSuccess($request);
            return $this->redirect($this->generateUrl('backend_admin_poll_index', array('id' => $id)));

        }

        return $this->render('BackendAdminBundle:Poll:edit.html.twig', array(
            'entity'      => $entity,
            'form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    public function questionAction(Request $request, $id){

        $this->get("services")->setVars('poll');
        $this->initialise();

        $entity = $this->em->getRepository('BackendAdminBundle:Poll')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Poll entity.');
        }

        $pollQuestions = $this->em->getRepository('BackendAdminBundle:PollQuestionOption')->getPollQuestions($id);

        $hasBeenAnswered = 0;
        foreach ($pollQuestions as $q){
            if($q["hasanswer"]){
                $hasBeenAnswered = 1;
            }
        }
        //print "<pre>";
        //var_dump($pollQuestions);die;

        $questionTypes = $this->em->getRepository('BackendAdminBundle:PollQuestionType')->findAll();
        $myLocale =  $this->translator->getLocale();

        if($hasBeenAnswered == 1){
            $this->get('services')->flashCustom($request, $this->translator->trans('label_poll_question_save_warning'));
        }

        //


        return $this->render('BackendAdminBundle:Poll:question.html.twig', array(
            'entity'      => $entity,
            'pollQuestions' => $pollQuestions,
            'myLocale' => $myLocale,
            'questionTypes' => $questionTypes,
            'hasBeenAnswered' => $hasBeenAnswered
        ));

    }


    public function questionSaveAction(Request $request, $id){

        /*
        print "<pre>";
        var_dump($_REQUEST);die;
        var_dump($_FILES);die;
        */

        $this->get("services")->setVars('poll');
        $this->initialise();

        $entity = $this->em->getRepository('BackendAdminBundle:Poll')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Poll entity.');
        }

        if(isset($_REQUEST["question"])){

            $arrQuestions = $_REQUEST["question"];


            ///CLEAR PREVIOUS QUESTIONS
            $this->em->getRepository('BackendAdminBundle:PollQuestion')->clearQuestions($id);

            foreach ($arrQuestions as $key => $question) {

                $objQuestion = new PollQuestion();
                $objQuestion->setPoll($entity);

                $qType = intval($question["selectType"]);
                $questionType = $this->em->getRepository('BackendAdminBundle:PollQuestionType')->find($qType);
                $objQuestion->setPollQuestionType($questionType);
                $objQuestion->setQuestion(trim($question["question"]));
                $objQuestion->setEnabled(1);


                //PHOTO UPLOAD
                //print "<pre>";
                //var_dump($myFile = $request->files->get("question"));die;
                $myFile = $request->files->get("question")[$key]["photo"];
                //var_dump($myFile);die;

                //if is edit and has previous file
                if(isset($question["hasphoto"])){
                    $objQuestion->setPollFilePhoto($question["hasphoto"]);
                }

                if($myFile != NULL){
                    if(intval($question["addphoto"]) == 1){
                        $file = $objQuestion->getPollFilePhoto();
                        $fileName = md5(uniqid()).'.'.$myFile->guessExtension();
                        $myFile->move($this->getParameter('polls_directory'), $fileName);
                        $objQuestion->setPollFilePhoto($objQuestion->getPhotoUploadDir().$fileName);
                    }
                }



                $this->get("services")->blameOnMe($objQuestion, "create");
                $this->get("services")->blameOnMe($objQuestion, "update");
                $this->em->persist($objQuestion);

                /*
                1 open
                2 multiple option
                3 select one option
                4 rating
                 */
                if($qType == 2 || $qType == 3){

                    if($qType == 2){
                        $arrTMP = $question["multiple"];
                    }
                    elseif($qType == 3){
                        $arrTMP = $question["selectone"];
                    }

                    foreach ($arrTMP as $key => $value){

                        $objOption = new PollQuestionOption();
                        $objOption->setPollQuestion($objQuestion);
                        $objOption->setQuestionOption(trim($value));
                        $this->get("services")->blameOnMe($objOption, "create");
                        $this->get("services")->blameOnMe($objOption, "update");
                        $this->em->persist($objOption);
                    }
                }

            }

            $this->em->flush();

        }


        return $this->redirectToRoute('backend_admin_poll_edit', array('id' => $id));

    }


    public function addQuestionAction(Request $request){

        $this->get("services")->setVars('poll');
        $this->initialise();

        $countQuestion = $_REQUEST["countQuestion"];

        $questionTypes = $this->em->getRepository('BackendAdminBundle:PollQuestionType')->findAll();
        $myLocale =  $this->translator->getLocale();


        return $this->render('BackendAdminBundle:Poll:addQuestion.html.twig', array(
            'countQuestion'      => $countQuestion,
            'myLocale' => $myLocale,
            'questionTypes' => $questionTypes

        ));

    }


}

