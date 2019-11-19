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

                    case 'duedate':
                    {

                        if($entity->getDueDate() != NULL){
                            $responseTemp = $entity->getDueDate()->format("m/d/y");
                        }
                        else{
                            $responseTemp = "--";
                        }

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
                            $edit = "<a title='".$this->translator->trans("tooltip.edit")."' href='".$urlEdit."'><i class='fa fa-pencil-square-o'></i><span class='item-label'></span></a>&nbsp;&nbsp;";


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
     * Creates a new PropertyContractTransaction entity.
     *
     */
    public function newAction(Request $request)
    {
        $this->get("services")->setVars('propertyContractTransaction');
        $this->initialise();

        $entity = new PropertyContractTransaction();
        //$form   = $this->createCreateForm($entity);

        $myComplexID = $this->get("services")->getSessionComplex();

        $paymentType = $this->em->getRepository("BackendAdminBundle:PropertyTransactionType")->findBy(array("enabled" => 1), array("nameEN" => "ASC"));
        $complexSector = $this->em->getRepository("BackendAdminBundle:ComplexSector")->findBy(array('complex'=> $myComplexID, 'enabled' => 1));



        return $this->render('BackendAdminBundle:PropertyContractTransaction:new.html.twig', array(
            'entity' => $entity,
            //'form' => $form->createView(),
            'myPath' => 'backend_admin_propertycontracttransaction_new',
            'new' => true,
            'complexSector' => $complexSector,
            'paymentType' => $paymentType

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

        if(!$entity){
            throw $this->createNotFoundException('Not found.');
        }

        //users cannot view private complexes
        $this->get('services')->checkComplexAccess($entity->getPropertyContract()->getProperty()->getComplex()->getId());


        return $this->render('BackendAdminBundle:PropertyContractTransaction:edit.html.twig', array(
            'entity' => $entity,
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
        $this->initialise();

        //print "<pre>";
        //var_dump($_REQUEST);die;
        $objProperty = $this->em->getRepository('BackendAdminBundle:Property')->find(intval($_REQUEST["property_select"]));

        $propertyContract = $this->em->getRepository('BackendAdminBundle:PropertyContract')->findOneBy(array("property" => $objProperty->getId(), 'propertyTransactionType' => 3, "enabled" => 1, 'isActive' => 1), array("id"=> "DESC"));
        $transactionType = $this->em->getRepository('BackendAdminBundle:PropertyTransactionType')->find(intval($_REQUEST["type_select"]));//reservacion

        $payment = new PropertyContractTransaction();
        $payment->setEnabled(1);
        $complex = $objProperty->getComplex();
        $payment->setComplex($complex);
        $payment->setPropertyContract($propertyContract);
        $payment->setPropertyTransactionType($transactionType);
        //$payment->setCommonAreaReservation($entity);
        $payment->setDescription(trim($_REQUEST["description"]));
        $payment->setComment(trim($_REQUEST["comment"]));

        $payment->setPaymentAmount(floatval($_REQUEST["amount"]));
        $payment->setDueDate(new \Datetime($_REQUEST["due_date"]));
        //status
        $payment->setStatus(0);

        //BLAME ME
        $this->get("services")->blameOnMe($payment, "create");
        $this->get("services")->blameOnMe($payment, "update");

        $this->em->persist($payment);

        $this->em->flush();

        //ADD POINTS
        $message = $this->translator->trans("label_new"). " ". $this->translator->trans("label_payment"). " ". $payment->getId();
        $playKey = "BC-A-00004";//Register payment
        $this->get("services")->addPointsAdmin($complex, $message, $playKey);


        $this->get('services')->flashSuccess($request);
        return $this->redirect($this->generateUrl('backend_admin_propertycontracttransaction_index'));


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
        $this->initialise();

        //print "<pre>";
        //var_dump($_REQUEST);DIE;

        $payment = $this->em->getRepository('BackendAdminBundle:PropertyContractTransaction')->find($id);

        if (!$payment) {
            throw $this->createNotFoundException('Unable to find PropertyContractTransaction entity.');
        }


        ///paid & paid date
        $mainTenant = $payment->getPropertyContract()->getProperty()->getMainTenant();
        $payment->setPaidBy($mainTenant);
        $gtmNow = gmdate("Y-m-d H:i:s");
        $payment->setPaidDate(new \DateTime($gtmNow));
        //status
        $payment->setStatus(1);
        $payment->setTransactionNumber(trim($_REQUEST["transaction_number"]));

        //BLAME ME
        $this->get("services")->blameOnMe($payment, "update");

        $this->em->persist($payment);

        $this->em->flush();

        $title = $this->translator->trans("push.payment_received");
        $description = $this->translator->trans("push.payment").trim($_REQUEST["transaction_number"]);
        $this->get("services")->sendPushNotification($mainTenant, $title, $description);

        $this->get('services')->flashSuccess($request);
        return $this->redirect($this->generateUrl('backend_admin_propertycontracttransaction_index', array('id' => $id)));

    }


}
