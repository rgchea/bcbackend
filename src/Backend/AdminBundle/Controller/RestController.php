<?php

namespace Backend\AdminBundle\Controller;

use Backend\AdminBundle\Entity\CommonArea;
use Backend\AdminBundle\Entity\CommonAreaAvailability;
use Backend\AdminBundle\Entity\CommonAreaPhoto;
use Backend\AdminBundle\Entity\CommonAreaReservation;
use Backend\AdminBundle\Entity\CommonAreaReservationStatus;
use Backend\AdminBundle\Entity\CommonAreaType;
use Backend\AdminBundle\Entity\ComplexSector;
use Backend\AdminBundle\Entity\GeoCountry;
use Backend\AdminBundle\Entity\NotificationType;
use Backend\AdminBundle\Entity\Poll;
use Backend\AdminBundle\Entity\PollQuestion;
use Backend\AdminBundle\Entity\PollQuestionOption;
use Backend\AdminBundle\Entity\Property;
use Backend\AdminBundle\Entity\PropertyType;
use Backend\AdminBundle\Entity\TermCondition;
use Backend\AdminBundle\Entity\Ticket;
use Backend\AdminBundle\Entity\TicketCategory;
use Backend\AdminBundle\Entity\TicketComment;
use Backend\AdminBundle\Entity\TicketStatus;
use Backend\AdminBundle\Entity\TicketType;
use Backend\AdminBundle\Entity\User;
use Backend\AdminBundle\Entity\UserNotification;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\FOSRestController;
use Swagger\Annotations as SWG;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;


//entities


/**
 * Class RestController
 *
 * @Route("/api")
 *
 */
class RestController extends FOSRestController
{
    protected $em;
    protected $translator;
    protected $serializer;

    // Set up all necessary variable
    protected function initialise()
    {
        $this->em = $this->getDoctrine()->getManager();
        $this->translator = $this->get('translator');
        $this->serializer = $this->get('jms_serializer');
    }


    /**
     * @Rest\Post("/register")
     *
     * @SWG\Response(
     *     response=201,
     *     description="User was successfully registered"
     * )
     *
     * @SWG\Response(
     *     response=500,
     *     description="User was not successfully registered"
     * )
     *
     * @SWG\Parameter(
     *     name="_name",
     *     in="body",
     *     type="string",
     *     description="The username",
     *     schema={}
     * )
     *
     * @SWG\Parameter(
     *     name="_email",
     *     in="body",
     *     type="string",
     *     description="The username",
     *     schema={}
     * )
     *
     * @SWG\Parameter(
     *     name="_username",
     *     in="body",
     *     type="string",
     *     description="The username",
     *     schema={}
     * )
     *
     * @SWG\Parameter(
     *     name="_password",
     *     in="query",
     *     type="string",
     *     description="The password"
     * )
     *
     * @SWG\Tag(name="User")
     */
    public function postRegisterAction(Request $request, UserPasswordEncoderInterface $encoder)
    {
        $serializer = $this->get('jms_serializer');
        $em = $this->getDoctrine()->getManager();

        $user = [];
        $message = "";

        try {
            $code = 200;
            $error = false;


            $email = $request->request->get('_email');
            $password = $request->request->get('_password');

            $user = new User();

            $user->setEmail($email);
            $user->setPlainPassword($password);
            $user->setPassword($encoder->encodePassword($user, $password));

            $em->persist($user);
            $em->flush();

        } catch (Exception $ex) {
            $code = 500;
            $error = true;
            $message = "An error has occurred trying to register the user - Error: {$ex->getMessage()}";
        }

        $response = [
            'code' => $code,
            'error' => $error,
            'data' => $code == 200 ? $user : $message,
        ];

        return new Response($serializer->serialize($response, "json"));
    }


    /**
     * @Rest\Get("/v1", name="")
     */
    public function getV1Action()
    {
        $this->initialise();
        $response = array("result" => $this->getUser()->getUsername());

        return new Response($this->serializer->serialize($response, "json"));

    }


    /**
     * @Rest\Get("/termsConditions", name="terms_and_conditions")
     *
     * @SWG\Parameter( name="app_version", in="query", type="string", description="The version of the app." )
     * @SWG\Parameter( name="code_version", in="query", type="string", description="The version of the code." )
     * @SWG\Parameter( name="language", in="query", type="string", description="The language being used (either en or es)." )
     * @SWG\Parameter( name="time_offset", in="query", type="string", description="Time difference with respect to GMT time." )
     *
     * @SWG\Response(
     *     response=200,
     *     description="Returns the Terms and Conditions in the requested language.",
     *     @SWG\Schema (
     *          @SWG\Property(
     *              property="data", type="string",
     *              example="<b>Terms and Conditions</b>"
     *          ),
     *          @SWG\Property( property="message", type="string", example="" )
     *      )
     * )
     *
     * @SWG\Response(
     *     response=500, description="Internal error.",
     *     @SWG\Schema (
     *          @SWG\Property(property="data", type="string", example="" ),
     *          @SWG\Property( property="message", type="string", example="Internal error." )
     *     )
     * )
     *
     * @SWG\Tag(name="Admin")
     */

