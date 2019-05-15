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
use Backend\AdminBundle\Entity\TenantContract;
use Backend\AdminBundle\Entity\TermCondition;
use Backend\AdminBundle\Entity\Ticket;
use Backend\AdminBundle\Entity\TicketCategory;
use Backend\AdminBundle\Entity\TicketComment;
use Backend\AdminBundle\Entity\TicketStatus;
use Backend\AdminBundle\Entity\TicketType;
use Backend\AdminBundle\Entity\User;
use Backend\AdminBundle\Entity\UserNotification;
use Backend\AdminBundle\Entity\Device;

use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\FOSRestController;
use Swagger\Annotations as SWG;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Translation\Translator;


//entities


/**
 * Class RestController
 *
 * @Route("/api/v1")
 *
 */
class RestController extends FOSRestController
{
    protected $em;
    /** @var Translator $translator */
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
     * @Rest\Get("/v1", name="")
     */
    public function getV1Action()
    {
        $this->initialise();

        $property = $this->em->getRepository('BackendAdminBundle:Property')->findOneBy(array('enabled' => true, 'id' => 1));

        return new JsonResponse(array(
            'message' => "",
            'user' => $this->getUser()->getUsername(),
            'property' => $property->getId()
        ));

    }


    /*DEVICE FUNCTIONS*/

    /**
     * @Rest\Post("/app/deviceSetToken", name="device_set_token")
     *
     * @SWG\Parameter( name="token", in="body", type="string", description="the token to be saved", schema={} )
     * @SWG\Parameter( name="phone_id", in="body", type="string", description="apple uses UDID, Android uses UUID", schema={} )
     * @SWG\Parameter( name="platform", in="body", type="string", description="Specifies the platform iOS or Android", schema={} )
     *
     * @SWG\Parameter( name="app_version", in="query", type="string", description="The version of the app." )
     * @SWG\Parameter( name="code_version", in="query", type="string", description="The version of the code." )
     * @SWG\Parameter( name="language", in="query", type="string", description="The language being used (either en or es)." )
     * @SWG\Parameter( name="time_offset", in="query", type="string", description="Time difference with respect to GMT time." )
     *
     * @SWG\Response(
     *     response=200,
     *     description="CREATE OR UPDATE a device with token on the server.",
     *     @SWG\Schema (
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
     * @SWG\Tag(name="App")
     */

