<?php

namespace Backend\AdminBundle\Controller;

use Backend\AdminBundle\Entity\Role;
use Backend\AdminBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\Session\Session;

use Symfony\Component\HttpFoundation\JsonResponse;





class UserController extends Controller
{


    protected $entityManager;
    protected $translator;
    protected $repository;

    // Set up all necessary variable
    protected function initialise()
    {
        $this->entityManager = $this->getDoctrine()->getManager();
        $this->repository = $this->entityManager->getRepository('BackendAdminBundle:User');
        $this->translator = $this->get('translator');
    }


    public function indexAction(Request $request)
    {

        $this->get("services")->setVars('user');

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


            $arrDate["start"] = $request->request->get('start_date');
            $arrDate["end"] = $request->request->get('end_date');
        }
        else // If the request is not a POST one, die hard
            die;

        // Process Parameters

        // Orders
        foreach ($orders as $key => $order)
        {
            // Orders does not contain the name of the column, but its number,
            // so add the name so we can handle it just like the $columns array
            $orders[$key]['name'] = $columns[$order['column']]['name'];
        }

        // Further filtering can be done in the Repository by passing necessary arguments
        if(trim($arrDate["start"]) != "" && trim($arrDate["end"]) != ""){
            $otherConditions = $arrDate;
        }
        else{
            $otherConditions = null;
        }


        // Get results from the Repository
        //$results = $this->repository->getRequiredDTData($start, $length, $orders, $search, $columns, $otherConditions = null);
        $results = $this->repository->getRequiredDTData($start, $length, $orders, $search, $columns, $otherConditions);

        // Returned objects are of type Town
        $objects = $results["results"];
        // Get total number of objects
        $total_objects_count = $this->repository->count(array());
        // Get total number of results
        $selected_objects_count = count($objects);
        // Get total number of filtered data
        $filtered_objects_count = $results["countResult"];

        // Construct response
        $response = '{
            "draw": '.$draw.',
            "recordsTotal": '.$total_objects_count.',
            "recordsFiltered": '.$filtered_objects_count.',
            "data": [';

        $i = 0;

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
                    case 'username':
                        {
                            $responseTemp = $entity->getUsername();

                            // Do this kind of treatments if you suspect that the string is not JS compatible
                            $responseTemp = htmlentities(str_replace(array("\r\n", "\n", "\r"), ' ', $responseTemp));

                            // View permission ?
                            /*
                            if ($this->get('security.authorization_checker')->isGranted('view_town', $entity))
                            {
                                // Get the ID
                                $id = $entity->getId();
                                // Construct the route
                                $url = $this->generateUrl('playground_town_view', array('id' => $id));
                                // Construct the html code to send back to datatables
                                $responseTemp = "<a href='".$url."' target='_self'>".$ref."</a>";
                            }
                            else
                            {
                                $responseTemp = $name;
                            }
                            */
                            break;
                        }

                    case 'role':
                        {
                            $role = $entity->getRole();
                            if ($role !== null)
                            {
                                $responseTemp = $role->getName();

                            }
                            break;
                        }

                    case 'created_at':
                        {
                            $responseTemp = $entity->getCreatedAt()->format('Y-m-d');
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

        $response .= ']}';

        // Send all this stuff back to DataTables
        $returnResponse = new JsonResponse();
        $returnResponse->setJson($response);

        return $returnResponse;

    }





}