    public function getTermsAndConditionsAction(Request $request)
    {
        try {
            $this->initialise();
            $lang = strtolower(trim($request->get('language')));

            /** @var TermCondition $terms */
            $terms = $this->em->getRepository('BackendAdminBundle:TermCondition')->findOneBy(array('enabled' => true), array('updatedAt' => 'DESC'));

            return new JsonResponse(array(
                'message' => "",
                'data' => ($lang == 'en') ? htmlspecialchars_decode($terms->getDescriptionEN()) : htmlspecialchars_decode($terms->getDescriptionES())
            ));
        } catch (Exception $ex) {
            return new JsonResponse(array('message' => $ex->getMessage()), JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }


    /**
     * @Rest\Get("/countries", name="countries")
     *
     * @SWG\Parameter( name="app_version", in="query", type="string", description="The version of the app." )
     * @SWG\Parameter( name="code_version", in="query", type="string", description="The version of the code." )
     * @SWG\Parameter( name="language", in="query", type="string", description="The language being used (either en or es)." )
     * @SWG\Parameter( name="time_offset", in="query", type="string", description="Time difference with respect to GMT time." )
     *
     * @SWG\Response(
     *     response=200,
     *     description="Returns the list of countries.",
     *     @SWG\Schema (
     *          @SWG\Property(
     *              property="data", type="array",
     *              @SWG\Items(
     *                  @SWG\Property( property="name", type="string", description="Name", example="Guatemala" ),
     *                  @SWG\Property( property="code", type="string", description="Area code", example="502" ),
     *                  @SWG\Property( property="locale", type="string", description="Language", example="es" )
     *              )
     *          ),
     *          @SWG\Property( property="message", type="string", example="" )
     *      )
     * )
     *
     * @SWG\Response(
     *     response=500, description="Internal error.",
     *     @SWG\Schema (
     *          @SWG\Property(property="data", type="string", example="" ),
     *          @SWG\Property( property="message", type="string", example="Internal error." )
     *     )
     * )
     *
     * @SWG\Tag(name="Admin")
     */

    public function getCountriesAction()
    {
        try {
            $this->initialise();
            $data = array();

            $countries = $this->em->getRepository('BackendAdminBundle:GeoCountry')->findBy(array('enabled' => true), array('code' => 'ASC'));
            /** @var GeoCountry $country */
            foreach ($countries as $country) {
                $data[] = array(
                    'name' => $country->getName(),
                    'code' => $country->getCode(),
                    'locale' => $country->getLocale()
                );
            }

            return new JsonResponse(array(
                'message' => "",
                'data' => $data
            ));
        } catch (Exception $ex) {
            return new JsonResponse(array('message' => $ex->getMessage()), JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }


    /**
     * @Rest\Get("/properties/{page_id}", name="properties")
     *
     * @SWG\Parameter( name="page_id", in="path", type="string", description="The requested pagination page." )
     *
     * @SWG\Parameter( name="app_version", in="query", type="string", description="The version of the app." )
     * @SWG\Parameter( name="code_version", in="query", type="string", description="The version of the code." )
     * @SWG\Parameter( name="language", in="query", type="string", description="The language being used (either en or es)." )
     * @SWG\Parameter( name="time_offset", in="query", type="string", description="Time difference with respect to GMT time." )
     *
     * @SWG\Response(
     *     response=200,
     *     description="Returns the list of properties.",
     *     @SWG\Schema (
     *          @SWG\Property(
     *              property="data", type="array",
     *              @SWG\Items(
     *                  @SWG\Property( property="code", type="string", description="Code", example="101" ),
     *                  @SWG\Property( property="name", type="string", description="Name", example="Casa Modelo" ),
     *                  @SWG\Property( property="address", type="string", description="Address", example="1 Avenue des Champs-Elysees" ),
     *                  @SWG\Property( property="property_type_id", type="integer", description="Property Type ID", example="1" ),
     *                  @SWG\Property( property="complex_id", type="integer", description="Sector ID", example="1" ),
     *                  @SWG\Property( property="player_id", type="integer", description="Team player ID", example="1" ),
     *              )
     *          ),
     *          @SWG\Property( property="message", type="string", example="" ),
     *          @SWG\Property(
     *              property="metadata", type="object",
     *                  @SWG\Property( property="my_page", type="string", description="Current page in the list of items", example="4" ),
     *                  @SWG\Property( property="prev_page", type="string", description="Previous page in the list of items", example="3" ),
     *                  @SWG\Property( property="next_page", type="string", description="Next page in the list of items", example="5" ),
     *                  @SWG\Property( property="last_page", type="string", description="Last page in the list of items", example="8" ),
     *          )
     *      )
     * )
     *
     * @SWG\Response(
     *     response=500, description="Internal error.",
     *     @SWG\Schema (
     *          @SWG\Property(property="data", type="string", example="" ),
     *          @SWG\Property( property="message", type="string", example="Internal error." )
     *     )
     * )
     *
     * @SWG\Tag(name="Property")
     */

    public function getPropertiesAction($page_id)
    {
        try {
            $this->initialise();
            $data = array();

            $properties = $this->em->getRepository('BackendAdminBundle:Property')->findBy(
                array('enabled' => true),
                array('code' => 'ASC')
            );
            $total = $this->em->getRepository('BackendAdminBundle:Property')->countApiProperties();

            /** @var Property $property */
            foreach ($properties as $property) {
                $propertyType = $property->getPropertyType();
                if ($propertyType == null) {
                    $propertyType = new PropertyType();
                }
                $complexSector = $property->getComplexSector();
                if ($complexSector == null) {
                    $complexSector = new ComplexSector();
                }

                $data[] = array(
                    'code' => $property->getCode(), 'name' => $property->getName(),
                    'address' => $property->getAddress(), 'property_type_id' => $propertyType->getId(),
                    'sector_id' => $complexSector->getId(), 'player_id' => $property->getTeamCorrelative());
            }

            return new JsonResponse(array(
                'message' => "",
                'metadata' => $this->calculatePagesMetadata($page_id, $total),
                'data' => $data
            ));
        } catch (Exception $ex) {
            return new JsonResponse(array('message' => $ex->getMessage()), JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }


    /**
     * @Rest\Get("/property/{code}", name="property")
     *
     * @SWG\Parameter( name="code", in="path", type="string", description="The code of the property." )
     *
     * @SWG\Parameter( name="app_version", in="query", type="string", description="The version of the app." )
     * @SWG\Parameter( name="code_version", in="query", type="string", description="The version of the code." )
     * @SWG\Parameter( name="language", in="query", type="string", description="The language being used (either en or es)." )
     * @SWG\Parameter( name="time_offset", in="query", type="string", description="Time difference with respect to GMT time." )
     *
     * @SWG\Response(
     *     response=200,
     *     description="Returns the information of a property.",
     *     @SWG\Schema (
     *          @SWG\Property(
     *              property="data", type="object",
     *              @SWG\Property( property="code", type="string", description="Code", example="101" ),
     *              @SWG\Property( property="name", type="string", description="Name", example="Casa Modelo" ),
     *              @SWG\Property( property="address", type="string", description="Address", example="1 Avenue des Champs-Elysees" ),
     *              @SWG\Property( property="property_type_id", type="integer", description="Property Type ID", example="1" ),
     *              @SWG\Property( property="complex_id", type="integer", description="Sector ID", example="1" ),
     *              @SWG\Property( property="player_id", type="integer", description="Team player ID", example="1" ),
     *          ),
     *          @SWG\Property( property="message", type="string", example="" )
     *      )
     * )
     *
     * @SWG\Response(
     *     response=500, description="Internal error.",
     *     @SWG\Schema (
     *          @SWG\Property(property="data", type="string", example="" ),
     *          @SWG\Property( property="message", type="string", example="Internal error." )
     *     )
     * )
     *
     * @SWG\Tag(name="Property")
     */

    public function getPropertyAction($code)
    {
        try {
            $this->initialise();

            /** @var Property $property */
            $propertyResult = $this->em->getRepository('BackendAdminBundle:Property')->getApiProperty($code);
            $property = $propertyResult[0];

            $propertyType = $property->getPropertyType();
            if ($propertyType == null) {
                $propertyType = new PropertyType();
            }
            $complexSector = $property->getComplexSector();
            if ($complexSector == null) {
                $complexSector = new ComplexSector();
            }

            $data = array(
                'code' => $property->getCode(), 'name' => $property->getName(),
                'address' => $property->getAddress(), 'property_type_id' => $propertyType->getId(),
                'sector_id' => $complexSector->getId(), 'teamCorrelative' => $property->getTeamCorrelative());

            return new JsonResponse(array('message' => "", 'data' => $data));
        } catch (Exception $ex) {
            return new JsonResponse(array('message' => $ex->getMessage()), JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }


    /**
     * @Rest\Get("/inbox/{page_id}", name="inbox")
     *
     * @SWG\Parameter( name="page_id", in="path", type="string", description="The requested pagination page." )
     *
     * @SWG\Parameter( name="app_version", in="query", type="string", description="The version of the app." )
     * @SWG\Parameter( name="code_version", in="query", type="string", description="The version of the code." )
     * @SWG\Parameter( name="language", in="query", type="string", description="The language being used (either en or es)." )
     * @SWG\Parameter( name="time_offset", in="query", type="string", description="Time difference with respect to GMT time." )
     *
     * @SWG\Response(
     *     response=200,
     *     description="Returns the list of messages in the inbox.",
     *     @SWG\Schema (
     *          @SWG\Property(
     *              property="data", type="array",
     *              @SWG\Items(
     *                  @SWG\Property( property="avatar_path", type="string", description="Avatar Path", example="/avatars/1.jpg" ),
     *                  @SWG\Property( property="username", type="string", description="Username", example="user1" ),
     *                  @SWG\Property( property="role", type="string", description="Role of the User", example="Role" ),
     *                  @SWG\Property( property="user_notification", type="object",
     *                      @SWG\Property( property="description", type="string", description="Description", example="Notification Description" ),
     *                      @SWG\Property( property="notification_type", type="string", description="Type of Notification", example="accept_invitation" ),
     *                  ),
     *                  @SWG\Property( property="replies_quantity", type="integer", description="Quantity of replies for the associated ticket", example="10" ),
     *                  @SWG\Property( property="timestamp", type="string", description="Timestamp UTC formatted with RFC850", example="Monday, 15-Aug-05 15:52:01 UTC" ),
     *                  @SWG\Property( property="notification_type", type="string", description="Name of the type of notification", example="Type1" ),
     *                  @SWG\Property( property="notification_notice", type="string", description="Notification notice", example="Notice" ),
     *              )
     *          ),
     *          @SWG\Property( property="message", type="string", example="" ),
     *          @SWG\Property(
     *              property="metadata", type="object",
     *                  @SWG\Property( property="my_page", type="string", description="Current page in the list of items", example="4" ),
     *                  @SWG\Property( property="prev_page", type="string", description="Previous page in the list of items", example="3" ),
     *                  @SWG\Property( property="next_page", type="string", description="Next page in the list of items", example="5" ),
     *                  @SWG\Property( property="last_page", type="string", description="Last page in the list of items", example="8" ),
     *          )
     *      )
     * )
     *
     * @SWG\Response(
     *     response=500, description="Internal error.",
     *     @SWG\Schema (
     *          @SWG\Property(property="data", type="string", example="" ),
     *          @SWG\Property( property="message", type="string", example="Internal error." )
     *     )
     * )
     *
     * @SWG\Tag(name="User")
     */

    public function getInboxAction(Request $request, $page_id = 1)
    {
        try {
            $this->initialise();
            $data = array();
            $lang = strtolower(trim($request->get('language')));

            $notifications = $this->em->getRepository('BackendAdminBundle:UserNotification')->getApiInbox($this->getUser(), $page_id);
            $total = $this->em->getRepository('BackendAdminBundle:UserNotification')->countApiInbox($this->getUser());

            $ticketIds = array();
            /** @var UserNotification $notification */
            foreach ($notifications as $notification) {
                $id = $notification->getTicket()->getId();
                $ticketIds[] = $id;
            }
            $ticketIds = array_unique($ticketIds);

            $preComments = $this->em->getRepository('BackendAdminBundle:TicketComment')->getApiCountPerTickets($ticketIds);
            $commentsReplies = array();
            foreach ($preComments as $comment) {
                $commentsReplies[$comment['id']] = $comment['count'];
            }

            /** @var UserNotification $notification */
            foreach ($notifications as $notification) {
                $notificationUser = $notification->getCreatedBy();
                if ($notificationUser == null) {
                    $notificationUser = new User();
                }
                $notificationType = $notification->getNotificationType();
                if ($notificationType == null) {
                    $notificationType = new NotificationType();
                }
                $ticket = $notification->getTicket();
                if ($ticket == null) {
                    $ticket = new Ticket();
                }


                $data[] = array(
                    'avatar_path' => $notificationUser->getAvatarPath(),
                    'username' => $notificationUser->getUsername(),
                    'role' => (($lang == 'en') ? $notificationUser->getRole()->getName() : $notificationUser->getRole()->getNameES()),
                    'user_notification' => array(
                        'description' => $notification->getDescription(),
                        'notification_type' => (($lang == 'en') ? $notificationType->getNameEN() : $notificationType->getNameES())),
                    'replies_quantity' => (array_key_exists($ticket->getId(), $commentsReplies)) ? $commentsReplies[$ticket->getId()] : 0,
                    'timestamp' => $notificationUser->format(\DateTime::RFC850),
                    'notification_type' => (($lang == 'en') ? $notificationType->getNameEN() : $notificationType->getNameES()),
                    'notification_notice' => $notification->getNotice(),
                );
            }

            return new JsonResponse(array(
                'message' => "",
                'metadata' => $this->calculatePagesMetadata($page_id, $total),
                'data' => $data
            ));
        } catch (Exception $ex) {
            return new JsonResponse(array('message' => $ex->getMessage()), JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }


    /**
     * @Rest\Get("/ticketCategory/{property_id}/{complex_id}/{page_id}", name="ticket_categories")
     *
     * @SWG\Parameter( name="property_id", in="path", type="string", description="The ID of the property." )
     * @SWG\Parameter( name="complex_id", in="path", type="string", description="The ID of the Complex." )
     * @SWG\Parameter( name="page_id", in="path", type="string", description="The requested pagination page." )
     *
     * @SWG\Parameter( name="app_version", in="query", type="string", description="The version of the app." )
     * @SWG\Parameter( name="code_version", in="query", type="string", description="The version of the code." )
     * @SWG\Parameter( name="language", in="query", type="string", description="The language being used (either en or es)." )
     * @SWG\Parameter( name="time_offset", in="query", type="string", description="Time difference with respect to GMT time." )
     *
     * @SWG\Response(
     *     response=200,
     *     description="Returns the list of ticket categories for the filter.",
     *     @SWG\Schema (
     *          @SWG\Property(
     *              property="data", type="array",
     *              @SWG\Items(
     *                  @SWG\Property( property="id", type="integer", description="Category ID", example="1" ),
     *                  @SWG\Property( property="name", type="string", description="Name of the category", example="Category" ),
     *                  @SWG\Property( property="icon_url", type="string", description="URL for the category's icon", example="/icons/1.jpg" ),
     *              )
     *          ),
     *          @SWG\Property( property="message", type="string", example="" ),
     *          @SWG\Property(
     *              property="metadata", type="object",
     *                  @SWG\Property( property="my_page", type="string", description="Current page in the list of items", example="4" ),
     *                  @SWG\Property( property="prev_page", type="string", description="Previous page in the list of items", example="3" ),
     *                  @SWG\Property( property="next_page", type="string", description="Next page in the list of items", example="5" ),
     *                  @SWG\Property( property="last_page", type="string", description="Last page in the list of items", example="8" ),
     *          )
     *      )
     * )
     *
     * @SWG\Response(
     *     response=500, description="Internal error.",
     *     @SWG\Schema (
     *          @SWG\Property( property="data", type="string", example="" ),
     *          @SWG\Property( property="message", type="string", example="Internal error." )
     *     )
     * )
     *
     * @SWG\Tag(name="User")
     */
    public function getTicketCategoriesAction($property_id, $complex_id, $page_id = 1)
    {
        try {
            $this->initialise();
            $data = array();

            $categories = $this->em->getRepository('BackendAdminBundle:Ticket')->getApiTicketCategories($property_id, $complex_id);
            $total = $this->em->getRepository('BackendAdminBundle:Ticket')->countApiTicketCategories($property_id, $complex_id);

            /** @var TicketCategory $category */
            foreach ($categories as $category) {
                $data[] = array(
                    'category_id' => $category->getId(),
                    'category_name' => $category->getName(),
                    'icon_url' => $category->getIconUrl(),
                );
            }

            return new JsonResponse(array(
                'message' => "",
                'metadata' => $this->calculatePagesMetadata($page_id, $total),
                'data' => $data
            ));
        } catch (Exception $ex) {
            return new JsonResponse(array('message' => $ex->getMessage()), JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }


    /**
     * @Rest\Get("/feed/{property_id}/{filter_category_id}/{page_id}", name="feed")
     *
     * @SWG\Parameter( name="property_id", in="path", type="string", description="The ID of the property." )
     * @SWG\Parameter( name="filter_category_id", in="path", type="string", description="The ID of the Filter Category." )
     * @SWG\Parameter( name="page_id", in="path", type="string", description="The requested pagination page." )
     *
     * @SWG\Parameter( name="app_version", in="query", type="string", description="The version of the app." )
     * @SWG\Parameter( name="code_version", in="query", type="string", description="The version of the code." )
     * @SWG\Parameter( name="language", in="query", type="string", description="The language being used (either en or es)." )
     * @SWG\Parameter( name="time_offset", in="query", type="string", description="Time difference with respect to GMT time." )
     *
     * @SWG\Response(
     *     response=200,
     *     description="Returns the list of ticket categories for the filter.",
     *     @SWG\Schema (
     *          @SWG\Property(
     *              property="data", type="array",
     *              @SWG\Items(
     *                  @SWG\Property( property="ticket_id", type="string", description="Ticket ID", example="1" ),
     *                  @SWG\Property( property="ticket_type_id", type="string", description="Ticket type ID", example="1" ),
     *                  @SWG\Property( property="ticket_type_name", type="string", description="Ticket type name", example="TicketTypeName" ),
     *                  @SWG\Property( property="status", type="string", description="Ticket status", example="Status" ),
     *                  @SWG\Property( property="ticket_title", type="string", description="Ticket title", example="TicketTile" ),
     *                  @SWG\Property( property="ticket_description", type="string", description="Ticket description", example="Lorem ipsum." ),
     *                  @SWG\Property( property="ticket_is_public", type="boolean", description="Is ticket public?", example="true" ),
     *                  @SWG\Property( property="username", type="string", description="Ticket's creator username", example="admin" ),
     *                  @SWG\Property( property="role", type="string", description="Ticket's creator role", example="Admin" ),
     *                  @SWG\Property( property="timestamp", type="string", description="Ticket created timestamp UTC formatted with RFC850", example="Monday, 15-Aug-05 15:52:01 UTC" ),
     *                  @SWG\Property( property="followers_quantity", type="string", description="Amount of followers for the ticket", example="2" ),
     *                  @SWG\Property( property="common_area", type="object",
     *                      @SWG\Property( property="id", type="string", description="Common area ID", example="1" ),
     *                      @SWG\Property( property="name", type="string", description="Common area name", example="Common area" ),
     *                      @SWG\Property( property="reservation_status", type="string", description="Common area reservation status", example="" ),
     *                      @SWG\Property( property="reservation_from", type="string", description="Common area reservation from date", example="Monday, 16-Aug-05 15:52:01 UTC" ),
     *                      @SWG\Property( property="reservation_to", type="string", description="Common area reservation to date", example="Monday, 17-Aug-05 15:52:01 UTC" ),
     *                  ),
     *                  @SWG\Property( property="comments_quantity", type="string", description="Ammount of comments for the ticket", example="3" ),
     *              )
     *          ),
     *          @SWG\Property( property="message", type="string", example="" ),
     *          @SWG\Property(
     *              property="metadata", type="object",
     *                  @SWG\Property( property="my_page", type="string", description="Current page in the list of items", example="4" ),
     *                  @SWG\Property( property="prev_page", type="string", description="Previous page in the list of items", example="3" ),
     *                  @SWG\Property( property="next_page", type="string", description="Next page in the list of items", example="5" ),
     *                  @SWG\Property( property="last_page", type="string", description="Last page in the list of items", example="8" ),
     *          )
     *      )
     * )
     *
     * @SWG\Response(
     *     response=500, description="Internal error.",
     *     @SWG\Schema (
     *          @SWG\Property(property="data", type="string", example="" ),
     *          @SWG\Property( property="message", type="string", example="Internal error." )
     *     )
     * )
     *
     * @SWG\Tag(name="Ticket")
     */
    public function getFeedAction(Request $request, $property_id, $complex_id, $page_id = 1)
    {
        try {
            $this->initialise();
            $data = array();
            $lang = strtolower(trim($request->get('language')));

            $tickets = $this->em->getRepository('BackendAdminBundle:Ticket')->getApiFeed($property_id, $complex_id, $page_id);
            $total = $this->em->getRepository('BackendAdminBundle:Ticket')->countApiFeed($property_id, $complex_id);

            $ticketIds = $this->getArrayOfIds($tickets);

            $preComments = $this->em->getRepository('BackendAdminBundle:TicketComment')->getApiCountPerTickets($ticketIds);
            $comments = array();
            foreach ($preComments as $comment) {
                $comments[$comment['id']] = $comment['count'];
            }

            $preFollowers = $this->em->getRepository('BackendAdminBundle:TicketFollower')->getApiCountPerTickets($ticketIds);
            $followers = array();
            foreach ($preFollowers as $follower) {
                $followers[$follower['id']] = $follower['count'];
            }

            /** @var Ticket $ticket */
            foreach ($tickets as $ticket) {
                $ticketType = $ticket->getTicketType();
                if ($ticketType == null) {
                    $ticketType = new TicketType();
                }
                $ticketStatus = $ticket->getTicketStatus();
                if ($ticketStatus == null) {
                    $ticketStatus = new TicketStatus();
                }
                $ticketUser = $ticket->getCreatedBy();
                if ($ticketUser == null) {
                    $ticketUser = new User();
                }

                $commonAreaReservation = $ticket->getCommonAreaReservation();
                if ($commonAreaReservation == null) {
                    $commonAreaReservation = new CommonAreaReservation();
                }
                $commonAreaReservationStatus = $commonAreaReservation->getCommonAreaResevationStatus();
                if ($commonAreaReservationStatus == null) {
                    $commonAreaReservationStatus = new CommonAreaReservationStatus();
                }
                $commonArea = $commonAreaReservation->getCommonArea();
                if ($commonArea == null) {
                    $commonArea = new CommonArea();
                }

                $data[] = array(
                    'ticket_id' => $ticket->getId(),
                    'ticket_type_id' => $ticketType->getId(),
                    'ticket_type_name' => $ticketType->getName(),
                    'status' => (($lang == 'en') ? $ticketStatus->getNameEN() : $ticketStatus->getNameES()),
                    'ticket_title' => $ticket->getTitle(),
                    'ticket_description' => $ticket->getDescription(),
                    'ticket_is_public' => $ticket->getIsPublic(),
                    'username' => $ticketUser->getUsername(),
                    'role' => (($lang == 'en') ? $ticketUser->getRole()->getName() : $ticketUser->getRole()->getNameES()),
                    'timestamp' => $ticket->getCreatedAt()->format(\DateTime::RFC850),
                    'followers_quantity' => (array_key_exists($ticket->getId(), $followers)) ? $followers[$ticket->getId()] : 0,
                    'common_area' => array(
                        "id" => $commonArea->getId(),
                        "name" => $commonArea->getName(),
                        "status" => (($lang == 'en') ? $commonAreaReservationStatus->getNameEN() : $commonAreaReservationStatus->getNameES()),
                        "reservation_from" => $commonAreaReservation->getReservationDateFrom()->format(\DateTime::RFC850),
                        "reservation_to" => $commonAreaReservation->getReservationDateTo()->format(\DateTime::RFC850),
                    ),
                    'comments_quantity' => (array_key_exists($ticket->getId(), $comments)) ? $comments[$ticket->getId()] : 0,
                );
            }

            return new JsonResponse(array(
                'message' => "",
                'metadata' => $this->calculatePagesMetadata($page_id, $total),
                'data' => $data
            ));
        } catch (Exception $ex) {
            return new JsonResponse(array('message' => $ex->getMessage()), JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @Rest\Get("/ticket/{ticket_id}", name="ticket")
     *
     * @SWG\Parameter( name="ticket_id", in="path", type="string", description="The ID of the ticket." )
     *
     * @SWG\Parameter( name="app_version", in="query", type="string", description="The version of the app." )
     * @SWG\Parameter( name="code_version", in="query", type="string", description="The version of the code." )
     * @SWG\Parameter( name="language", in="query", type="string", description="The language being used (either en or es)." )
     * @SWG\Parameter( name="time_offset", in="query", type="string", description="Time difference with respect to GMT time." )
     *
     * @SWG\Response(
     *     response=200,
     *     description="Returns the ticket alongside comments, followers and reservations (if its the case).",
     *     @SWG\Schema (
     *          @SWG\Property(
     *              property="data", type="object",
     *                  @SWG\Property( property="ticket_title", type="string", description="Ticket title", example="TicketTile" ),
     *                  @SWG\Property( property="username", type="string", description="Ticket's creator username", example="admin" ),
     *                  @SWG\Property( property="status", type="string", description="Ticket status", example="Status" ),
     *                  @SWG\Property( property="timestamp", type="string", description="Ticket created timestamp UTC formatted with RFC850", example="Monday, 15-Aug-05 15:52:01 UTC" ),
     *                  @SWG\Property( property="followers_quantity", type="string", description="Amount of followers for the ticket", example="2" ),
     *                  @SWG\Property( property="comments_quantity", type="string", description="Ammount of comments for the ticket", example="3" ),
     *                  @SWG\Property( property="ticket_description", type="string", description="Ticket description", example="Lorem ipsum." ),
     *                  @SWG\Property( property="comments", type="array",
     *                      @SWG\Items(
     *                          @SWG\Property( property="username", type="string", description="Comments's creator username", example="2" ),
     *                          @SWG\Property( property="timestamp", type="string", description="Comment's creation time", example="Monday, 15-Aug-05 15:52:01 UTC" ),
     *                          @SWG\Property( property="like", type="string", description="User that liked the comment", example="user2" ),
     *                          @SWG\Property( property="comment", type="string", description="Comment content", example="Comment" ),
     *                          @SWG\Property( property="icon_url", type="string", description="URL for the icon", example="/icons/1.jpg" ),
     *                      )
     *                  ),
     *                  @SWG\Property( property="common_area", type="object",
     *                      @SWG\Property( property="id", type="string", description="Common area ID", example="1" ),
     *                      @SWG\Property( property="name", type="string", description="Common area name", example="Common area" ),
     *                      @SWG\Property( property="reservation_status", type="string", description="Common area reservation status", example="" ),
     *                      @SWG\Property( property="reservation_from", type="string", description="Common area reservation from date", example="Monday, 16-Aug-05 15:52:01 UTC" ),
     *                      @SWG\Property( property="reservation_to", type="string", description="Common area reservation to date", example="Monday, 17-Aug-05 15:52:01 UTC" ),
     *                  ),
     *          ),
     *          @SWG\Property( property="message", type="string", example="" )
     *      )
     * )
     *
     * @SWG\Response(
     *     response=500, description="Internal error.",
     *     @SWG\Schema (
     *          @SWG\Property(property="data", type="string", example="" ),
     *          @SWG\Property( property="message", type="string", example="Internal error." )
     *     )
     * )
     *
     * @SWG\Tag(name="Ticket")
     */
    public function getTicketAction(Request $request, $ticket_id)
    {
        try {
            $this->initialise();
            $lang = strtolower(trim($request->get('language')));

            $tickets = $this->em->getRepository('BackendAdminBundle:Ticket')->getApiSingleTicket($ticket_id);
            $ticket = $tickets[0];

            $ticketIds = array($ticket->getId());

            // Fetching the ticket comment counts
            $rawCommentsCount = $this->em->getRepository('BackendAdminBundle:TicketComment')->getApiCountPerTickets($ticketIds);
            $commentsCount = array();
            foreach ($rawCommentsCount as $comment) {
                $commentsCount[$comment['id']] = $comment['count'];
            }

            // Fetching the ticket followers counts
            $rawFollowersCount = $this->em->getRepository('BackendAdminBundle:TicketFollower')->getApiCountPerTickets($ticketIds);
            $followersCount = array();
            foreach ($rawFollowersCount as $follower) {
                $followersCount[$follower['id']] = $follower['count'];
            }

            // Fetching the ticket comments
            $comments = $this->em->getRepository('BackendAdminBundle:TicketComment')->getApiSingleTicketComments($ticket_id);

//            $ticketType = $ticket->getTicketType();
//            if ($ticketType == null) {
//                $ticketType = new TicketType();
//            }
            $ticketStatus = $ticket->getTicketStatus();
            if ($ticketStatus == null) {
                $ticketStatus = new TicketStatus();
            }
            $ticketUser = $ticket->getCreatedBy();
            if ($ticketUser == null) {
                $ticketUser = new User();
            }

            $data = array(
                'ticket_title' => $ticket->getTitle(),
                'username' => $ticketUser->getUsername(),
                'status' => (($lang == 'en') ? $ticketStatus->getNameEN() : $ticketStatus->getNameES()),
                'timestamp' => $ticket->getCreatedAt()->format(\DateTime::RFC850),
                'followers_quantity' => (array_key_exists($ticket->getId(), $followersCount)) ? $followersCount[$ticket->getId()] : 0,
                'comments_quantity' => (array_key_exists($ticket->getId(), $commentsCount)) ? $commentsCount[$ticket->getId()] : 0,
                'ticket_description' => $ticket->getDescription(),
            );


            $data['comments'] = array();
            /** @var TicketComment $comment */
            foreach ($comments as $comment) {
                $commentUser = $comment->getCreatedBy();
                if ($commentUser == null) {
                    $commentUser = new User();
                }
                $likeUser = $comment->getLikedBy();
                if ($likeUser == null) {
                    $likeUser = new User();
                }

                $data['comments'][] = array(
                    'username' => $commentUser->getUsername(),
                    'timestamp' => $comment->getCreatedAt()->format(\DateTime::RFC850),
                    'like' => $likeUser->getUsername(), // ToDo: Question -> Solo un usuario le puede dar like o varios?
                    'comment' => $comment->getCommentDescription(),
                    'icon_url' => "", // ToDo: Question -> De donde sale este campo? Y porque tienen q estar cargados en la app (asi dice el doc)?
                );
            }


            if ($ticket->getCommonAreaReservation() != null) {
                $commonAreaReservation = $ticket->getCommonAreaReservation();
                if ($commonAreaReservation == null) {
                    $commonAreaReservation = new CommonAreaReservation();
                }
                $commonAreaReservationStatus = $commonAreaReservation->getCommonAreaResevationStatus();
                if ($commonAreaReservationStatus == null) {
                    $commonAreaReservationStatus = new CommonAreaReservationStatus();
                }
                $commonArea = $commonAreaReservation->getCommonArea();
                if ($commonArea == null) {
                    $commonArea = new CommonArea();
                }

                $data['common_area'] = array(
                    "id" => $commonArea->getId(),
                    "name" => $commonArea->getName(),
                    "status" => (($lang == 'en') ? $commonAreaReservationStatus->getNameEN() : $commonAreaReservationStatus->getNameES()),
                    "reservation_from" => $commonAreaReservation->getReservationDateFrom()->format(\DateTime::RFC850),
                    "reservation_to" => $commonAreaReservation->getReservationDateTo()->format(\DateTime::RFC850),
                );
            }

            return new JsonResponse(array(
                'message' => "",
                'data' => $data
            ));
        } catch (Exception $ex) {
            return new JsonResponse(array('message' => $ex->getMessage()), JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @Rest\Get("/polls/{page_id}", name="polls")
     *
     * @SWG\Parameter( name="page_id", in="path", type="string", description="The requested pagination page." )
     *
     * @SWG\Parameter( name="app_version", in="query", type="string", description="The version of the app." )
     * @SWG\Parameter( name="code_version", in="query", type="string", description="The version of the code." )
     * @SWG\Parameter( name="language", in="query", type="string", description="The language being used (either en or es)." )
     * @SWG\Parameter( name="time_offset", in="query", type="string", description="Time difference with respect to GMT time." )
     *
     * @SWG\Response(
     *     response=200,
     *     description="Returns the list of active polls bby date.",
     *     @SWG\Schema (
     *          @SWG\Property(
     *              property="data", type="array",
     *              @SWG\Items(
     *                  @SWG\Property( property="id", type="integer", description="Poll ID", example="1" ),
     *                  @SWG\Property( property="name", type="string", description="Name of the poll", example="Poll" ),
     *              )
     *          ),
     *          @SWG\Property( property="message", type="string", example="" ),
     *          @SWG\Property(
     *              property="metadata", type="object",
     *                  @SWG\Property( property="my_page", type="string", description="Current page in the list of items", example="4" ),
     *                  @SWG\Property( property="prev_page", type="string", description="Previous page in the list of items", example="3" ),
     *                  @SWG\Property( property="next_page", type="string", description="Next page in the list of items", example="5" ),
     *                  @SWG\Property( property="last_page", type="string", description="Last page in the list of items", example="8" ),
     *          )
     *      )
     * )
     *
     * @SWG\Response(
     *     response=500, description="Internal error.",
     *     @SWG\Schema (
     *          @SWG\Property( property="data", type="string", example="" ),
     *          @SWG\Property( property="message", type="string", example="Internal error." )
     *     )
     * )
     *
     * @SWG\Tag(name="Poll")
     */
    public function getPollsAction($page_id = 1)
    {
        try {
            $this->initialise();
            $data = array();

            $polls = $this->em->getRepository('BackendAdminBundle:Poll')->getApiPolls($page_id);
            $total = $this->em->getRepository('BackendAdminBundle:Poll')->countApiPolls();

            /** @var Poll $poll */
            foreach ($polls as $poll) {
                $data[] = array(
                    'id' => $poll->getId(),
                    'name' => $poll->getName(),
                );
            }

            return new JsonResponse(array(
                'message' => "",
                'metadata' => $this->calculatePagesMetadata($page_id, $total),
                'data' => $data
            ));
        } catch (Exception $ex) {
            return new JsonResponse(array('message' => $ex->getMessage()), JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @Rest\Get("/poll/{poll_id}", name="poll")
     *
     * @SWG\Parameter( name="poll_id", in="path", type="string", description="The ID of the poll." )
     *
     * @SWG\Parameter( name="app_version", in="query", type="string", description="The version of the app." )
     * @SWG\Parameter( name="code_version", in="query", type="string", description="The version of the code." )
     * @SWG\Parameter( name="language", in="query", type="string", description="The language being used (either en or es)." )
     * @SWG\Parameter( name="time_offset", in="query", type="string", description="Time difference with respect to GMT time." )
     *
     * @SWG\Response(
     *     response=200,
     *     description="Returns the poll including all of its details.",
     *     @SWG\Schema (
     *          @SWG\Property(
     *              property="data", type="object",
     *                  @SWG\Property( property="id", type="integer", description="Poll ID", example="1" ),
     *                  @SWG\Property( property="name", type="string", description="Name of the poll", example="Poll" ),
     *                  @SWG\Property( property="questions", type="array",
     *                      @SWG\Items(
     *                          @SWG\Property( property="poll_question", type="string", description="Poll question", example="Question" ),
     *                          @SWG\Property( property="poll_file_photo", type="string", description="Poll file photo", example="/photo.jpg" ),
     *                          @SWG\Property( property="question", type="string", description="Poll question", example="Question" ),
     *                          @SWG\Property( property="question_type", type="string", description="Poll question type", example="Type" ),
     *                          @SWG\Property( property="poll_question_options", type="array",
     *                              @SWG\Items(
     *                              @SWG\Property( property="option", type="string", description="Question's option", example="Option" ),
     *                              )
     *                          ),
     *                      )
     *                  ),
     *          ),
     *          @SWG\Property( property="message", type="string", example="" ),
     *          @SWG\Property(
     *              property="metadata", type="object",
     *                  @SWG\Property( property="my_page", type="string", description="Current page in the list of items", example="4" ),
     *                  @SWG\Property( property="prev_page", type="string", description="Previous page in the list of items", example="3" ),
     *                  @SWG\Property( property="next_page", type="string", description="Next page in the list of items", example="5" ),
     *                  @SWG\Property( property="last_page", type="string", description="Last page in the list of items", example="8" ),
     *          )
     *      )
     * )
     *
     * @SWG\Response(
     *     response=500, description="Internal error.",
     *     @SWG\Schema (
     *          @SWG\Property( property="data", type="string", example="" ),
     *          @SWG\Property( property="message", type="string", example="Internal error." )
     *     )
     * )
     *
     * @SWG\Tag(name="Poll")
     */
    public function getPollAction($poll_id)
    {
        try {
            $this->initialise();

            $poll = $this->em->getRepository('BackendAdminBundle:Poll')->findById($poll_id);
            $questions = $this->em->getRepository('BackendAdminBundle:PollQuestion')->getApiPoll($poll_id);

            $qids = $this->getArrayOfIds($questions);

            $rawAnswers = $this->em->getRepository('BackendAdminBundle:PollQuestionOption')->getApiPoll($qids);
            $answers = array();
            /** @var PollQuestionOption $answer */
            foreach ($rawAnswers as $answer) {
                $answers[$answer->getPollQuestion()->getId()] = $answer;
            }

            $data = array(
                'id' => $poll->getId(),
                'name' => $poll->getName(),
            );


            $data['questions'] = array();
            /** @var PollQuestion $question */
            foreach ($questions as $question) {

                $options = array();
                foreach( $answers[$question->getId()] as $answer ) {
                    $options[] = array( 'option' => $answer->getQuestionOption() );
                }

                $data['questions'][] = array(
                    'poll_question' => $question->getQuestion(),
                    'poll_file_photo' => $question->getPollFilePhoto(),
                    'question' => $question->getQuestion(), // ToDo: Question -> Cual es la diferencia entre question y poll_question?
                    'question_type' => $question->getPollQuestionType()->getName(),
                    'poll_question_options' => $options, // ToDo: Question -> Esto seria un array tambien?
                );
            }

            return new JsonResponse(array(
                'message' => "",
                'data' => $data
            ));
        } catch (Exception $ex) {
            return new JsonResponse(array('message' => $ex->getMessage()), JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }


    /**
     * @Rest\Get("/commonAreas/{property_id}/{page_id}", name="common_areas")
     *
     * @SWG\Parameter( name="property_id", in="path", type="string", description="The ID of the property." )
     * @SWG\Parameter( name="page_id", in="path", type="string", description="The requested pagination page." )
     *
     * @SWG\Parameter( name="app_version", in="query", type="string", description="The version of the app." )
     * @SWG\Parameter( name="code_version", in="query", type="string", description="The version of the code." )
     * @SWG\Parameter( name="language", in="query", type="string", description="The language being used (either en or es)." )
     * @SWG\Parameter( name="time_offset", in="query", type="string", description="Time difference with respect to GMT time." )
     *
     * @SWG\Response(
     *     response=200,
     *     description="Returns the list of common areas of a property.",
     *     @SWG\Schema (
     *          @SWG\Property(
     *              property="data", type="object",
     *                  @SWG\Property( property="id", type="integer", description="Common area ID", example="1" ),
     *                  @SWG\Property( property="name", type="string", description="Name of the common area", example="Common area" ),
     *                  @SWG\Property( property="description", type="string", description="Description of the common area", example="Description" ),
     *                  @SWG\Property( property="common_area_type", type="string", description="Type of the common area", example="Description" ),
     *                  @SWG\Property( property="common_area_photos", type="array",
     *                      @SWG\Items(
     *                          @SWG\Property( property="url", type="string", description="URL of common area photo", example="/photo.jpg" ),
     *                      )
     *                  ),
     *          ),
     *          @SWG\Property( property="message", type="string", example="" ),
     *          @SWG\Property(
     *              property="metadata", type="object",
     *                  @SWG\Property( property="my_page", type="string", description="Current page in the list of items", example="4" ),
     *                  @SWG\Property( property="prev_page", type="string", description="Previous page in the list of items", example="3" ),
     *                  @SWG\Property( property="next_page", type="string", description="Next page in the list of items", example="5" ),
     *                  @SWG\Property( property="last_page", type="string", description="Last page in the list of items", example="8" ),
     *          )
     *      )
     * )
     *
     * @SWG\Response(
     *     response=500, description="Internal error.",
     *     @SWG\Schema (
     *          @SWG\Property( property="data", type="string", example="" ),
     *          @SWG\Property( property="message", type="string", example="Internal error." )
     *     )
     * )
     *
     * @SWG\Tag(name="Common Area")
     */
    public function getCommonAreasAction($property_id, $page_id = 1)
    {
        try {
            $this->initialise();
            $data = array();

            $properties = $this->em->getRepository('BackendAdminBundle:Property')->getApiCommonAreas($property_id);

            $complexIds = array();
            /** @var Property $property */
            foreach ($properties as $property) {
                $complexIds[] = $property->getComplexSector()->getComplex()->getId();
            }

            $commonAreas = $this->em->getRepository('BackendAdminBundle:CommonArea')->getApiCommonAreas($complexIds, $page_id);
            $total = $this->em->getRepository('BackendAdminBundle:CommonArea')->countApiCommonAreas($complexIds);

            $cids = $this->getArrayOfIds($commonAreas);

            $rawPhotos = $this->em->getRepository('BackendAdminBundle:CommonAreaPhoto')->getApiCommonAreas($cids);
            $photos = array();
            /** @var CommonAreaPhoto $photo */
            foreach ($rawPhotos as $photo) {
                $photos[$photo->getCommonArea()->getId()] = $photo;
            }

            /** @var CommonArea $commonArea */
            foreach ($commonAreas as $commonArea) {
                $commonAreaPhotos = array();
                /** @var CommonAreaPhoto $photo */
                foreach ($photos[$commonArea->getId()] as $photo) {
                    $commonAreaPhotos[] = array( 'url' => $photo->getPhotoPath() ); // ToDo: Question -> Esta propiedad es int.
                }

                $commonAreaType = $commonArea->getCommonAreaType();
                if ($commonAreaType == null) {
                    $commonAreaType = new CommonAreaType();
                }

                $data[] = array(
                    'id' => $commonArea->getId(), // ToDo: Question -> No esta en el spec.
                    'name' => $commonArea->getName(),
                    'description' => $commonArea->getDescription(),
                    'common_area_type' => $commonAreaType->getName(),
                    'common_area_photos' => $commonAreaPhotos,
                );
            }

            return new JsonResponse(array(
                'message' => "",
                'metadata' => $this->calculatePagesMetadata($page_id, $total),
                'data' => $data
            ));
        } catch (Exception $ex) {
            return new JsonResponse(array('message' => $ex->getMessage()), JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }


    /**
     * @Rest\Get("/commonAreaAvailability/{common_area_id}", name="common_area_availability")
     *
     * @SWG\Parameter( name="common_area_id", in="path", type="string", description="The ID of the common area." )
     *
     * @SWG\Parameter( name="app_version", in="query", type="string", description="The version of the app." )
     * @SWG\Parameter( name="code_version", in="query", type="string", description="The version of the code." )
     * @SWG\Parameter( name="language", in="query", type="string", description="The language being used (either en or es)." )
     * @SWG\Parameter( name="time_offset", in="query", type="string", description="Time difference with respect to GMT time." )
     *
     * @SWG\Response(
     *     response=200,
     *     description="Returns a list of availabilities and other list of reservations from a common area.",
     *     @SWG\Schema (
     *          @SWG\Property(
     *              property="data", type="object",
     *                  @SWG\Property( property="reservation", type="array",
     *                      @SWG\Items(
     *                          @SWG\Property( property="common_area_resevation_status", type="string", description="Reservation status for the common area", example="status" ),
     *                          @SWG\Property( property="reservation_date_from", type="string", description="Reservation date from UTC formatted with RFC850", example="Monday, 15-Aug-05 15:52:01 UTC" ),
     *                          @SWG\Property( property="reservation_date_to", type="string", description="Reservation date to UTC formatted with RFC850", example="Monday, 16-Aug-05 15:52:01 UTC" ),
     *                      )
     *                  ),
     *                  @SWG\Property( property="common_area_availability", type="array",
     *                      @SWG\Items(
     *                          @SWG\Property( property="week_day_range_start", type="integer", description="The day of the week that the range starts", example="1" ),
     *                          @SWG\Property( property="week_day_range_finish", type="integer", description="The day of the week that the range ends", example="5" ),
     *                          @SWG\Property( property="week_day", type="integer", description="The day of the week", example="2" ),
     *                          @SWG\Property( property="hour_from", type="integer", description="From hour", example="10" ),
     *                          @SWG\Property( property="hour_from", type="integer", description="To hour", example="15" ),
     *                      )
     *                  ),
     *          ),
     *          @SWG\Property( property="message", type="string", example="" ),
     *      )
     * )
     *
     * @SWG\Response(
     *     response=500, description="Internal error.",
     *     @SWG\Schema (
     *          @SWG\Property( property="data", type="string", example="" ),
     *          @SWG\Property( property="message", type="string", example="Internal error." )
     *     )
     * )
     *
     * @SWG\Tag(name="Common Area")
     */
    public function getCommonAreaAvailabilityAction(Request $request, $common_area_id)
    {
        try {
            $this->initialise();
            $lang = strtolower(trim($request->get('language')));
            $data = array( 'reservations' => array(), 'availabilities' => array() );

            $availabilities = $this->em->getRepository('BackendAdminBundle:CommonAreaAvailability')->getApiCommonAreaAvailability($common_area_id);
            $reservations = $this->em->getRepository('BackendAdminBundle:CommonAreaReservation')->getApiCommonAreaAvailability($common_area_id);

            /** @var CommonAreaReservation $reservation */
            foreach ($reservations as $reservation) {
                $status = $reservation->getCommonAreaResevationStatus();
                if ($status == null) {
                    $status = new CommonAreaReservationStatus();
                }

                $data['reservations'][] = array(
                    'common_area_resevation_status' => ($lang == 'en') ? $status->getNameEN() : $status->getNameES(),
                    'reservation_date_from' => $reservation->getReservationDateFrom()->format(\DateTime::RFC850),
                    'reservation_date_to' => $reservation->getReservationDateTo()->format(\DateTime::RFC850),
                );
            }

            /** @var CommonAreaAvailability $availability */
            foreach ($availabilities as $availability) {
                $data['availabilities'][] = array(
                    'week_day_range_start' => $availability->getWeekDayRangeStart(),
                    'week_day_range_finish' => $availability->getWeekDayRangeFinish(),
                    'week_day' => $availability->getWeekdaySingle(),
                    'hour_from' => $availability->getHourFrom(),
                    'hour_to' => $availability->getHourTo(),
                );
            }

            return new JsonResponse(array(
                'message' => "",
                'data' => $data
            ));
        } catch (Exception $ex) {
            return new JsonResponse(array('message' => $ex->getMessage()), JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @Rest\Get("/commonArea/{common_area_id}", name="common_area")
     *
     * @SWG\Parameter( name="common_area_id", in="path", type="string", description="The ID of the common area." )
     *
     * @SWG\Parameter( name="app_version", in="query", type="string", description="The version of the app." )
     * @SWG\Parameter( name="code_version", in="query", type="string", description="The version of the code." )
     * @SWG\Parameter( name="language", in="query", type="string", description="The language being used (either en or es)." )
     * @SWG\Parameter( name="time_offset", in="query", type="string", description="Time difference with respect to GMT time." )
     *
     * @SWG\Response(
     *     response=200,
     *     description="Returns the details from a common area.",
     *     @SWG\Schema (
     *          @SWG\Property(
     *              property="data", type="object",
     *                  @SWG\Property( property="name", type="string", description="Name of the common area", example="Common area" ),
     *                  @SWG\Property( property="description", type="string", description="Description of the common area", example="Description" ),
     *                  @SWG\Property( property="regulation", type="string", description="Regulation of the common area", example="Regulation" ),
     *                  @SWG\Property( property="term_condition", type="string", description="Terms and conditions of the common area", example="Terms and conditions" ),
     *                  @SWG\Property( property="price", type="float", description="Price of the common area", example="100" ),
     *                  @SWG\Property( property="reservation_hour_period", type="integer", description="The reservation hour period of the common area", example="" ),
     *                  @SWG\Property( property="required_payment", type="boolean", description="The required payment for the reservation of the common area", example="true" ),
     *                  @SWG\Property( property="has_equipment", type="boolean", description="If the common area is equiped", example="true" ),
     *                  @SWG\Property( property="equipment_description", type="string", description="Description of the equipement of the common area", example="" ),
     *          ),
     *          @SWG\Property( property="message", type="string", example="" ),
     *      )
     * )
     *
     * @SWG\Response(
     *     response=500, description="Internal error.",
     *     @SWG\Schema (
     *          @SWG\Property( property="data", type="string", example="" ),
     *          @SWG\Property( property="message", type="string", example="Internal error." )
     *     )
     * )
     *
     * @SWG\Tag(name="Common Area")
     */
    public function getCommonAreaAction($common_area_id)
    {
        try {
            $this->initialise();

            /** @var CommonArea $commonArea */
            $commonArea = $this->em->getRepository('BackendAdminBundle:CommonArea')->findById($common_area_id);

            $data = array(
                'name' => $commonArea->getName(),
                'description' => $commonArea->getDescription(),
                'regulation' => $commonArea->getRegulation(),
                'term_condition' => $commonArea->getTermCondition(),
                'price' => $commonArea->getPrice(),
                'reservation_hour_period' => $commonArea->getReservationHourPeriod(),
                'required_payment' => $commonArea->getRequiredPayment(),
                'has_equipment' => $commonArea->getHasEquipment(),
                'equipment_description' => $commonArea->getEquipmentDescription(),
            );

            return new JsonResponse(array(
                'message' => "",
                'data' => $data
            ));
        } catch (Exception $ex) {
            return new JsonResponse(array('message' => $ex->getMessage()), JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }






    private function calculatePagesMetadata($page, $total)
    {
        return array(
            'my_page' => $page,
            'prev_page' => ($page <= 1) ? 1 : $page - 1,
            'next_page' => ($page >= $total) ? $total : $page + 1,
            'last_page' => round($total / 10, 0, PHP_ROUND_HALF_UP) // ToDo
        );
    }

    private function getArrayOfIds($items) {
        $ids = array();

        foreach ($items as $item) {
            $ids[] = $item->getId();
        }

        return array_unique($ids);
    }

}