    public function postDeviceSetTokenAction(Request $request)
    {
        try {
            $this->initialise();
            $token = trim($request->get('token'));
            $phoneID = trim($request->get('phone_id'));
            $platform = trim($request->get('platform'));

            /*
            $this->get("services")->blameOnMe($tenant, "update");

            $this->em->persist($tenant);
            $this->em->flush();
            */

            $device = $this->em->getRepository('BackendAdminBundle:Device')->findOneByPhoneId($phoneID);
            $gtmNow = gmdate("Y-m-d H:i:s");

            if($device){
                ///IS AN UPDATE

                $device->setTokenPush($token);
                $device->setTokenUpdatedAt(new \DateTime($gtmNow));
                $device->setUpdatedAt(new \DateTime($gtmNow));


            }
            else{
                ///CREATE
                $device = new Device();

                $device->setPhoneId($phoneID);
                $device->setCreatedAt(new \DateTime($gtmNow));
                $device->setUpdatedAt(new \DateTime($gtmNow));
                $device->setEnabled(1);
                $device->setTokenPush($token);
                $device->setTokenUpdatedAt(new \DateTime($gtmNow));
                $device->setPlatform($platform);

                $this->get("services")->blameOnMe($device, "create");
                $this->get("services")->blameOnMe($device, "update");


            }

            $this->em->persist($device);
            $this->em->flush();

            return new JsonResponse(array(
                'message' => "".$device->getId(),
            ));
        } catch (Exception $ex) {
            return new JsonResponse(array('message' => $ex->getMessage()), JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
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
     * @Rest\Post("/forgotPassword", name="forgot_password")
     *
     * @SWG\Parameter( name="email", in="body", type="string", description="The email of the user.", schema={} )
     *
     * @SWG\Parameter( name="app_version", in="query", type="string", description="The version of the app." )
     * @SWG\Parameter( name="code_version", in="query", type="string", description="The version of the code." )
     * @SWG\Parameter( name="language", in="query", type="string", description="The language being used (either en or es)." )
     * @SWG\Parameter( name="time_offset", in="query", type="string", description="Time difference with respect to GMT time." )
     *
     * @SWG\Response(
     *     response=200,
     *     description="Sends an email to the corresponding user to recover his/her account.",
     *     @SWG\Schema (
     *          @SWG\Property( property="message", type="string", example="" )
     *      )
     * )
     *
     * @SWG\Response(
     *     response=401, description="Invalid email.",
     *     @SWG\Schema (
     *          @SWG\Property(property="data", type="string", example="" ),
     *          @SWG\Property( property="message", type="string", example="Invalid email." )
     *     )
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

    public function postForgotPasswordAction(Request $request, UserPasswordEncoderInterface $encoder)
    {
        try {
            $this->initialise();
            $email = strtolower(trim($request->get('email')));
            $lang = strtolower(trim($request->get('language')));

            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                return new JsonResponse(array('message' => 'Invalid email format.'), JsonResponse::HTTP_UNAUTHORIZED);
            }

            $user = $this->em->getRepository('BackendAdminBundle:User')->findOneBy(array('enabled' => true, 'email' => $email));

            if ($user == null) {
                return new JsonResponse(array('message' => 'Invalid email.'), JsonResponse::HTTP_UNAUTHORIZED);
            }

            $pass = $this->random_str(32);

            $this->translator->setLocale($lang);
            $subject = $this->translator->trans('mail.forgot_password_subject');
            $bodyHtml = $this->translator->trans(
                'mail.forgot_password_body',
                ['%password%' => $pass]
            );

            $user->setPlainPassword($pass);
            $user->setPassword($encoder->encodePassword($user, $pass));
            $this->get("services")->blameOnMe($user, "update");
            $this->em->persist($user);
            $this->em->flush();

            $message = $this->get('services')->generalTemplateMail($subject, $user->getEmail(), $bodyHtml);
            $this->sendEmail($message);

            return new JsonResponse(array(
                'message' => "" . $user->getId(),
            ));
        } catch (Exception $ex) {
            return new JsonResponse(array('message' => $ex->getMessage()), JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }


    /**
     * @Rest\Post("/register", name="register")
     *
     * @SWG\Parameter( name="name", in="body", type="string", description="The name of the user.", schema={} )
     * @SWG\Parameter( name="mobile_phone", in="body", type="string", description="The mobile phone of the user.", schema={} )
     * @SWG\Parameter( name="country_code", in="body", type="string", description="The country code of the user.", schema={} )
     * @SWG\Parameter( name="email", in="body", type="string", description="The email of the user.", schema={} )
     * @SWG\Parameter( name="password", in="body", type="string", description="The password of the user.", schema={} )
     *
     * @SWG\Parameter( name="app_version", in="query", type="string", description="The version of the app." )
     * @SWG\Parameter( name="code_version", in="query", type="string", description="The version of the code." )
     * @SWG\Parameter( name="language", in="query", type="string", description="The language being used (either en or es)." )
     * @SWG\Parameter( name="time_offset", in="query", type="string", description="Time difference with respect to GMT time." )
     *
     * @SWG\Response(
     *     response=200,
     *     description="Creates a successfull user account.",
     *     @SWG\Schema (
     *          @SWG\Property( property="message", type="string", example="" )
     *      )
     * )
     *
     * @SWG\Response(
     *     response=409, description="User already exists conflict.",
     *     @SWG\Schema (
     *          @SWG\Property(property="data", type="string", example="" ),
     *          @SWG\Property( property="message", type="string", example="User already exist." )
     *     )
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

    public function postRegisterAction(Request $request, UserPasswordEncoderInterface $encoder)
    {
        try {
            $this->initialise();
            $name = trim($request->get('name'));
            $mobilePhone = trim($request->get('mobile_phone'));
            $countryCode = trim($request->get('country_code'));
            $email = strtolower(trim($request->get('email')));
            $password = $request->get('password');

            $lang = strtolower(trim($request->get('language')));

            // Some Validation
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                throw new \Exception("Invalid email format.");
            }

            if (!preg_match('/^\+?[0-9]+$/', $mobilePhone)) {
                throw new \Exception("Invalid phone format.");
            }

            $country = $this->em->getRepository('BackendAdminBundle:GeoCountry')->findOneBy(array('enabled' => true, 'code' => $countryCode));
            if ($country == null) {
                throw new \Exception("Invalid country code.");
            }

            // User existance
            $user = $this->em->getRepository('BackendAdminBundle:User')->findOneBy(array('enabled' => true, 'email' => $email));
            if ($user != null) {
                return new JsonResponse(array('message' => 'User already exists.'), JsonResponse::HTTP_CONFLICT);
            }

            $user = new User();

            $user->setName($name);
            $user->setMobilePhone($mobilePhone);
            $user->setGeoCountry($country);
            $user->setUsername($email);
            $user->setEmail($email);
            $user->setPlainPassword($password);
            $user->setPassword($encoder->encodePassword($user, $password));

            $this->get("services")->blameOnMe($user, "create");
            $this->get("services")->blameOnMe($user, "update");

            $this->em->persist($user);
            $this->em->flush();

            $this->translator->setLocale($lang);
            $subject = $this->translator->trans('mail.register_subject');
            $bodyHtml = "<b>".$this->translator->trans('mail.label_user')."</b> ".$user->getUsername()."<br/>";
            $bodyHtml .= "<b>".$this->translator->trans('mail.label_password')."</b> ".$password."<br/><br/>";
            $bodyHtml .= $this->translator->trans('mail.register_body');

            $message = $this->get('services')->generalTemplateMail($subject, $user->getEmail(), $bodyHtml);
            $this->sendEmail($message);

            return new JsonResponse(array(
                'message' => "" . $user->getId(),
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
     * @Rest\Post("/welcomePrivateKey", name="welcome_private_key")
     *
     * @SWG\Parameter( name="property_code", in="body", type="string", description="The code of the property.", schema={} )
     *
     * @SWG\Parameter( name="app_version", in="query", type="string", description="The version of the app." )
     * @SWG\Parameter( name="code_version", in="query", type="string", description="The version of the code." )
     * @SWG\Parameter( name="language", in="query", type="string", description="The language being used (either en or es)." )
     * @SWG\Parameter( name="time_offset", in="query", type="string", description="Time difference with respect to GMT time." )
     *
     * @SWG\Response(
     *     response=200,
     *     description="Creates an association between a Property and a User via the property code. Applicable to welcomePrivateKey, welcomeQR and welcomeInvite.",
     *     @SWG\Schema (
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

    public function postWelcomePrivateKeyAction(Request $request)
    {
        try {
            $this->initialise();
            $propertyCode = strtolower(trim($request->get('property_code')));
            $user = $this->getUser();

            /** @var Property $property */
            $property = $this->em->getRepository('BackendAdminBundle:Property')->findOneBy(array('enabled' => true, 'code' => $propertyCode));
            if ($property == null) {
                throw new \Exception("Invalid property code.");
            }

            $tenantRaw = $this->em->getRepository('BackendAdminBundle:TenantContract')->getApiWelcomePrivateKey($property);
            /** @var TenantContract $tenant */
            $tenant = $tenantRaw[0];

            $role = $this->em->getRepository('BackendAdminBundle:Role')->findOneById(4);

            $tenant->setUser($this->getUser());
            $tenant->setRole($role);
            $tenant->setIsOwner(true);

            $property->setOwner($this->getUser());

            $this->get("services")->blameOnMe($property, "update");
            $this->get("services")->blameOnMe($tenant, "update");

            $this->em->persist($tenant);
            $this->em->persist($property);
            $this->em->flush();

            return new JsonResponse(array(
                'message' => "" . $user->getId(),
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
     *                  @SWG\Property( property="type_id", type="integer", description="Property Type ID", example="1" ),
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
                array('enabled' => true, 'owner' => $this->getUser()),
                array('code' => 'ASC')
            );
            $total = $this->em->getRepository('BackendAdminBundle:Property')->countApiProperties($this->getUser());

            /** @var Property $property */
            foreach ($properties as $property) {
                $type = $property->getPropertyType();
                if ($type == null) {
                    $type = new PropertyType();
                }
                $complexSector = $property->getComplexSector();
                if ($complexSector == null) {
                    $complexSector = new ComplexSector();
                }

                $data[] = array(
                    'id' => $property->getId(),
                    'code' => $property->getCode(), 'name' => $property->getName(),
                    'address' => $property->getAddress(), 'type_id' => $type->getId(),
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
     *              @SWG\Property( property="type_id", type="integer", description="Property Type ID", example="1" ),
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
            $propertyResult = $this->em->getRepository('BackendAdminBundle:Property')->getApiProperty($code, $this->getUser());
            $property = $propertyResult[0];

            $type = $property->getPropertyType();
            if ($type == null) {
                $type = new PropertyType();
            }
            $complexSector = $property->getComplexSector();
            if ($complexSector == null) {
                $complexSector = new ComplexSector();
            }

            $data = array(
                'code' => $property->getCode(), 'name' => $property->getName(),
                'address' => $property->getAddress(), 'type_id' => $type->getId(),
                'sector_id' => $complexSector->getId(), 'teamCorrelative' => $property->getTeamCorrelative());

            return new JsonResponse(array('message' => "", 'data' => $data));
        } catch (Exception $ex) {
            return new JsonResponse(array('message' => $ex->getMessage()), JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }


    /**
     * @Rest\Get("/propertyDetail/{code}", name="property_detail")
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
     *              @SWG\Property( property="id", type="integer", description="ID", example="1" ),
     *              @SWG\Property( property="code", type="string", description="Code", example="101" ),
     *              @SWG\Property( property="name", type="string", description="Name", example="Casa Modelo" ),
     *              @SWG\Property( property="address", type="string", description="Address", example="1 Avenue des Champs-Elysees" ),
     *              @SWG\Property( property="type_id", type="integer", description="Property Type ID", example="1" ),
     *              @SWG\Property( property="is_owner", type="boolean", description="If it is owner", example="true" ),
     *              @SWG\Property( property="photos", type="array",
     *                  @SWG\Items(
     *                      @SWG\Property( property="url", type="string", description="URL of property photo", example="/photo.jpg" ),
     *                  )
     *              ),
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

    public function getPropertyDetailAction($code)
    {
        try {
            $this->initialise();

            /** @var Property $property */
            $propertyResult = $this->em->getRepository('BackendAdminBundle:Property')->getApiProperty($code, $this->getUser());
            $property = $propertyResult[0];

            $type = $property->getPropertyType();
            if ($type == null) {
                $type = new PropertyType();
            }

            $owner = $property->getOwner();
            if ($owner == null) {
                $owner = new User();
            }

            $data = array(
                'id' => $property->getId(),
                'code' => $property->getCode(), 'name' => $property->getName(),
                'address' => $property->getAddress(), 'type_id' => $type->getId(),
                'is_owner' => $owner->getId() == $this->getUser()->getId(),
                'photos' => array(),
            );

            return new JsonResponse(array('message' => "", 'data' => $data));
        } catch (Exception $ex) {
            return new JsonResponse(array('message' => $ex->getMessage()), JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @Rest\Post("/sendSMS", name="send_sms")
     *
     * @SWG\Parameter( name="property_code", in="body", type="string", description="The code of the property.", schema={} )
     *
     * @SWG\Parameter( name="app_version", in="query", type="string", description="The version of the app." )
     * @SWG\Parameter( name="code_version", in="query", type="string", description="The version of the code." )
     * @SWG\Parameter( name="language", in="query", type="string", description="The language being used (either en or es)." )
     * @SWG\Parameter( name="time_offset", in="query", type="string", description="Time difference with respect to GMT time." )
     *
     * @SWG\Response(
     *     response=200,
     *     description="Sends successfully a sms to the user.",
     *     @SWG\Schema (
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

    public function postSendSmsAction(Request $request)
    {
        try {
            $this->initialise();
            $propertyCode = strtolower(trim($request->get('property_code')));
            $user = $this->getUser();

            $property = $this->em->getRepository('BackendAdminBundle:Property')->findOneBy(array('enabled' => true, 'code' => $propertyCode));
            if ($property == null) {
                throw new \Exception("Invalid property code.");
            }

            // ToDo: Still pending info.

            $msg = $this->get('services')->serviceSendSMS("hello there monkey", "+50241550669");

            return new JsonResponse(array(
                'message' => "" . $user->getId(),
            ));
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
     *                      @SWG\Property( property="type", type="string", description="Type of Notification", example="accept_invitation" ),
     *                  ),
     *                  @SWG\Property( property="replies_quantity", type="integer", description="Quantity of replies for the associated ticket", example="10" ),
     *                  @SWG\Property( property="timestamp", type="string", description="Timestamp UTC formatted with RFC850", example="Monday, 15-Aug-05 15:52:01 UTC" ),
     *                  @SWG\Property( property="type", type="string", description="Name of the type of notification", example="Type1" ),
     *                  @SWG\Property( property="notice", type="string", description="Notification notice", example="Notice" ),
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
                $user = $notification->getCreatedBy();
                if ($user == null) {
                    $user = new User();
                }
                $type = $notification->getNotificationType();
                if ($type == null) {
                    $type = new NotificationType();
                }
                $ticket = $notification->getTicket();
                if ($ticket == null) {
                    $ticket = new Ticket();
                }


                $data[] = array(
                    'avatar_path' => $user->getAvatarPath(),
                    'username' => $user->getUsername(),
                    'role' => (($lang == 'en') ? $user->getRole()->getName() : $user->getRole()->getNameES()),
                    'user_notification' => array(
                        'description' => $notification->getDescription(),
                        'type' => (($lang == 'en') ? $type->getNameEN() : $type->getNameES())),
                    'replies_quantity' => (array_key_exists($ticket->getId(), $commentsReplies)) ? $commentsReplies[$ticket->getId()] : 0,
                    'timestamp' => $user->format(\DateTime::RFC850),
                    'type' => (($lang == 'en') ? $type->getNameEN() : $type->getNameES()),
                    'notice' => $notification->getNotice(),
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
     *                  @SWG\Property( property="id", type="string", description="Ticket ID", example="1" ),
     *                  @SWG\Property( property="type_id", type="string", description="Ticket type ID", example="1" ),
     *                  @SWG\Property( property="type_name", type="string", description="Ticket type name", example="TicketTypeName" ),
     *                  @SWG\Property( property="status", type="string", description="Ticket status", example="Status" ),
     *                  @SWG\Property( property="title", type="string", description="Ticket title", example="TicketTile" ),
     *                  @SWG\Property( property="description", type="string", description="Ticket description", example="Lorem ipsum." ),
     *                  @SWG\Property( property="is_public", type="boolean", description="Is ticket public?", example="true" ),
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
                $type = $ticket->getTicketType();
                if ($type == null) {
                    $type = new TicketType();
                }
                $status = $ticket->getTicketStatus();
                if ($status == null) {
                    $status = new TicketStatus();
                }
                $user = $ticket->getCreatedBy();
                if ($user == null) {
                    $user = new User();
                }

                $reservation = $ticket->getCommonAreaReservation();
                if ($reservation == null) {
                    $reservation = new CommonAreaReservation();
                }
                $reservationStatus = $reservation->getCommonAreaResevationStatus();
                if ($reservationStatus == null) {
                    $reservationStatus = new CommonAreaReservationStatus();
                }
                $commonArea = $reservation->getCommonArea();
                if ($commonArea == null) {
                    $commonArea = new CommonArea();
                }

                $data[] = array(
                    'id' => $ticket->getId(),
                    'type_id' => $type->getId(),
                    'type_name' => $type->getName(),
                    'status' => (($lang == 'en') ? $status->getNameEN() : $status->getNameES()),
                    'title' => $ticket->getTitle(),
                    'description' => $ticket->getDescription(),
                    'is_public' => $ticket->getIsPublic(),
                    'username' => $user->getUsername(),
                    'role' => (($lang == 'en') ? $user->getRole()->getName() : $user->getRole()->getNameES()),
                    'timestamp' => $ticket->getCreatedAt()->format(\DateTime::RFC850),
                    'followers_quantity' => (array_key_exists($ticket->getId(), $followers)) ? $followers[$ticket->getId()] : 0,
                    'common_area' => array(
                        "id" => $commonArea->getId(),
                        "name" => $commonArea->getName(),
                        "status" => (($lang == 'en') ? $reservationStatus->getNameEN() : $reservationStatus->getNameES()),
                        "reservation_from" => $reservation->getReservationDateFrom()->format(\DateTime::RFC850),
                        "reservation_to" => $reservation->getReservationDateTo()->format(\DateTime::RFC850),
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
     *                  @SWG\Property( property="title", type="string", description="Ticket title", example="TicketTile" ),
     *                  @SWG\Property( property="username", type="string", description="Ticket's creator username", example="admin" ),
     *                  @SWG\Property( property="status", type="string", description="Ticket status", example="Status" ),
     *                  @SWG\Property( property="timestamp", type="string", description="Ticket created timestamp UTC formatted with RFC850", example="Monday, 15-Aug-05 15:52:01 UTC" ),
     *                  @SWG\Property( property="followers_quantity", type="string", description="Amount of followers for the ticket", example="2" ),
     *                  @SWG\Property( property="comments_quantity", type="string", description="Ammount of comments for the ticket", example="3" ),
     *                  @SWG\Property( property="description", type="string", description="Ticket description", example="Lorem ipsum." ),
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

            $status = $ticket->getTicketStatus();
            if ($status == null) {
                $status = new TicketStatus();
            }
            $ticketUser = $ticket->getCreatedBy();
            if ($ticketUser == null) {
                $ticketUser = new User();
            }

            $data = array(
                'title' => $ticket->getTitle(),
                'username' => $ticketUser->getUsername(),
                'status' => (($lang == 'en') ? $status->getNameEN() : $status->getNameES()),
                'timestamp' => $ticket->getCreatedAt()->format(\DateTime::RFC850),
                'followers_quantity' => (array_key_exists($ticket->getId(), $followersCount)) ? $followersCount[$ticket->getId()] : 0,
                'comments_quantity' => (array_key_exists($ticket->getId(), $commentsCount)) ? $commentsCount[$ticket->getId()] : 0,
                'description' => $ticket->getDescription(),
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
                    'like' => $likeUser->getUsername(),
                    'comment' => $comment->getCommentDescription(),
                    'icon_url' => "",
                );
            }


            if ($ticket->getCommonAreaReservation() != null) {
                $reservation = $ticket->getCommonAreaReservation();
                if ($reservation == null) {
                    $reservation = new CommonAreaReservation();
                }
                $reservationStatus = $reservation->getCommonAreaResevationStatus();
                if ($reservationStatus == null) {
                    $reservationStatus = new CommonAreaReservationStatus();
                }
                $commonArea = $reservation->getCommonArea();
                if ($commonArea == null) {
                    $commonArea = new CommonArea();
                }

                $data['common_area'] = array(
                    "id" => $commonArea->getId(),
                    "name" => $commonArea->getName(),
                    "status" => (($lang == 'en') ? $reservationStatus->getNameEN() : $reservationStatus->getNameES()),
                    "reservation_from" => $reservation->getReservationDateFrom()->format(\DateTime::RFC850),
                    "reservation_to" => $reservation->getReservationDateTo()->format(\DateTime::RFC850),
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
     * @Rest\Post("/ticket", name="create_ticket")
     *
     * @SWG\Parameter( name="title", in="body", type="string", description="The title of the ticket.", schema={} )
     * @SWG\Parameter( name="description", in="body", type="string", description="The description of the ticket.", schema={} )
     * @SWG\Parameter( name="photos", in="body", type="array", description="The photos of the ticket.", schema={} )
     * @SWG\Parameter( name="solution", in="body", type="boolean", description="Is the ticket a solution.", schema={} )
     * @SWG\Parameter( name="is_public", in="body", type="boolean", description="Is the ticket public or private.", schema={} )
     * @SWG\Parameter( name="category_id", in="body", type="integer", description="The category ID of the ticket.", schema={} )
     * @SWG\Parameter( name="sector_id", in="body", type="integer", description="The complex sector ID of the ticket.", schema={} )
     * @SWG\Parameter( name="property_id", in="body", type="integer", description="The property ID of the ticket.", schema={} )
     *
     * @SWG\Parameter( name="app_version", in="query", type="string", description="The version of the app." )
     * @SWG\Parameter( name="code_version", in="query", type="string", description="The version of the code." )
     * @SWG\Parameter( name="language", in="query", type="string", description="The language being used (either en or es)." )
     * @SWG\Parameter( name="time_offset", in="query", type="string", description="Time difference with respect to GMT time." )
     *
     * @SWG\Response(
     *     response=200,
     *     description="Creates a successfull user account.",
     *     @SWG\Schema (
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

    public function postTicketAction(Request $request)
    {
        try {
            $this->initialise();
            $title = $request->get('title');
            $description = $request->get('description');
            $photos = $request->get('photos'); // ToDo: This is gonna be cardiacation cause dunoo
            $solution = $request->get('solution');
            $isPublic = $request->get('is_public');
            $categoryId = $request->get('category_id');
            $complexSectorId = $request->get('sector_id');
            $propertyId = $request->get('property_id');
            $commonAreaReservationId = $request->get('common_area_reservation_id');
            $tenantContractId = $request->get('tenant_contract_id');

            // Required parameter
            $category = $this->em->getRepository('BackendAdminBundle:TicketCategory')->findOneBy(array('enabled' => true, 'id' => $categoryId));
            if ($category == null) {
                throw new \Exception("Invalid category ID.");
            }

            // Required parameter
            $complexSector = $this->em->getRepository('BackendAdminBundle:ComplexSector')->findOneBy(array('enabled' => true, 'id' => $complexSectorId));
            if ($complexSector == null) {
                throw new \Exception("Invalid complex sector ID.");
            }

            // Required parameter
            $property = $this->em->getRepository('BackendAdminBundle:Property')->findOneBy(array('enabled' => true, 'id' => $propertyId));
            if ($property == null) {
                throw new \Exception("Invalid property ID.");
            }

            // Optional parameter
            $commonAreaReservation = $this->em->getRepository('BackendAdminBundle:CommonAreaReservation')->findOneBy(array('enabled' => true, 'id' => $commonAreaReservationId));

            // Required parameter
            $tenantContract = $this->em->getRepository('BackendAdminBundle:TenantContract')->findOneBy(array('enabled' => true, 'id' => $tenantContractId));
            if ($tenantContract == null) {
                throw new \Exception("Invalid tenant contract ID.");
            }

            // ToDo: to be finished.

            // ToDo: adds an entity to TicketStatusLog, the same for closeTicket.

            return new JsonResponse(array(
                'message' => "",
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
     *                          @SWG\Property( property="question", type="string", description="Poll question", example="Question" ),
     *                          @SWG\Property( property="file_photo", type="string", description="Poll file photo", example="/photo.jpg" ),
     *                          @SWG\Property( property="type", type="string", description="Poll question type", example="Type" ),
     *                          @SWG\Property( property="options", type="array",
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
                foreach ($answers[$question->getId()] as $answer) {
                    $options[] = array('option' => $answer->getQuestionOption());
                }

                $data['questions'][] = array(
                    'question' => $question->getQuestion(),
                    'file_photo' => $question->getPollFilePhoto(),
                    'type' => $question->getPollQuestionType()->getName(),
                    'options' => $options,
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
     *                  @SWG\Property( property="type", type="string", description="Type of the common area", example="Description" ),
     *                  @SWG\Property( property="photos", type="array",
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
                    $commonAreaPhotos[] = array('url' => $photo->getPhotoPath());
                }

                $commonAreaType = $commonArea->getCommonAreaType();
                if ($commonAreaType == null) {
                    $commonAreaType = new CommonAreaType();
                }

                $data[] = array(
                    'id' => $commonArea->getId(),
                    'name' => $commonArea->getName(),
                    'description' => $commonArea->getDescription(),
                    'type' => $commonAreaType->getName(),
                    'photos' => $commonAreaPhotos,
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
     *                          @SWG\Property( property="status", type="string", description="Reservation status for the common area", example="status" ),
     *                          @SWG\Property( property="date_from", type="string", description="Reservation date from UTC formatted with RFC850", example="Monday, 15-Aug-05 15:52:01 UTC" ),
     *                          @SWG\Property( property="date_to", type="string", description="Reservation date to UTC formatted with RFC850", example="Monday, 16-Aug-05 15:52:01 UTC" ),
     *                      )
     *                  ),
     *                  @SWG\Property( property="common_area_availability", type="array",
     *                      @SWG\Items(
     *                          @SWG\Property( property="week_day_range_start", type="integer", description="The day of the week that the range starts", example="1" ),
     *                          @SWG\Property( property="week_day_range_finish", type="integer", description="The day of the week that the range ends", example="5" ),
     *                          @SWG\Property( property="week_day", type="integer", description="The day of the week", example="2" ),
     *                          @SWG\Property( property="hour_from", type="integer", description="From hour", example="10" ),
     *                          @SWG\Property( property="hour_to", type="integer", description="To hour", example="15" ),
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
            $data = array('reservations' => array(), 'availabilities' => array());

            $availabilities = $this->em->getRepository('BackendAdminBundle:CommonAreaAvailability')->getApiCommonAreaAvailability($common_area_id);
            $reservations = $this->em->getRepository('BackendAdminBundle:CommonAreaReservation')->getApiCommonAreaAvailability($common_area_id);

            /** @var CommonAreaReservation $reservation */
            foreach ($reservations as $reservation) {
                $status = $reservation->getCommonAreaResevationStatus();
                if ($status == null) {
                    $status = new CommonAreaReservationStatus();
                }

                $data['reservations'][] = array(
                    'status' => ($lang == 'en') ? $status->getNameEN() : $status->getNameES(),
                    'date_from' => $reservation->getReservationDateFrom()->format(\DateTime::RFC850),
                    'date_to' => $reservation->getReservationDateTo()->format(\DateTime::RFC850),
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
            'last_page' => ceil($total / 10)
        );
    }

    private function getArrayOfIds($items)
    {
        $ids = array();

        foreach ($items as $item) {
            $ids[] = $item->getId();
        }

        return array_unique($ids);
    }

    private function random_str($length, $keyspace = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ')
    {
        $pieces = [];
        $max = mb_strlen($keyspace, '8bit') - 1;
        for ($i = 0; $i < $length; ++$i) {
            $pieces []= $keyspace[random_int(0, $max)];
        }
        return implode('', $pieces);
    }

}