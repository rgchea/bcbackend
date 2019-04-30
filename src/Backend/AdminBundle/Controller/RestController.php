<?php

namespace Backend\AdminBundle\Controller;

use Backend\AdminBundle\Entity\GeoCountry;
use Backend\AdminBundle\Entity\Property;
use Backend\AdminBundle\Entity\TermCondition;
use Backend\AdminBundle\Entity\TicketCategory;
use Backend\AdminBundle\Entity\TicketComment;
use Backend\AdminBundle\Entity\User;
use Backend\AdminBundle\Entity\UserNotification;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\FOSRestController;
use Nelmio\ApiDocBundle\Annotation\Model;
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
     * @Rest\Get("/termsConditions/{lang}", name="terms_and_conditions")
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
     * @SWG\Parameter(
     *     name="lang", in="path", type="string",
     *     description="The language of the Terms and Conditions.",
     * )
     *
     * @SWG\Tag(name="Admin")
     */

    public function getTermsAndConditionsAction($lang)
    {
        try {
            $this->initialise();
            $response = array('data' => '');

            /** @var TermCondition $terms */
            $terms = $this->em->getRepository('BackendAdminBundle:TermCondition')->findOneBy(array('enabled' => true), array('updatedAt' => 'DESC'));

            switch (strtolower(trim($lang))) {
                case 'en':
                    $response['data'] = htmlspecialchars_decode($terms->getDescriptionEN());
                    break;
                case 'es':
                    $response['data'] = htmlspecialchars_decode($terms->getDescriptionES());
                    break;
                default:
                    throw new Exception('Unrecognized language');
            }

            return new JsonResponse($response);
        } catch (Exception $ex) {
            return new JsonResponse(array('message' => $ex->getMessage()), JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }


    /**
     * @Rest\Get("/country", name="countries")
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
                $data[] = array('name' => $country->getName(), 'code' => $country->getCode(), 'locale' => $country->getLocale());
            }
            $response = array('message' => "", 'data' => $data);

            return new JsonResponse($response);
        } catch (Exception $ex) {
            return new JsonResponse(array('message' => $ex->getMessage()), JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }


    /**
     * @Rest\Get("/property", name="properties")
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

    public function getPropertiesAction()
    {
        try {
            $this->initialise();
            $data = array();

            $properties = $this->em->getRepository('BackendAdminBundle:Property')->findBy(array('enabled' => true), array('code' => 'ASC'));
            /** @var Property $property */
            foreach ($properties as $property) {
                $data[] = array(
                    'code' => $property->getCode(), 'name' => $property->getName(),
                    'address' => $property->getAddress(), 'property_type_id' => $property->getPropertyType()->getId(),
                    'sector_id' => $property->getComplexSector()->getId(), 'player_id' => $property->getTeamCorrelative());
            }
            $response = array('message' => "", 'data' => $data);

            return new JsonResponse($response);
        } catch (Exception $ex) {
            return new JsonResponse(array('message' => $ex->getMessage()), JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }


    /**
     * @Rest\Get("/property/{code}", name="property")
     *
     * @SWG\Parameter(
     *     name="code", in="path", type="string",
     *     description="The code of the property.",
     * )
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
            $data = array(
                'code' => $property->getCode(), 'name' => $property->getName(),
                'address' => $property->getAddress(), 'property_type_id' => $property->getPropertyType()->getId(),
                'sector_id' => $property->getComplexSector()->getId(), 'teamCorrelative' => $property->getTeamCorrelative());

            $response = array('message' => "", 'data' => $data);

            return new JsonResponse($response);
        } catch (Exception $ex) {
            return new JsonResponse(array('message' => $ex->getMessage()), JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }


    /**
     * @Rest\Get("/inbox/{page}", name="inbox")
     *
     * @SWG\Parameter(
     *     name="page", in="path", type="string",
     *     description="The code of the property.",
     * )
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
     * @SWG\Tag(name="User")
     */

    public function getInboxAction($page)
    {
        try {
            $this->initialise();
            $data = array();

            $notifications = $this->em->getRepository('BackendAdminBundle:UserNotification')->getApiInbox($this->getUser(), $page);

            $ticketIds = array();
            $commentsReplies = array();
            /** @var UserNotification $notification */
            foreach ($notifications as $notification) {
                $id = $notification->getTicket()->getId();
                $ticketIds[] = $id;
                $commentsReplies[$id] = 0;
            }
            $ticketIds = array_unique($ticketIds);

            $preComments = $this->em->getRepository('BackendAdminBundle:TicketComment')->getApiRepliesQuantities($ticketIds);
            /** @var TicketComment $comment */
            foreach ($preComments as $comment) {
                $commentsReplies[$comment->getTicket()->getId()] += 1;
            }

            $date_utc = new \DateTime("now", new \DateTimeZone("UTC"));

            /** @var UserNotification $notification */
            foreach ($notifications as $notification) {
                $data[] = array(
                    'avatar_path' => $notification->getSentBy()->getAvatarPath(),
                    'username' => $notification->getSentBy()->getUsername(),
                    'role' => $notification->getSentBy()->getRole()->getName(),
                    'user_notification' => array(
                        'description' => $notification->getDescription(),
                        'notification_type' => $notification->getNotificationType()->getName()),
                    'replies_quantity' => $commentsReplies[$notification->getTicket()->getId()],
                    'timestamp' => $date_utc->format(\DateTime::RFC850),
                    'notification_type' => $notification->getNotificationType()->getName(),
                    'notification_notice' => $notification->getNotice(),
                );
            }

            $response = array('message' => "", 'data' => $data);

            return new JsonResponse($response);
        } catch (Exception $ex) {
            return new JsonResponse(array('message' => $ex->getMessage()), JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }


    /**
     * @Rest\Get("/ticketCategory/{property_id}/{complex_id}", name="ticket_categories")
     *
     * @SWG\Response(
     *     response=200,
     *     description="Returns the list of ticket categories for the filter.",
     *     @SWG\Schema (
     *          @SWG\Property(
     *              property="data", type="array",
     *              @SWG\Items(
     *                  @SWG\Property( property="id", type="integer", description="Category ID", example="1 ),
     *                  @SWG\Property( property="name", type="string", description="Name of the category", example="Category" ),
     *                  @SWG\Property( property="icon_url", type="string", description="URL for the category's icon", example="/icons/1.jpg" ),
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
     * @SWG\Tag(name="User")
     */

    public function getTicketCategoriesAction($property_id, $complex_id)
    {
        try {
            $this->initialise();
            $data = array();

            $categories = $this->em->getRepository('BackendAdminBundle:Ticket')->getApiTicketCategories($property_id, $complex_id);

            /** @var TicketCategory $category */
            foreach ($categories as $category) {
                $data[] = array(
                    'category_id' => $category->getId(),
                    'category_name' => $category->getName(),
                    'icon_url' => "",
                );
            }

            $response = array('message' => "", 'data' => $data);

            return new JsonResponse($response);
        } catch (Exception $ex) {
            return new JsonResponse(array('message' => $ex->getMessage()), JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

}