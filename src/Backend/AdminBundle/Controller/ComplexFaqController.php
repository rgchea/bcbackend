<?php

namespace Backend\AdminBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Translation\TranslatorInterface;
//use Symfony\Component\Form\Extension\Core\Type\SubmitType;





use Backend\AdminBundle\Entity\ComplexFaq;
use Backend\AdminBundle\Form\ComplexFaqType;

/**
 * ComplexFaq controller.
 *
 */
class ComplexFaqController extends Controller
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
        $this->repository = $this->em->getRepository('BackendAdminBundle:ComplexFaq');
        $this->translator = $this->get('translator');
        $this->renderer = $this->get('templating');
        $this->userLogged = $this->session->get('userLogged');
        $this->role = $this->session->get('userLogged')->getRole()->getName();


    }


    public function indexAction(Request $request)
    {


        //var_dump($this->translator->trans('label_welcome'));
        $this->get("services")->setVars('complexFaq');
        $this->initialise();

        if($this->role != "SUPER ADMIN"){
            if(count($this->session->get("myComplexes")) == 0){
                return $this->redirectToRoute('backend_admin_complex_new');
                //throw $this->createAccessDeniedException($this->translator->trans('label_access_denied'));
            }
        }

        //print $this->translator->getLocale();die;

        return $this->render('BackendAdminBundle:ComplexFaq:index.html.twig');


    }


    public function listDatatablesAction(Request $request)
    {

        $this->get("services")->setVars('complexFaq');

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

        ///FILTER BY ROLE
        $filters = null;
        if($this->role != "SUPER ADMIN"){

            $arrComplex = $this->em->getRepository('BackendAdminBundle:Complex')->getComplexByUser($this->userLogged->getId());
            foreach ($arrComplex as $k =>$v) {
                $filters[$v] = $v;//the complex id
            }

        }

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
                            $responseTemp = $entity->getComplex()->getName();

                            break;
                        }
                    case 'actions':
                        {
                            $urlEdit = $this->generateUrl('backend_admin_complex_faq_edit', array('id' => $entity->getId()));
                            $edit = "<a href='".$urlEdit."'><i class='fa fa-pencil-square-o'></i><span class='item-label'></span></a>&nbsp;&nbsp;";

                            //$urlDelete = $this->generateUrl('backend_admin_complex_faq_delete', array('id' => $entity->getId()));
                            //$delete = "<a class='btn-delete' href='".$urlDelete."'><i class='fa fa-trash-o'></i><span class='item-label'></span></a>";

                            $responseTemp = $edit;
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
     * Creates a new ComplexFaq entity.
     *
     */
    public function newAction(Request $request)
    {
        $this->get("services")->setVars('complexFaq');
        $this->initialise();

        $entity = new ComplexFaq();
        $form   = $this->createCreateForm($entity);


        return $this->render('BackendAdminBundle:ComplexFaq:new.html.twig', array(
            'entity' => $entity,
            'form' => $form->createView(),
        ));
    }



    /**
     * Finds and displays a ComplexFaq entity.
     *
     */
    public function showAction($entity)
    {
        $deleteForm = $this->createDeleteForm($entity);

        return $this->render('backend_admin_complex_faq/show.html.twig', array(
            'entity' => $entity,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing ComplexFaq entity.
     *
     */
    public function editAction(Request $request, $id)
    {
        $this->get("services")->setVars('complexFaq');
        $this->initialise();

        $em = $this->getDoctrine()->getManager();

        $id = intval($id);
        $entity = $em->getRepository('BackendAdminBundle:ComplexFaq')->find($id);
        if(!$entity){
            throw $this->createNotFoundException('Not found.');
        }

        //users cannot view private complexes
        $this->get('services')->checkComplexAccess($entity->getComplex()->getId());


        $deleteForm = $this->createDeleteForm($entity);
        $editForm = $this->createEditForm($entity);

        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirectToRoute('backend_admin_complex_faq_edit', array('id' => $id));
        }

        return $this->render('BackendAdminBundle:ComplexFaq:edit.html.twig', array(
            'entity' => $entity,
            'form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a ComplexFaq entity.
     *
     */
    public function deleteAction(Request $request, $id)
    {

        $this->get("services")->setVars('complexFaq');
        $this->initialise();

        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('BackendAdminBundle:ComplexFaq')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find ComplexFaq entity.');
        }
        else{

            //SOFT DELETE
            $entity->setEnabled(0);
            $this->get("services")->blameOnMe($entity);
            $em->persist($entity);
            $em->flush();

        }


        $this->get('services')->flashSuccess($request);
        return $this->redirectToRoute('backend_admin_complex_faq_index');
    }

    /**
     * Creates a form to delete a ComplexFaq entity.
     *
     * @param ComplexFaq $complexFaq The ComplexFaq entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($entity)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('backend_admin_complex_faq_delete', array('id' => $entity->getId())))
            ->setMethod('DELETE')
            ->getForm()
            ;
    }




    /**
     * Creates a new ComplexFaq entity.
     *
     */
    public function createAction(Request $request)
    {

        $this->get("services")->setVars('complexFaq');
        $this->initialise();


        $entity = new ComplexFaq();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);
        /*print "<pre>";
        var_dump($form->getErrorsAsString());die;
         * */

        if ($form->isValid()) {
            $myRequest = $request->request->get('complexFaq');
            //var_dump($myRequest);die;
            $em = $this->getDoctrine()->getManager();
            //var_dump($request->get('complexFaq');die;


            $this->get("services")->blameOnMe($entity, "create");

            $em->persist($entity);
            $em->flush();


            $this->get('services')->flashSuccess($request);
            return $this->redirect($this->generateUrl('backend_admin_complex_faq_index'));

        }
        /*
        else{
            print "FORMULARIO NO VALIDO";DIE;
        }
         * */

        return $this->render('BackendAdminBundle:ComplexFaq:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a form to create a ComplexFaq entity.
     *
     * @param ComplexFaq $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm($entity)
    {
        $this->get("services")->setVars('complexFaq');
        $this->initialise();

        $form = $this->createForm(ComplexFaqType::class, $entity, array(
            'action' => $this->generateUrl('backend_admin_complex_faq_create'),
            'method' => 'POST',
            'role' => $this->role,
            'userID' => $this->userLogged->getId(),
            'repository' => $this->em->getRepository('BackendAdminBundle:Complex'),

        ));


        return $form;
    }




    /**
     * Creates a form to edit a ComplexFaq entity.
     *
     * @param ComplexFaq $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm($entity)
    {

        $this->get("services")->setVars('complexFaq');
        $this->initialise();


        $form = $this->createForm(ComplexFaqType::class, $entity, array(
            'action' => $this->generateUrl('backend_admin_complex_faq_update', array('id' => $entity->getId())),
            'role' => $this->role,
            'userID' => $this->userLogged->getId(),
            'repository' => $this->em->getRepository('BackendAdminBundle:Complex'),

        ));


        return $form;
    }


    /**
     * Edits an existing ComplexFaq entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $this->get("services")->setVars('complexFaq');
        $this->initialise();

        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('BackendAdminBundle:ComplexFaq')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find ComplexFaq entity.');
        }

        $deleteForm = $this->createDeleteForm($entity);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $myRequest = $request->request->get('complexFaq');

            $this->get("services")->blameOnMe($entity);
            $em->flush();

            $this->get('services')->flashSuccess($request);
            return $this->redirect($this->generateUrl('backend_admin_complex_faq_index', array('id' => $id)));

        }

        return $this->render('BackendAdminBundle:ComplexFaq:edit.html.twig', array(
            'entity'      => $entity,
            'form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }


}
