<?php

namespace Backend\AdminBundle\Controller;

use Backend\AdminBundle\Entity\CommonArea;
use Backend\AdminBundle\Entity\CommonAreaAvailability;
use Backend\AdminBundle\Entity\CommonAreaPhoto;
use Backend\AdminBundle\Entity\CommonAreaReservation;
use Backend\AdminBundle\Entity\CommonAreaReservationStatus;
use Backend\AdminBundle\Entity\CommonAreaType;
use Backend\AdminBundle\Entity\Complex;
use Backend\AdminBundle\Entity\ComplexFaq;
use Backend\AdminBundle\Entity\ComplexSector;
use Backend\AdminBundle\Entity\Device;
use Backend\AdminBundle\Entity\GeoCountry;
use Backend\AdminBundle\Entity\NotificationType;
use Backend\AdminBundle\Entity\Poll;
use Backend\AdminBundle\Entity\PollQuestion;
use Backend\AdminBundle\Entity\PollQuestionOption;
use Backend\AdminBundle\Entity\PollTenantAnswer;
use Backend\AdminBundle\Entity\Property;
use Backend\AdminBundle\Entity\PropertyContract;
use Backend\AdminBundle\Entity\PropertyPhoto;
use Backend\AdminBundle\Entity\PropertyType;
use Backend\AdminBundle\Entity\TenantContract;
use Backend\AdminBundle\Entity\TermCondition;
use Backend\AdminBundle\Entity\Ticket;
use Backend\AdminBundle\Entity\TicketCategory;
use Backend\AdminBundle\Entity\TicketComment;
use Backend\AdminBundle\Entity\TicketFilePhoto;
use Backend\AdminBundle\Entity\TicketStatus;
use Backend\AdminBundle\Entity\TicketStatusLog;
use Backend\AdminBundle\Entity\TicketType;
use Backend\AdminBundle\Entity\User;
use Backend\AdminBundle\Entity\UserNotification;
use Backend\AdminBundle\Repository\CommonAreaPhotoRepository;
use Backend\AdminBundle\Repository\CommonAreaRepository;
use Backend\AdminBundle\Repository\ComplexFaqRepository;
use Backend\AdminBundle\Repository\ComplexRepository;
use Backend\AdminBundle\Repository\NotificationTypeRepository;
use Backend\AdminBundle\Repository\PropertyRepository;
use Backend\AdminBundle\Repository\TenantContractRepository;
use Backend\AdminBundle\Repository\TicketCategoryRepository;
use Backend\AdminBundle\Repository\UserNotificationRepository;
use Backend\AdminBundle\Repository\UserRepository;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\FOSRestController;
use Swagger\Annotations as SWG;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\File\File as FileObject;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Translation\Translator;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Validator\ValidatorInterface;


//entities


/**
 * Class RestController
 *
 * @Route("/api")
 *
 */
class RestController extends FOSRestController
{
    const UPLOADS_FOLDER = '/var/www/uploads'; // ToDo: Change to proper path
    const TENANT_ROLE_ID = 4;
    const COMMON_AREA_RESERVATION_STATUS_ID = 1;
    const TICKET_STATUS_OPEN_ID = 1;
    const TICKET_STATUS_CLOSE_ID = 1; // ToDo: Change the proper ticketStatus CLOSE.
    const INVITATION_NOTIFICATION_TYPE_ID = 3;

    const USER_ADMIN_ROLE_ID = 1;

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
     * @Rest\Get("/v1/test", name="")
     */
    public function getV1Action(Request $request)
    {
        $this->initialise();

        $pid = trim($request->get('pid'));

        $property = $this->em->getRepository('BackendAdminBundle:Property')->findOneBy(array('enabled' => true, 'id' => $pid));

        if ($property == null) {
            return new JsonResponse(array());
        }

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
     * @SWG\Parameter( name="code_version", in="query", required=true, type="string", description="The version of the code." )
     * @SWG\Parameter( name="language", in="query", required=true, type="string", description="The language being used (either en or es)." )
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

            if ($device) {
                ///IS AN UPDATE

                $device->setTokenPush($token);
                $device->setTokenUpdatedAt(new \DateTime($gtmNow));
                $device->setUpdatedAt(new \DateTime($gtmNow));


            } else {
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
                'message' => "" . $device->getId(),
            ));
        } catch (Exception $ex) {
            return new JsonResponse(array('message' => $ex->getMessage()), JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Logins the user to the app.
     *
     * This call takes the username and password, validates them, and if they are correctly, it will return a token to be used in all subsecuent requests.
     *
     * @Rest\Post("/login_check", name="login_check")
     *
     * @SWG\Parameter( name="Content-Type", in="header", required=true, type="string", default="application/json" )
     *
     * @SWG\Parameter( name="_username", in="body", type="string", description="The username", schema={} )
     * @SWG\Parameter( name="_password", in="body", type="string", description="The password", schema={} )
     *
     * @SWG\Response(
     *     response=200,
     *     description="User was logged in successfully. The HTTP Header for any POST request must be application/x-www-form-urlencoded."
     * )
     * @SWG\Response(
     *     response=401,
     *     description="Invalid user and password"
     * )*
     *
     * @SWG\Response(
     *     response=500,
     *     description="User was not logged in successfully"
     * )
     *
     *
     * @SWG\Tag(name="User")
     */
    public function getLoginCheckAction(Request $request)
    {
        try {
            $this->initialise();

            if (!$request->headers->has('Content-Type')) {
                throw new \Exception("Missing Content-Type header.");
            }

            $username = $request->get('username');
            $password = $request->get('password');// hash('sha512', $data);

            $user = $this->em->getRepository('BackendAdminBundle:User')->findOneBy(array("username" => $username, "enabled" => 1));

            if (!$user) {
                $code = 401;
                $error = true;
                $message = "No user found- Error";
            } else {
                $factory = $this->get('security.encoder_factory');
                $salt = $user->getSalt();
                $encoder = $factory->getEncoder($user);

                if (!$encoder->isPasswordValid($user->getPassword(), $password, $salt)) {
                    $code = 401;
                    $error = true;
                    $message = "Invalid user and password- Error";
                } else {
                    //VALID USER AND PASSWORD GENERATE TOKEN
                    $jwtManager = $this->container->get('lexik_jwt_authentication.jwt_manager');
                    return new JsonResponse(['token' => $jwtManager->create($user)]);
                }
            }

            $response = [
                'code' => $code,
                'error' => $error,
                'data' => $message,
            ];

            return new JsonResponse($response);

        } catch (Exception $ex) {
            $code = 500;
            $error = true;
            $message = "An error has occurred trying to retrieve the user - Error: {$ex->getMessage()}";
            $response = [
                'code' => $code,
                'error' => $error,
                'data' => $message,
            ];

            return new JsonResponse($response);
        }


    }


    /**
     * Gets the terms and conditions.
     *
     * Returns an HTML with the terms and conditions inside the data property based on the language.
     *
     * @Rest\Get("/termsConditions", name="termsConditions")
     *
     * @SWG\Parameter( name="Content-Type", in="header", type="string", default="application/json" )
     *
     * @SWG\Parameter( name="app_version", in="query", required=true, type="string", description="The version of the app." )
     * @SWG\Parameter( name="code_version", in="query", required=true, type="string", description="The version of the code." )
     * @SWG\Parameter( name="language", in="query", required=true, type="string", description="The language being used (either en or es)." )
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
                'message' => "termsConditions",
                'data' => ($lang == 'en') ? htmlspecialchars_decode($terms->getDescriptionEN()) : htmlspecialchars_decode($terms->getDescriptionES())
            ));
        } catch (Exception $ex) {
            return new JsonResponse(array('message' => $ex->getMessage()), JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }


    /**
     * Forgot Password mechanism for users.
     *
     * Takes the username (email) and resets the password with a 32 chars lenght random password, which is sent by email to the user.
     *
     * @Rest\Post("/forgotPassword", name="forgotPassword", options={})
     *
     * @SWG\Parameter( name="Content-Type", in="header", required=true, type="string", default="application/json" )
     *
     * @SWG\Parameter( name="email", in="body", required=true, type="string", description="The email of the user.", schema={} )
     *
     * @SWG\Parameter( name="app_version", in="query", required=true, type="string", description="The version of the app." )
     * @SWG\Parameter( name="code_version", in="query", required=true, type="string", description="The version of the code." )
     * @SWG\Parameter( name="language", in="query", required=true, type="string", description="The language being used (either en or es)." )
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
    public function postForgotPasswordAction(Request $request)
    {
        try {
            $this->initialise();

            if (!$request->headers->has('Content-Type')) {
                throw new \Exception("Missing Content-Type header.");
            }

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

            $encoder = $this->get('security.password_encoder');

            $user->setPlainPassword($pass);
            $user->setPassword($encoder->encodePassword($user, $pass));
            $this->get("services")->blameOnMe($user, "update");
            $this->em->persist($user);
            $this->em->flush();

            $message = $this->get('services')->generalTemplateMail($subject, $user->getEmail(), $bodyHtml);

            return new JsonResponse(array(
                'message' => "forgotPassword",
            ));
        } catch (Exception $ex) {
            return new JsonResponse(array('message' => $ex->getMessage()), JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }


    /**
     * User register.
     *
     * Takes the body and creates a user with this. It sents a welcome email to the new user.
     *
     * @Rest\Post("/register", name="register")
     *
     * @SWG\Parameter( name="Content-Type", in="header", required=true, type="string", default="application/json" )
     *
     * @SWG\Parameter( name="name", in="body", required=true, type="string", description="The name of the user.", schema={} )
     * @SWG\Parameter( name="mobile_phone", in="body", required=true, type="string", description="The mobile phone of the user.", schema={} )
     * @SWG\Parameter( name="country_code", in="body", required=true, type="string", description="The country code of the user.", schema={} )
     * @SWG\Parameter( name="email", in="body", required=true, type="string", description="The email of the user.", schema={} )
     * @SWG\Parameter( name="password", in="body", required=true, type="string", description="The password of the user.", schema={} )
     *
     * @SWG\Parameter( name="app_version", in="query", required=true, type="string", description="The version of the app." )
     * @SWG\Parameter( name="code_version", in="query", required=true, type="string", description="The version of the code." )
     * @SWG\Parameter( name="language", in="query", required=true, type="string", description="The language being used (either en or es)." )
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

    public function postRegisterAction(Request $request)
    {
        try {
            $this->initialise();

            if (!$request->headers->has('Content-Type')) {
                throw new \Exception("Missing Content-Type header.");
            }

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

            $role = $this->em->getRepository('BackendAdminBundle:Role')->findOneById(self::TENANT_ROLE_ID);

            $user = new User();

            $encoder = $this->get('security.password_encoder');

            $user->setName($name);
            $user->setMobilePhone($mobilePhone);
            $user->setGeoCountry($country);
            $user->setUsername($email);
            $user->setEmail($email);
            $user->setRole($role);
            $user->setSalt(base_convert(sha1(uniqid(mt_rand(), true)), 16, 36));
            $user->setPlainPassword($password);
            $user->setPassword($encoder->encodePassword($user, $password));
            $user->setEnabled(true);

            $this->get("services")->blameOnMe($user, "create");
            $this->get("services")->blameOnMe($user, "update");

            $this->em->persist($user);

            /** @var TenantContractRepository $tenantRepo */
            $tenantRepo = $this->em->getRepository('BackendAdminBundle:TenantContract');
            $tenantContracts = $tenantRepo->getApiRegister($email);

            foreach ($tenantContracts as $tenantContract) {
                $tenantContract->setUser($user);
                $notification = $this->createInviteUserNotification($tenantContract, $user);
                $this->em->persist($tenantContract);
                $this->em->persist($notification);
            }

            $this->em->flush();

            $this->translator->setLocale($lang);
            $subject = $this->translator->trans('mail.register_subject');
            $bodyHtml = "<b>" . $this->translator->trans('mail.label_user') . "</b> " . $user->getUsername() . "<br/>";
            $bodyHtml .= "<b>" . $this->translator->trans('mail.label_password') . "</b> " . $password . "<br/><br/>";
            $bodyHtml .= $this->translator->trans('mail.register_body');

            $message = $this->get('services')->generalTemplateMail($subject, $user->getEmail(), $bodyHtml);

            return new JsonResponse(array(
                'message' => "register",
            ));
        } catch (Exception $ex) {
            return new JsonResponse(array('message' => $ex->getMessage()), JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }


    /**
     * Get a list of available countries.
     *
     * Returns a list of countries available in the system.
     *
     * @Rest\Get("/countries", name="countries")
     *
     * @SWG\Parameter( name="Content-Type", in="header", type="string", default="application/json" )
     * @SWG\Parameter( name="Authorization", in="header", required=true, type="string", default="Bearer TOKEN", description="Authorization" )
     *
     * @SWG\Parameter( name="app_version", in="query", required=true, type="string", description="The version of the app." )
     * @SWG\Parameter( name="code_version", in="query", required=true, type="string", description="The version of the code." )
     * @SWG\Parameter( name="language", in="query", required=true, type="string", description="The language being used (either en or es)." )
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
                'message' => "countries",
                'data' => $data
            ));
        } catch (Exception $ex) {
            return new JsonResponse(array('message' => $ex->getMessage()), JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }


    /**
     * Adds a new property to a user by using the property code.
     *
     * This creates a relationship between the user and a property through the property code. This is applicable for welcomePrivateKey, welcomeQR and welcomeInvite, since all the endpoints do the same with the same parameters.
     *
     * @Rest\Post("/v1/welcomePrivateKey", name="welcomePrivateKey")
     *
     * @SWG\Parameter( name="Content-Type", in="header", required=true, type="string", default="application/json" )
     * @SWG\Parameter( name="Authorization", in="header", required=true, type="string", default="Bearer TOKEN", description="Authorization" )
     *
     * @SWG\Parameter( name="property_code", in="body", required=true, type="string", description="The code of the property.", schema={} )
     *
     * @SWG\Parameter( name="app_version", in="query", required=true, type="string", description="The version of the app." )
     * @SWG\Parameter( name="code_version", in="query", required=true, type="string", description="The version of the code." )
     * @SWG\Parameter( name="language", in="query", required=true, type="string", description="The language being used (either en or es)." )
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
     * @param Request $request
     * @return JsonResponse
     * @throws \Exception
     */

    public function postWelcomePrivateKeyAction(Request $request)
    {
        try {
            $this->initialise();

            if (!$request->headers->has('Content-Type')) {
                throw new \Exception("Missing Content-Type header.");
            }

            $propertyCode = strtolower(trim($request->get('property_code')));
            $user = $this->getUser();

            /** @var Property $property */
            $property = $this->em->getRepository('BackendAdminBundle:Property')->getApiProperty($propertyCode);
            if ($property == null) {
                throw new \Exception("Invalid property code.");
            }

            // First it needs to validate via SMS, before doing this relationship.
//            $tenantRaw = $this->em->getRepository('BackendAdminBundle:TenantContract')->getApiWelcomePrivateKey($property);
//            /** @var TenantContract $tenant */
//            $tenant = $tenantRaw[0];
//
//            $role = $this->em->getRepository('BackendAdminBundle:Role')->findOneById(4);
//
//            $tenant->setUser($this->getUser());
//            $tenant->setRole($role);
//            $tenant->setIsOwner(true);
//
//            $property->setOwner($this->getUser());
//
//            $this->get("services")->blameOnMe($property, "update");
//            $this->get("services")->blameOnMe($tenant, "update");
//
//            $this->em->persist($tenant);
//            $this->em->persist($property);
//            $this->em->flush();

            $type = $property->getPropertyType();
            if ($type == null) {
                $type = new PropertyType();
            }
            $complexSector = $property->getComplexSector();
            if ($complexSector == null) {
                $complexSector = new ComplexSector();
            }

            $data = array(
                'id' => $property->getId(),
                'code' => $property->getCode(), 'name' => $property->getName(),
                'address' => $property->getAddress(), 'type_id' => $type->getId(),
                'sector_id' => $complexSector->getId(), 'teamCorrelative' => $property->getTeamCorrelative());

            return new JsonResponse(array('message' => "welcomePrivateKey", 'data' => $data,));
        } catch (Exception $ex) {
            return new JsonResponse(array('message' => $ex->getMessage()), JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }


    /**
     * Gets the properties of the user.
     *
     * Returns a paginated list of properties owned or associated by the user.
     *
     * @Rest\Get("/v1/properties/{page_id}", name="listProperties")
     *
     * @SWG\Parameter( name="Content-Type", in="header", type="string", default="application/json" )
     * @SWG\Parameter( name="Authorization", in="header", required=true, type="string", default="Bearer TOKEN", description="Authorization" )
     *
     * @SWG\Parameter( name="page_id", in="path", type="string", description="The requested pagination page." )
     *
     * @SWG\Parameter( name="app_version", in="query", required=true, type="string", description="The version of the app." )
     * @SWG\Parameter( name="code_version", in="query", required=true, type="string", description="The version of the code." )
     * @SWG\Parameter( name="language", in="query", required=true, type="string", description="The language being used (either en or es)." )
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

    public function getPropertiesAction($page_id = 1)
    {
        try {
            $this->initialise();
            $data = array();

            $properties = $this->em->getRepository('BackendAdminBundle:Property')->getApiProperties($this->getUser());
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
                'message' => "listProperties",
                'metadata' => $this->calculatePagesMetadata($page_id, $total),
                'data' => $data
            ));
        } catch (Exception $ex) {
            return new JsonResponse(array('message' => $ex->getMessage()), JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }


    /**
     * Gets information about a property with the code.
     *
     * Returns information about a property by using the property code.
     *
     * @Rest\Get("/v1/property/{code}", name="propertyInfo")
     *
     * @SWG\Parameter( name="Content-Type", in="header", type="string", default="application/json" )
     * @SWG\Parameter( name="Authorization", in="header", required=true, type="string", default="Bearer TOKEN", description="Authorization" )
     *
     * @SWG\Parameter( name="code", in="path", required=true, type="string", description="The code of the property." )
     *
     * @SWG\Parameter( name="app_version", in="query", required=true, type="string", description="The version of the app." )
     * @SWG\Parameter( name="code_version", in="query", required=true, type="string", description="The version of the code." )
     * @SWG\Parameter( name="language", in="query", required=true, type="string", description="The language being used (either en or es)." )
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
     * @param $code
     * @return JsonResponse
     * @throws \Exception
     */
    public function getPropertyAction($code)
    {
        try {
            $this->initialise();

            $logger = $this->get('logger');
            $logger->info("CODE = " . $code);

            /** @var Property $property */
            $property = $this->em->getRepository('BackendAdminBundle:Property')->getApiProperty($code);

            if ($property == null) {
                throw new \Exception("Invalid property code.");
            }

            $type = $property->getPropertyType();
            if ($type == null) {
                $type = new PropertyType();
            }
            $complexSector = $property->getComplexSector();
            if ($complexSector == null) {
                $complexSector = new ComplexSector();
            }

            $data = array(
                'id' => $property->getId(),
                'code' => $property->getCode(), 'name' => $property->getName(),
                'address' => $property->getAddress(), 'type_id' => $type->getId(),
                'sector_id' => $complexSector->getId(), 'teamCorrelative' => $property->getTeamCorrelative());

            return new JsonResponse(array('message' => "propertyInfo", 'data' => $data));
        } catch (Exception $ex) {
            return new JsonResponse(array('message' => $ex->getMessage()), JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }


    /**
     * Gets information about a property with the id.
     *
     * Returns information about a property by using the property id.
     *
     * @Rest\Get("/v1/propertyDetail/{id}", name="propertyDetail")
     *
     * @SWG\Parameter( name="Content-Type", in="header", type="string", default="application/json" )
     * @SWG\Parameter( name="Authorization", in="header", required=true, type="string", default="Bearer TOKEN", description="Authorization" )
     *
     * @SWG\Parameter( name="id", in="path", required=true, type="string", description="The id of the property." )
     *
     * @SWG\Parameter( name="app_version", in="query", required=true, type="string", description="The version of the app." )
     * @SWG\Parameter( name="code_version", in="query", required=true, type="string", description="The version of the code." )
     * @SWG\Parameter( name="language", in="query", required=true, type="string", description="The language being used (either en or es)." )
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

    public function getPropertyDetailAction($id)
    {
        try {
            $this->initialise();

            /** @var Property $property */
            $property = $this->em->getRepository('BackendAdminBundle:Property')->getApiPropertyDetail($id, $this->getUser());
            if ($property == null) {
                throw new \Exception("Invalid property code.");
            }

            $contracts = $this->em->getRepository('BackendAdminBundle:PropertyContract')->findBy(
                array('enabled' => true, 'property' => $property, 'isActive' => true),
                array('createdAt', 'DESC')
            );

            if (count($contracts) < 1) {
                throw new \Exception("No available contracts for this property.");
            }
            /** @var PropertyContract $contract */
            $contract = $contracts[0];

            $type = $property->getPropertyType();
            if ($type == null) {
                $type = new PropertyType();
            }

            $owner = $property->getOwner();
            if ($owner == null) {
                $owner = new User();
            }

            $photosFull = $this->em->getRepository('BackendAdminBundle:PropertyPhoto')->findBy(
                array('enabled' => true, 'property' => $property)
            );

            $photos = array();
            /** @var PropertyPhoto $photo */
            foreach ($photosFull as $photo) {
                $photos[] = $photo->getPhotoPath();
            }

            $data = array(
                'id' => $property->getId(),
                'code' => $property->getCode(), 'name' => $property->getName(),
                'address' => $property->getAddress(), 'type_id' => $type->getId(),
                'is_owner' => $owner->getId() == $this->getUser()->getId(),
                'property_contract_id' => $contract->getId(),
                'photos' => $photos,
            );

            return new JsonResponse(array('message' => "", 'data' => $data));
        } catch (Exception $ex) {
            return new JsonResponse(array('message' => $ex->getMessage()), JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Sends SMS to validate.
     *
     * This relies on Twilio to send an SMS to the user with a code.
     *
     * @Rest\Post("/v1/sendSMS", name="sendSMS")
     *
     * @SWG\Parameter( name="Content-Type", in="header", required=true, type="string", default="application/json" )
     * @SWG\Parameter( name="Authorization", in="header", required=true, type="string", default="Bearer TOKEN", description="Authorization" )
     *
     * @SWG\Parameter( name="property_code", in="body", required=true, type="string", description="The code of the property.", schema={} )
     *
     * @SWG\Parameter( name="app_version", in="query", required=true, type="string", description="The version of the app." )
     * @SWG\Parameter( name="code_version", in="query", required=true, type="string", description="The version of the code." )
     * @SWG\Parameter( name="language", in="query", required=true, type="string", description="The language being used (either en or es)." )
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

            if (!$request->headers->has('Content-Type')) {
                throw new \Exception("Missing Content-Type header.");
            }

            $propertyCode = strtolower(trim($request->get('property_code')));

            /** @var Property $property */
            $property = $this->em->getRepository('BackendAdminBundle:Property')->findOneBy(array('enabled' => true, 'code' => $propertyCode));
            if ($property == null) {
                throw new \Exception("Invalid property code.");
            }

            $smsCode = $pass = $this->random_str(6, '0123456789');

            $logger = $this->get('logger');
            $logger->debug("SMS_CODE = " . $smsCode);

            $property->setSmsCode($smsCode);
            $this->get("services")->blameOnMe($property, "update");

            $this->em->persist($property);
            $this->em->flush();

            $smsMessage = $this->translator->trans('sms.code', ['%code%' => $smsCode]);

//            $msg = $this->get('services')->serviceSendSMS($smsMessage, $user->getMobilePhone() );

            $response = array('message' => "");
            if ($this->container->getParameter('kernel.environment') == 'dev') {// ToDo: To check for prod.
                $response['debug'] = $smsCode;
            }
            return new JsonResponse($response);
        } catch (Exception $ex) {
            return new JsonResponse(array('message' => $ex->getMessage()), JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Validates SMS.
     *
     * Validates the code sent via sendSMS endpoint..
     *
     * @Rest\Post("/v1/validateSMS", name="validateSMSCode")
     *
     * @SWG\Parameter( name="Content-Type", in="header", required=true, type="string", default="application/json" )
     * @SWG\Parameter( name="Authorization", in="header", required=true, type="string", default="Bearer TOKEN", description="Authorization" )
     *
     * @SWG\Parameter( name="property_code", in="body", required=true, type="string", description="The code of the property.", schema={} )
     * @SWG\Parameter( name="sms_code", in="body", required=true, type="string", description="The code received by SMS.", schema={} )
     *
     * @SWG\Parameter( name="app_version", in="query", required=true, type="string", description="The version of the app." )
     * @SWG\Parameter( name="code_version", in="query", required=true, type="string", description="The version of the code." )
     * @SWG\Parameter( name="language", in="query", required=true, type="string", description="The language being used (either en or es)." )
     * @SWG\Parameter( name="time_offset", in="query", type="string", description="Time difference with respect to GMT time." )
     *
     * @SWG\Response(
     *     response=200,
     *     description="Validates a sms code for the property for the user.",
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
    public function postValidateSmsAction(Request $request)
    {
        try {
            $this->initialise();

            if (!$request->headers->has('Content-Type')) {
                throw new \Exception("Missing Content-Type header.");
            }

            $propertyCode = strtolower(trim($request->get('property_code')));
            $smsCode = strtolower(trim($request->get('sms_code')));

            /** @var Property $property */
            $property = $this->em->getRepository('BackendAdminBundle:Property')->findOneBy(array('enabled' => true, 'code' => $propertyCode, 'sms_code' => $smsCode));
            if ($property == null) {
                throw new \Exception("Invalid property code.");
            }

            $contracts = $this->em->getRepository('BackendAdminBundle:PropertyContract')->getApiWelcomePrivateKey($property);

            if (count($contracts) <= 0) {
                throw new \Exception("No available contracts.");
            } else if (count($contracts) > 1) {
                throw new \Exception("One or more active contracts.");
            }

            $contract = $contracts[0];

            $tenants = $this->em->getRepository('BackendAdminBundle:TenantContract')->findOneBy(array('enabled' => true, 'isOwner' => true, 'propertyContract' => $contract));

            if (count($tenants) > 0) {
                throw new \Exception("A tenant is already owner.");
            }

            $tenant = new TenantContract();

            $role = $this->em->getRepository('BackendAdminBundle:Role')->findOneById(self::TENANT_ROLE_ID);

            $tenant->setUser($this->getUser());
            $tenant->setRole($role);
            $tenant->setPropertyContract($contract);
            $tenant->setIsOwner(true);
            $tenant->setEnabled(true);

            $property->setOwner($this->getUser());

            $this->get("services")->blameOnMe($property, "update");
            $this->get("services")->blameOnMe($tenant, "update");

            $this->em->persist($tenant);
            $this->em->persist($property);
            $this->em->flush();

            return new JsonResponse(array(
                'message' => "validateSMSCode",
            ));
        } catch (Exception $ex) {
            return new JsonResponse(array('message' => $ex->getMessage()), JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }


    // ToDo: update with new fields.

    /**
     * Gets the user inbox.
     *
     * Returns a list of notifications from the user.
     *
     * @Rest\Get("/v1/inbox/{page_id}", name="listInbox")
     *
     * @SWG\Parameter( name="Content-Type", in="header", type="string", default="application/json" )
     * @SWG\Parameter( name="Authorization", in="header", required=true, type="string", default="Bearer TOKEN", description="Authorization" )
     *
     * @SWG\Parameter( name="page_id", in="path", type="string", description="The requested pagination page." )
     *
     * @SWG\Parameter( name="app_version", in="query", required=true, type="string", description="The version of the app." )
     * @SWG\Parameter( name="code_version", in="query", required=true, type="string", description="The version of the code." )
     * @SWG\Parameter( name="language", in="query", required=true, type="string", description="The language being used (either en or es)." )
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
     *                  @SWG\Property( property="timestamp", type="string", description="Timestamp GMT formatted with Unix Time (https://en.wikipedia.org/wiki/Unix_time)", example="1272509157" ),
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

            /** @var UserNotificationRepository $userNotificationRepo */
            $userNotificationRepo = $this->em->getRepository('BackendAdminBundle:UserNotification');

            $notifications = $userNotificationRepo->getApiInbox($this->getUser(), $page_id);
            $total = $userNotificationRepo->countApiInbox($this->getUser());

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
                    'timestamp' => $user->getTimestamp(),
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
     * Gets the ticket categories for a property and complex.
     *
     * Return the ticket categories available for a property and complex.
     *
     * @Rest\Get("/v1/ticketCategory/{complex_id}/{page_id}", name="ticket_categories")
     *
     * @SWG\Parameter( name="Content-Type", in="header", type="string", default="application/json" )
     * @SWG\Parameter( name="Authorization", in="header", required=true, type="string", default="Bearer TOKEN", description="Authorization" )
     *
     * @SWG\Parameter( name="complex_id", in="path", required=true, type="string", description="The ID of the Complex." )
     * @SWG\Parameter( name="page_id", in="path", type="string", description="The requested pagination page." )
     *
     * @SWG\Parameter( name="app_version", in="query", required=true, type="string", description="The version of the app." )
     * @SWG\Parameter( name="code_version", in="query", required=true, type="string", description="The version of the code." )
     * @SWG\Parameter( name="language", in="query", required=true, type="string", description="The language being used (either en or es)." )
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
     * @SWG\Tag(name="Ticket")
     */
    public function getTicketCategoriesAction($complex_id, $page_id = 1)
    {
        try {
            $this->initialise();
            $data = array();

            /** @var TicketCategoryRepository $ticketCategoryRepo */
            $ticketCategoryRepo = $this->em->getRepository('BackendAdminBundle:TicketCategory');
            $categories = $ticketCategoryRepo->getApiTicketCategories($complex_id);
            $total = $ticketCategoryRepo->countApiTicketCategories($complex_id);

            /** @var TicketCategory $category */
            foreach ($categories as $category) {

                $iconUrl = ($category->getIcon() != null) ? $category->getIcon()->getIconUnicode() : "";

                $data[] = array(
                    'category_id' => $category->getId(),
                    'category_name' => $category->getName(),
                    'icon_url' => $iconUrl,
                    'color' => $category->getColor(),
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
     * Gets a feed of tickets of the user.
     *
     * Returns a feed of tickets that belong to the user.
     *
     * @Rest\Get("/v1/feed/{property_id}/{filter_category_id}/{page_id}", name="listFeed")
     *
     * @SWG\Parameter( name="Content-Type", in="header", type="string", default="application/json" )
     * @SWG\Parameter( name="Authorization", in="header", required=true, type="string", default="Bearer TOKEN", description="Authorization" )
     *
     * @SWG\Parameter( name="property_id", in="path", required=true, type="string", description="The ID of the property." )
     * @SWG\Parameter( name="filter_category_id", in="path", required=true, type="string", description="The ID of the Filter Category." )
     * @SWG\Parameter( name="page_id", in="path", type="string", description="The requested pagination page." )
     *
     * @SWG\Parameter( name="app_version", in="query", required=true, type="string", description="The version of the app." )
     * @SWG\Parameter( name="code_version", in="query", required=true, type="string", description="The version of the code." )
     * @SWG\Parameter( name="language", in="query", required=true, type="string", description="The language being used (either en or es)." )
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
     *                  @SWG\Property( property="timestamp", type="string", description="Ticket created timestamp GMT formatted with Unix Time (https://en.wikipedia.org/wiki/Unix_time)", example="1272509157" ),
     *                  @SWG\Property( property="followers_quantity", type="string", description="Amount of followers for the ticket", example="2" ),
     *                  @SWG\Property( property="common_area", type="object",
     *                      @SWG\Property( property="id", type="string", description="Common area ID", example="1" ),
     *                      @SWG\Property( property="name", type="string", description="Common area name", example="Common area" ),
     *                      @SWG\Property( property="reservation_status", type="string", description="Common area reservation status", example="" ),
     *                      @SWG\Property( property="reservation_from", type="string", description="Common area reservation from date", example="1272509157" ),
     *                      @SWG\Property( property="reservation_to", type="string", description="Common area reservation to date", example="1272519157" ),
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
    public function getFeedAction(Request $request, $property_id, $filter_category_id, $page_id = 1)
    {
        try {
            $this->initialise();
            $data = array();
            $lang = strtolower(trim($request->get('language')));

            $tickets = $this->em->getRepository('BackendAdminBundle:Ticket')->getApiFeed($property_id, $filter_category_id, $this->getUser(), $page_id);
            $total = $this->em->getRepository('BackendAdminBundle:Ticket')->countApiFeed($property_id, $filter_category_id, $this->getUser());

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

                /** @var CommonAreaReservation $reservation */
                $reservation = $ticket->getCommonAreaReservation();
                if ($reservation == null) {
                    $reservation = new CommonAreaReservation();
                    $invalidDate = new \DateTime("@0");
                    $reservation->setReservationDateFrom($invalidDate);
                    $reservation->setReservationDateTo($invalidDate);
                }
                $reservationStatus = $reservation->getCommonAreaReservationStatus();
                if ($reservationStatus == null) {
                    $reservationStatus = new CommonAreaReservationStatus();
                    $reservationStatus->setNameEN('');
                    $reservationStatus->setNameES('');
                }
                $commonArea = $reservation->getCommonArea();
                if ($commonArea == null) {
                    $commonAreaData = array();
                } else {
                    $commonAreaData = array(
                        "id" => $commonArea->getId(),
                        "name" => $commonArea->getName(),
                        "status" => (($lang == 'en') ? $reservationStatus->getNameEN() : $reservationStatus->getNameES()),
                        "reservation_from" => $reservation->getReservationDateFrom()->getTimestamp(),
                        "reservation_to" => $reservation->getReservationDateTo()->getTimestamp(),
                    );
                }

                if ($user->getRole() != null) {
                    $role = (($lang == 'en') ? $user->getRole()->getName() : $user->getRole()->getNameES());
                } else {
                    $role = "";
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
                    'role' => $role,
                    'timestamp' => $ticket->getCreatedAt()->getTimestamp(),
                    'followers_quantity' => (array_key_exists($ticket->getId(), $followers)) ? $followers[$ticket->getId()] : 0,
                    'common_area' => $commonAreaData,
                    'comments_quantity' => (array_key_exists($ticket->getId(), $comments)) ? $comments[$ticket->getId()] : 0,
                );
            }

            return new JsonResponse(array(
                'message' => "",
                'message' => "",
                'metadata' => $this->calculatePagesMetadata($page_id, $total),
                'data' => $data
            ));
        } catch (Exception $ex) {
            return new JsonResponse(array('message' => $ex->getMessage()), JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Gets a ticket and all its information.
     *
     * Returns the ticket information including comments, followers and reservations if there are some.
     *
     * @Rest\Get("/v1/ticket/{ticket_id}", name="singleTicket")
     *
     * @SWG\Parameter( name="Content-Type", in="header", type="string", default="application/json" )
     * @SWG\Parameter( name="Authorization", in="header", required=true, type="string", default="Bearer TOKEN", description="Authorization" )
     *
     * @SWG\Parameter( name="ticket_id", in="path", required=true, type="string", description="The ID of the ticket." )
     *
     * @SWG\Parameter( name="app_version", in="query", required=true, type="string", description="The version of the app." )
     * @SWG\Parameter( name="code_version", in="query", required=true, type="string", description="The version of the code." )
     * @SWG\Parameter( name="language", in="query", required=true, type="string", description="The language being used (either en or es)." )
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
     *                  @SWG\Property( property="timestamp", type="string", description="Ticket created timestamp GMT formatted with Unix Time (https://en.wikipedia.org/wiki/Unix_time)", example="1272509157" ),
     *                  @SWG\Property( property="followers_quantity", type="string", description="Amount of followers for the ticket", example="2" ),
     *                  @SWG\Property( property="comments_quantity", type="string", description="Ammount of comments for the ticket", example="3" ),
     *                  @SWG\Property( property="description", type="string", description="Ticket description", example="Lorem ipsum." ),
     *                  @SWG\Property( property="comments", type="array",
     *                      @SWG\Items(
     *                          @SWG\Property( property="username", type="string", description="Comments's creator username", example="2" ),
     *                          @SWG\Property( property="timestamp", type="string", description="Comment's creation time", example="1272509157" ),
     *                          @SWG\Property( property="like", type="string", description="User that liked the comment", example="user2" ),
     *                          @SWG\Property( property="comment", type="string", description="Comment content", example="Comment" ),
     *                          @SWG\Property( property="icon_url", type="string", description="URL for the icon", example="/icons/1.jpg" ),
     *                      )
     *                  ),
     *                  @SWG\Property( property="common_area", type="object",
     *                      @SWG\Property( property="id", type="string", description="Common area ID", example="1" ),
     *                      @SWG\Property( property="name", type="string", description="Common area name", example="Common area" ),
     *                      @SWG\Property( property="reservation_status", type="string", description="Common area reservation status", example="" ),
     *                      @SWG\Property( property="reservation_from", type="string", description="Common area reservation from date", example="1272509157" ),
     *                      @SWG\Property( property="reservation_to", type="string", description="Common area reservation to date", example="1272519157" ),
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

            $ticket = $this->em->getRepository('BackendAdminBundle:Ticket')->getApiSingleTicket($ticket_id);
            if ($ticket == null) {
                throw new \Exception("Invalid ticket ID.");
            }

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
                'timestamp' => $ticket->getCreatedAt()->getTimestamp(),
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
                    'timestamp' => $comment->getCreatedAt()->getTimestamp(),
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
                $reservationStatus = $reservation->getCommonAreaReservationStatus();
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
                    "reservation_from" => $reservation->getReservationDateFrom()->getTimestamp(),
                    "reservation_to" => $reservation->getReservationDateTo()->getTimestamp(),
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
     * Creates a ticket.
     *
     * It receives all the necessary information to create a ticket.
     *
     * @Rest\Post("/v1/ticket", name="createTicket")
     *
     * @SWG\Parameter( name="Content-Type", in="header", required=true, type="string", default="application/json" )
     * @SWG\Parameter( name="Authorization", in="header", required=true, type="string", default="Bearer TOKEN", description="Authorization" )
     *
     * @SWG\Parameter( name="title", in="body", required=true, type="string", description="The title of the ticket.", schema={} )
     * @SWG\Parameter( name="description", in="body", required=true, type="string", description="The description of the ticket.", schema={} )
     * @SWG\Parameter( name="photos", in="body", type="array", description="The photos of the ticket. It must be base64 encoded.", schema={} )
     * @SWG\Parameter( name="solution", in="body", required=true, type="string", description="Rhe ticket's solution.", schema={} )
     * @SWG\Parameter( name="is_public", in="body", required=true, type="boolean", description="Is the ticket public or private.", schema={} )
     * @SWG\Parameter( name="category_id", in="body", required=true, type="integer", description="The category ID of the ticket.", schema={} )
     * @SWG\Parameter( name="sector_id", in="body", required=true, type="integer", description="The complex sector ID of the ticket.", schema={} )
     * @SWG\Parameter( name="property_id", in="body", required=true, type="integer", description="The property ID of the ticket.", schema={} )
     * @SWG\Parameter( name="common_area_reservation_id", in="body", type="integer", description="The common area reservation ID of the ticket.", schema={} )
     * @SWG\Parameter( name="tenant_contract_id", in="body", type="integer", description="The tenant contract ID.", schema={} )
     *
     * @SWG\Parameter( name="app_version", in="query", required=true, type="string", description="The version of the app." )
     * @SWG\Parameter( name="code_version", in="query", required=true, type="string", description="The version of the code." )
     * @SWG\Parameter( name="language", in="query", required=true, type="string", description="The language being used (either en or es)." )
     * @SWG\Parameter( name="time_offset", in="query", type="string", description="Time difference with respect to GMT time." )
     *
     * @SWG\Response(
     *     response=200,
     *     description="Creates a successfull Ticket.",
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

            if (!$request->headers->has('Content-Type')) {
                throw new \Exception("Missing Content-Type header.");
            }

            $title = trim($request->get('title'));
            $description = trim($request->get('description'));
            $photos = $request->get('photos');
            $solution = trim($request->get('solution'));
            $isPublic = boolval($request->get('is_public'));
            $categoryId = intval($request->get('category_id'));
            $complexSectorId = intval($request->get('sector_id'));
            $propertyId = intval($request->get('property_id'));
            $commonAreaReservationId = intval($request->get('common_area_reservation_id'));
            $tenantContractId = intval($request->get('tenant_contract_id'));

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
//            if ($tenantContract == null) {
//                throw new \Exception("Invalid tenant contract ID.");
//            }

            $status = $this->em->getRepository('BackendAdminBundle:TicketStatus')->findOneById(self::TICKET_STATUS_OPEN_ID);
            if ($status == null) {
                throw new \Exception("Invalid ticket status.");
            }

            $ticket = new Ticket();
            $ticket->setTitle($title);
            $ticket->setDescription($description);
            $ticket->setPossibleSolution($solution);
            $ticket->setIsPublic($isPublic);
            $ticket->setTicketCategory($category);
            $ticket->setComplexSector($complexSector);
            $ticket->setComplex($complexSector->getComplex());
            $ticket->setProperty($property);
            $ticket->setCommonAreaReservation($commonAreaReservation);
            $ticket->setTenantContract($tenantContract);
            $ticket->setTicketStatus($status);
            $ticket->setEnabled(true);
            // ToDo: setAssignedTo
//            $ticket->setAssignedTo();
            $this->get("services")->blameOnMe($ticket, "create");
            $this->get("services")->blameOnMe($ticket, "update");

            $statusLog = new TicketStatusLog();
            $statusLog->setTicketStatus($status);
            $statusLog->setTicket($ticket);
            $this->get("services")->blameOnMe($statusLog, "create");
            $this->get("services")->blameOnMe($statusLog, "update");

            /** @var ValidatorInterface $validator */
            $validator = $this->get('validator');

            foreach ($photos as $photo) {
                $decodedPhoto = base64_decode($photo);

                $tmpPath = sys_get_temp_dir() . '/sf_upload' . uniqid();
                file_put_contents($tmpPath, $decodedPhoto);
                $uploadedFile = new FileObject($tmpPath);
                $originalFilename = $uploadedFile->getFilename();

                $violations = $validator->validate(
                    $uploadedFile,
                    array(
                        new File(array(
                            'maxSize' => '5M',
                            'mimeTypes' => array('image/*')
                        ))
                    )
                );

                if ($violations->count() > 0) {
                    throw new \Exception("Invalid image.");
                }

                $fileName = md5(uniqid()) . '.' . $uploadedFile->guessExtension();

                try {
                    $uploadedFile->move(self::UPLOADS_FOLDER, $fileName);
                } catch (FileException $e) {
                    throw new \Exception("Could not upload photo.");
                }

                $ticketPhoto = new TicketFilePhoto(); // ToDo: Update properties
//                $ticketPhoto->setPath($masterFilePath . '/' . $fileName);
//                $ticketPhoto->setFilename($fileName);
//                $ticketPhoto->setOriginalFilename(($originalFilename!=null)?$originalFilename:$fileName);
//                $ticketPhoto->setMimeType($uploadedFile->getMimeType());

//                $this->em->persist($ticketPhoto);
            }

            $this->em->persist($ticket);
            $this->em->persist($statusLog);

            $this->em->flush();

            $response = array('message' => "");
            if ($this->container->getParameter('kernel.environment') == 'dev') {
                $response['debug'] = $ticket->getId();
            }
            return new JsonResponse($response);

        } catch (Exception $ex) {
            return new JsonResponse(array('message' => $ex->getMessage()), JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Closes a ticket.
     *
     * Closes a ticket and adds a rating to it.
     *
     * @Rest\Put("/v1/ticket", name="closeTicket")
     *
     * @SWG\Parameter( name="Content-Type", in="header", required=true, type="string", default="application/json" )
     * @SWG\Parameter( name="Authorization", in="header", required=true, type="string", default="Bearer TOKEN", description="Authorization" )
     *
     * @SWG\Parameter( name="ticket_id", in="body", required=true, type="integer", description="The ticket ID.", schema={} )
     * @SWG\Parameter( name="rating", in="body", type="integer", description="Rating.", schema={} )
     *
     * @SWG\Parameter( name="app_version", in="query", required=true, type="string", description="The version of the app." )
     * @SWG\Parameter( name="code_version", in="query", required=true, type="string", description="The version of the code." )
     * @SWG\Parameter( name="language", in="query", required=true, type="string", description="The language being used (either en or es)." )
     * @SWG\Parameter( name="time_offset", in="query", type="string", description="Time difference with respect to GMT time." )
     *
     * @SWG\Response(
     *     response=200,
     *     description="Closes an existing ticket.",
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

    public function putTicketAction(Request $request)
    {
        try {
            $this->initialise();

            if (!$request->headers->has('Content-Type')) {
                throw new \Exception("Missing Content-Type header.");
            }

            $ticketId = trim($request->get('ticket_id'));
            $rating = trim($request->get('rating'));

            // Required parameter
            /** @var Ticket $ticket */
            $ticket = $this->em->getRepository('BackendAdminBundle:Ticket')->findOneBy(array('enabled' => true, 'id' => $ticketId));
            if ($ticket == null) {
                throw new \Exception("Invalid ticket ID.");
            }

            $status = $this->em->getRepository('BackendAdminBundle:TicketStatus')->findOneById(self::TICKET_STATUS_CLOSE_ID);
            if ($status == null) {
                throw new \Exception("Invalid ticket status.");
            }

            $ticket->setRatingToTenant($rating);
            $ticket->setTicketStatus($status);

            $this->get("services")->blameOnMe($ticket, "update");

            $statusLog = new TicketStatusLog();
            $statusLog->setTicketStatus($status);
            $statusLog->setTicket($ticket);
            $this->get("services")->blameOnMe($statusLog, "create");
            $this->get("services")->blameOnMe($statusLog, "update");

            $this->em->persist($ticket);
            $this->em->persist($statusLog);

            $this->em->flush();

            return new JsonResponse(array(
                'message' => "",
            ));
        } catch (Exception $ex) {
            return new JsonResponse(array('message' => $ex->getMessage()), JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Creates a comment.
     *
     * Creates a comment for a ticket.
     *
     * @Rest\Post("/v1/comment", name="commentTicket")
     *
     * @SWG\Parameter( name="Content-Type", in="header", required=true, type="string", default="application/json" )
     * @SWG\Parameter( name="Authorization", in="header", required=true, type="string", default="Bearer TOKEN", description="Authorization" )
     *
     * @SWG\Parameter( name="ticket_id", in="body", required=true, type="integer", description="The ticket ID.", schema={} )
     * @SWG\Parameter( name="comment", in="body", required=true, type="string", description="Comment.", schema={} )
     *
     * @SWG\Parameter( name="app_version", in="query", required=true, type="string", description="The version of the app." )
     * @SWG\Parameter( name="code_version", in="query", required=true, type="string", description="The version of the code." )
     * @SWG\Parameter( name="language", in="query", required=true, type="string", description="The language being used (either en or es)." )
     * @SWG\Parameter( name="time_offset", in="query", type="string", description="Time difference with respect to GMT time." )
     *
     * @SWG\Response(
     *     response=200,
     *     description="Posts a comment into an existin ticket.",
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

    public function postCommentAction(Request $request)
    {
        try {
            $this->initialise();

            if (!$request->headers->has('Content-Type')) {
                throw new \Exception("Missing Content-Type header.");
            }

            $ticketId = trim($request->get('ticket_id'));
            $comment = trim($request->get('comment'));

            // Required parameter
            /** @var Ticket $ticket */
            $ticket = $this->em->getRepository('BackendAdminBundle:Ticket')->findOneBy(array('enabled' => true, 'id' => $ticketId));
            if ($ticket == null) {
                throw new \Exception("Invalid ticket ID.");
            }

            $ticketComment = new TicketComment();
            $ticketComment->setTicket($ticket);
            $ticketComment->setCommentDescription($comment);
            $ticketComment->setEnabled(true);
            $this->get("services")->blameOnMe($ticketComment, "create");
            $this->get("services")->blameOnMe($ticketComment, "update");

            $this->em->persist($ticketComment);

            $this->em->flush();

            return new JsonResponse(array(
                'message' => "",
            ));
        } catch (Exception $ex) {
            return new JsonResponse(array('message' => $ex->getMessage()), JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Get active polls.
     *
     * Returns a list of active polls.
     *
     * @Rest\Get("/v1/polls/{page_id}", name="listPolls")
     *
     * @SWG\Parameter( name="Content-Type", in="header", type="string", default="application/json" )
     * @SWG\Parameter( name="Authorization", in="header", required=true, type="string", default="Bearer TOKEN", description="Authorization" )
     *
     * @SWG\Parameter( name="page_id", in="path", type="string", description="The requested pagination page." )
     *
     * @SWG\Parameter( name="app_version", in="query", required=true, type="string", description="The version of the app." )
     * @SWG\Parameter( name="code_version", in="query", required=true, type="string", description="The version of the code." )
     * @SWG\Parameter( name="language", in="query", required=true, type="string", description="The language being used (either en or es)." )
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
     * Gets information about a poll.
     *
     * Returns the information about a poll, including questions and answers for each.
     *
     * @Rest\Get("/v1/poll/{poll_id}", name="poll")
     *
     * @SWG\Parameter( name="Content-Type", in="header", type="string", default="application/json" )
     * @SWG\Parameter( name="Authorization", in="header", required=true, type="string", default="Bearer TOKEN", description="Authorization" )
     *
     * @SWG\Parameter( name="poll_id", in="path", type="string", description="The ID of the poll." )
     *
     * @SWG\Parameter( name="app_version", in="query", required=true, type="string", description="The version of the app." )
     * @SWG\Parameter( name="code_version", in="query", required=true, type="string", description="The version of the code." )
     * @SWG\Parameter( name="language", in="query", required=true, type="string", description="The language being used (either en or es)." )
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


// ToDo: from email
// # /api/v1/answer
// Actualmente todos los campos de respuestas para los distintos tipos son requeridos.
// Los campos deberían de ser opcionales y en el backend se debería de validar si el tipo de respuesta es correcto para el tipo de pregunta

// Hay q arreglar, q based en el question, primero hay q ver q tipo de question es y en base a eso se valida cual de los 3 campos es requerido.

    /**
     * Post an answer to a poll.
     *
     * Creates the answer of a tenant to a poll.
     *
     * @Rest\Post("/v1/answer", name="tenantAnswer")
     *
     * @SWG\Parameter( name="Content-Type", in="header", required=true, type="string", default="application/json" )
     * @SWG\Parameter( name="Authorization", in="header", required=true, type="string", default="Bearer TOKEN", description="Authorization" )
     *
     * @SWG\Parameter( name="poll_question_id", in="body", required=true, type="integer", description="The Poll Question ID.", schema={} )
     * @SWG\Parameter( name="answer_text", in="body", type="string", description="The answer text. It is required although it could be empty.", schema={} )
     * @SWG\Parameter( name="answer_rating", in="body", type="integer", description="The answer rating.", schema={} )
     * @SWG\Parameter( name="poll_question_option_ids", in="body", type="array", description="Array of integers of poll question option ids. Must have at least 1 element.", schema={} )
     *
     * @SWG\Parameter( name="app_version", in="query", required=true, type="string", description="The version of the app." )
     * @SWG\Parameter( name="code_version", in="query", required=true, type="string", description="The version of the code." )
     * @SWG\Parameter( name="language", in="query", required=true, type="string", description="The language being used (either en or es)." )
     * @SWG\Parameter( name="time_offset", in="query", type="string", description="Time difference with respect to GMT time." )
     *
     * @SWG\Response(
     *     response=200,
     *     description="Post an answer to a poll.",
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
     * @SWG\Tag(name="Poll")
     */

    public function postAnswerAction(Request $request)
    {
        try {
            $this->initialise();

            if (!$request->headers->has('Content-Type')) {
                throw new \Exception("Missing Content-Type header.");
            }

            $pollQuestionId = $request->get('poll_question_id');
            $answerText = trim($request->get('answer_text'));
            $answerRating = $request->get('answer_rating');
            $pollQuestionOptionIds = $request->get('poll_question_option_ids');

            if (count($pollQuestionOptionIds) == 0) {
                throw new \Exception("poll_question_option_ids is empty.");
            }

            // Required parameter
            $pollQuestion = $this->em->getRepository('BackendAdminBundle:PollQuestion')->findOneBy(array('enabled' => true, 'id' => $pollQuestionId));
            if ($pollQuestion == null) {
                throw new \Exception("Invalid Poll Question ID.");
            }

            // Required parameter
            $pollQuestionOptions = $this->em->getRepository('BackendAdminBundle:PollQuestion')->getApiAnswer($pollQuestionOptionIds);
            if ($pollQuestionOptions == null) {
                throw new \Exception("Invalid Poll Question ID.");
            }

            if (count($pollQuestionOptionIds) != count($pollQuestionOptions)) {
                throw new \Exception("Invalid Poll Question Option ID.");
            }

            foreach ($pollQuestionOptions as $option) {
                $answer = new PollTenantAnswer();
                $answer->setAnswerText($answerText);
                $answer->setAnswerRating($answerRating);
                $answer->setPollQuestion($pollQuestion);
                $answer->setPollQuestionOption($option);
                $answer->setEnabled(true);
                $this->get("services")->blameOnMe($answer, "create");
                $this->get("services")->blameOnMe($answer, "update");

                $this->em->persist($answer);
            }

            $this->em->flush();

            return new JsonResponse(array(
                'message' => "",
            ));
        } catch (Exception $ex) {
            return new JsonResponse(array('message' => $ex->getMessage()), JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }


    /**
     * Updates the avatar.
     *
     * Sets the user avatar with a new image, which is received in base64 encoding.
     *
     * @Rest\Put("/v1/avatar", name="setAvatar")
     *
     * @SWG\Parameter( name="Content-Type", in="header", required=true, type="string", default="application/json" )
     * @SWG\Parameter( name="Authorization", in="header", required=true, type="string", default="Bearer TOKEN", description="Authorization" )
     *
     * @SWG\Parameter( name="photo", in="body", type="array", description="The new photo of the avatar. It must be base64 encoded.", schema={} )
     *
     * @SWG\Parameter( name="app_version", in="query", required=true, type="string", description="The version of the app." )
     * @SWG\Parameter( name="code_version", in="query", required=true, type="string", description="The version of the code." )
     * @SWG\Parameter( name="language", in="query", required=true, type="string", description="The language being used (either en or es)." )
     * @SWG\Parameter( name="time_offset", in="query", type="string", description="Time difference with respect to GMT time." )
     *
     * @SWG\Response(
     *     response=200,
     *     description="Updates the user avatar.",
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

    public function putAvatarAction(Request $request, ValidatorInterface $validator)
    {
        try {
            $this->initialise();

            if (!$request->headers->has('Content-Type')) {
                throw new \Exception("Missing Content-Type header.");
            }

            $photo = $request->get('photo');

            $decodedPhoto = base64_decode($photo);

            $tmpPath = sys_get_temp_dir() . '/sf_upload' . uniqid();
            file_put_contents($tmpPath, $decodedPhoto);
            $uploadedFile = new FileObject($tmpPath);
            $originalFilename = $uploadedFile->getFilename();

            $violations = $validator->validate(
                $uploadedFile,
                array(
                    new File(array(
                        'maxSize' => '5M',
                        'mimeTypes' => array('image/*')
                    ))
                )
            );

            if ($violations->count() > 0) {
                throw new \Exception("Invalid image.");
            }

            $fileName = md5(uniqid()) . '.' . $uploadedFile->guessExtension();

            try {
                $uploadedFile->move(self::UPLOADS_FOLDER, $fileName);
            } catch (FileException $e) {
                throw new \Exception("Could not upload photo.");
            }


            /** @var User $user */
            $user = $this->getUser();

            $user->setAvatarPath($uploadedFile->getPath());
            $this->get("services")->blameOnMe($user, "update");
            $this->em->persist($user);

            $this->em->flush();

            return new JsonResponse(array(
                'message' => "",
            ));
        } catch (Exception $ex) {
            return new JsonResponse(array('message' => $ex->getMessage()), JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }


    /**
     * Lists the invites from a property and a contract.
     *
     * Returns a list with the invites from a property and a contract.
     *
     * @Rest\Get("/v1/propertyInvites/{property_contract_id}/{property_id}/{page_id}", name="listPropertyInvites")
     *
     * @SWG\Parameter( name="Content-Type", in="header", type="string", default="application/json" )
     * @SWG\Parameter( name="Authorization", in="header", required=true, type="string", default="Bearer TOKEN", description="Authorization" )
     *
     * @SWG\Parameter( name="page_id", in="path", type="integer", description="The requested pagination page." )
     * @SWG\Parameter( name="property_contract_id", in="path", type="integer", description="The property contract id." )
     * @SWG\Parameter( name="property_id", in="path", type="integer", description="The property id." )
     *
     * @SWG\Parameter( name="app_version", in="query", required=true, type="string", description="The version of the app." )
     * @SWG\Parameter( name="code_version", in="query", required=true, type="string", description="The version of the code." )
     * @SWG\Parameter( name="language", in="query", required=true, type="string", description="The language being used (either en or es)." )
     * @SWG\Parameter( name="time_offset", in="query", type="string", description="Time difference with respect to GMT time." )
     *
     * @SWG\Response(
     *     response=200,
     *     description="Returns a list with the property invites of a property and a contract.",
     *     @SWG\Schema (
     *          @SWG\Property(
     *              property="data", type="array",
     *              @SWG\Items(
     *                  @SWG\Property( property="tenant_contract_id", type="integer", description="Tenant contract ID", example="1" ),
     *                  @SWG\Property( property="intived", type="string", description="Name of the invited person", example="Jon Doe" ),
     *                  @SWG\Property( property="email", type="string", description="Email of the invited person", example="invited@example.com" ),
     *                  @SWG\Property( property="phone", type="string", description="Phone of the invited person", example="+306 5558 8999" ),
     *                  @SWG\Property( property="invitationAccepted", type="boolean", description="If the invitation has been accepted or not", example="true" ),
     *              ),
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
     * @SWG\Tag(name="Property Invites")
     */
    public function getPropertyInvitesAction($property_contract_id, $property_id, $page_id = 1)
    {
        try {
            $this->initialise();
            $data = array();

            /** @var TenantContractRepository $tenantContractRepo */
            $tenantContractRepo = $this->em->getRepository('BackendAdminBundle:TenantContract');

            $contracts = $tenantContractRepo->getApiPropertyInvites($property_contract_id, $property_id, $page_id);
            $total = $tenantContractRepo->countApiPropertyInvites($property_contract_id, $property_id);

            /** @var TenantContract $contract */
            foreach ($contracts as $contract) {
                $data[] = array(
                    'tenant_contract_id' => $contract->getId(),
                    'invited' => ($contract->getUser() != null) ? $contract->getUser()->getName() : "",
                    'phone' => ($contract->getUser() != null) ? $contract->getUser()->getMobilePhone() : "",
                    'email' => ($contract->getUser() != null) ? $contract->getUser()->getEmail() : $contract->getInvitationUserEmail(),
                    'invitationAccepted' => $contract->getInvitationAccepted(),
                );
            }

            return new JsonResponse(array(
                'message' => "listPropertyInvites",
                'metadata' => $this->calculatePagesMetadata($page_id, $total),
                'data' => $data
            ));
        } catch (Exception $ex) {
            return new JsonResponse(array('message' => $ex->getMessage()), JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Posts an invitation.
     *
     * Creates a tenant contract and a notification for a given user.
     *
     * @Rest\Post("/v1/invitation", name="sendInvitation")
     *
     * @SWG\Parameter( name="Content-Type", in="header", required=true, type="string", default="application/json" )
     * @SWG\Parameter( name="Authorization", in="header", required=true, type="string", default="Bearer TOKEN", description="Authorization" )
     *
     * @SWG\Parameter( name="message", in="body", required=true, type="string", description="Message.", schema={} )
     * @SWG\Parameter( name="email", in="body", required=true, type="string", description="The email of the invited user.", schema={} )
     * @SWG\Parameter( name="property_contract_id", in="body", required=true, type="integer", description="The id of the property contract.", schema={} )
     *
     * @SWG\Parameter( name="app_version", in="query", required=true, type="string", description="The version of the app." )
     * @SWG\Parameter( name="code_version", in="query", required=true, type="string", description="The version of the code." )
     * @SWG\Parameter( name="language", in="query", required=true, type="string", description="The language being used (either en or es)." )
     * @SWG\Parameter( name="time_offset", in="query", type="string", description="Time difference with respect to GMT time." )
     *
     * @SWG\Response(
     *     response=200,
     *     description="Creates an invitation for a user to a property.",
     *     @SWG\Schema (
     *          @SWG\Property( property="message", type="string", example="" )
     *      )
     * )
     * @SWG\Response(
     *     response=500, description="Internal error.",
     *     @SWG\Schema (
     *          @SWG\Property(property="data", type="string", example="" ),
     *          @SWG\Property( property="message", type="string", example="Internal error." )
     *     )
     * )
     *
     * @SWG\Tag(name="Property Invites")
     */

    public function postInvitationAction(Request $request)
    {
        try {
            $this->initialise();

            if (!$request->headers->has('Content-Type')) {
                throw new \Exception("Missing Content-Type header.");
            }

            $lang = strtolower(trim($request->get('language')));

            $message = trim($request->get('message'));
            $email = trim($request->get('email'));
            $propertyContractId = trim($request->get('property_contract_id'));

            /** @var PropertyContractRepository $propertyContractRepo */
            $propertyContractRepo = $this->em->getRepository('BackendAdminBundle:PropertyContract');
            /** @var UserRepository $userRepo */
            $userRepo = $this->em->getRepository('BackendAdminBundle:User');
            /** @var TenantContractRepository $tenantRepo */
            $tenantRepo = $this->em->getRepository('BackendAdminBundle:TenantContract');

            $propertyContract = $propertyContractRepo->findOneBy(array('enabled' => true, 'id' => $propertyContractId));
            if ($propertyContract == null) {
                throw new \Exception("Invalid property contract.");
            }

            $conflicTenantContracts = $tenantRepo->findBy(array('enabled' => true, 'propertyContract' => $propertyContract, 'invitationUserEmail' => $email));
            if (count($conflicTenantContracts) > 0) {
                throw new \Exception("There is at least one existing TenantContract with this email and property contract.");
            }

            $tenantContract = new TenantContract();
            $tenantContract->setPropertyContract($propertyContract);
            $tenantContract->setIsOwner(false);
            $tenantContract->setEnabled(true);
            $tenantContract->setInvitationUserEmail($email);
            $tenantContract->setInvitationAccepted(false);
            $this->get("services")->blameOnMe($tenantContract, "create");
            $this->get("services")->blameOnMe($tenantContract, "update");

            /** @var User $user */
            $user = $userRepo->findOneBy(array('enabled' => true, 'email' => $email));
            if ($propertyContract != null) {
                $tenantContract->setUser($user);
                $notification = $this->createInviteUserNotification($tenantContract, $user);
                $this->em->persist($notification);
            }

            $this->em->persist($tenantContract);
            $this->em->flush();

            $this->translator->setLocale($lang);
            $now = new \DateTime();
            $subject = $this->translator->trans('mail.invite_subject');
            $bodyHtml = sprintf("<p>%s</p><br/>", $this->translator->trans('mail.invite_body'));
            $bodyHtml .= sprintf("<p>%s</p><br/>", $message);
            $bodyHtml .= sprintf("<b>%s</b> %s<br/>", $this->translator->trans('mail.label_time'), $now->format('Y-m-d H:i'));
            $bodyHtml .= "<br/>";

            $messageEmail = $this->get('services')->generalTemplateMail($subject, $email, $bodyHtml);

            return new JsonResponse(array(
                'message' => "sendInvitation",
            ));
        } catch (Exception $ex) {
            return new JsonResponse(array('message' => $ex->getMessage()), JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    private function createInviteUserNotification( $tenantContract, $user ) {
        /** @var NotificationTypeRepository $notificationRepo */
        $notificationRepo = $this->em->getRepository('BackendAdminBundle:NotificationType');
        $notificationType = $notificationRepo->findOneById( self::INVITATION_NOTIFICATION_TYPE_ID );

        $notification = new UserNotification();
        $notification->setEnabled(true);
        $notification->setTenantContract($tenantContract);
        $notification->setNotificationType($notificationType);

        $notification->setCreatedBy($user);
        $notification->setUpdatedBy($user);

        $now = new \DateTime();
        $notification->setCreatedAt($now);
        $notification->setUpdatedAt($now);

        return $notification;
    }


    /**
     * Accepts an invitation.
     *
     * Accepts an invitation by setting the proper values in the tenant contract and the notification.
     *
     * @Rest\Put("/v1/invitation", name="acceptInvitation")
     *
     * @SWG\Parameter( name="Content-Type", in="header", required=true, type="string", default="application/json" )
     * @SWG\Parameter( name="Authorization", in="header", required=true, type="string", default="Bearer TOKEN", description="Authorization" )
     *
     * @SWG\Parameter( name="tenant_contract_id", in="body", required=true, type="integer", description="The id of the tenant contract.", schema={} )
     *
     * @SWG\Parameter( name="app_version", in="query", required=true, type="string", description="The version of the app." )
     * @SWG\Parameter( name="code_version", in="query", required=true, type="string", description="The version of the code." )
     * @SWG\Parameter( name="language", in="query", required=true, type="string", description="The language being used (either en or es)." )
     * @SWG\Parameter( name="time_offset", in="query", type="string", description="Time difference with respect to GMT time." )
     *
     * @SWG\Response(
     *     response=200,
     *     description="Accepts an invitation for a user to a property.",
     *     @SWG\Schema (
     *          @SWG\Property( property="message", type="string", example="" )
     *      )
     * )
     * @SWG\Response(
     *     response=500, description="Internal error.",
     *     @SWG\Schema (
     *          @SWG\Property(property="data", type="string", example="" ),
     *          @SWG\Property( property="message", type="string", example="Internal error." )
     *     )
     * )
     *
     * @SWG\Tag(name="Property Invites")
     */

    public function postAcceptInvitationAction(Request $request)
    {
        try {
            $this->initialise();

            if (!$request->headers->has('Content-Type')) {
                throw new \Exception("Missing Content-Type header.");
            }

            $tenantContractId = trim($request->get('tenant_contract_id'));

            /** @var TenantContractRepository $tenantRepo */
            $tenantRepo = $this->em->getRepository('BackendAdminBundle:TenantContract');
            /** @var UserNotificationRepository $notificationRepo */
            $notificationRepo = $this->em->getRepository('BackendAdminBundle:UserNotification');

            /** @var TenantContract $tenantContract */
            $tenantContract = $tenantRepo->findOneBy(array('enabled' => true, 'user' => $this->getUser(), 'id' => $tenantContractId));
            if ($tenantContract == null) {
                throw new \Exception("Invalid tenant contract.");
            }

            $userNotifications = $notificationRepo->findBy(array('enabled' => true, 'user' => $this->getUser(), 'tenantContract' => $tenantContract));

            /** @var UserNotification $userNotification */
            foreach ($userNotifications as $userNotification) {
                $userNotification->setIsRead(true);
                $this->get("services")->blameOnMe($userNotification, "update");

                $this->em->persist($userNotification);
            }

            $tenantContract->setInvitationAccepted(true);
            $this->get("services")->blameOnMe($tenantContract, "update");

            $this->em->persist($tenantContract);
            $this->em->flush();

            return new JsonResponse(array(
                'message' => "acceptInvitation",
            ));
        } catch (Exception $ex) {
            return new JsonResponse(array('message' => $ex->getMessage()), JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Gets the FAQs.
     *
     * Returns a list with the frequently asked questions of a business.
     *
     * @Rest\Get("/v1/faq/{complex_id}/{page_id}", name="faq")
     *
     * @SWG\Parameter( name="Content-Type", in="header", type="string", default="application/json" )
     * @SWG\Parameter( name="Authorization", in="header", required=true, type="string", default="Bearer TOKEN", description="Authorization" )
     *
     * @SWG\Parameter( name="page_id", in="path", type="string", description="The requested pagination page." )
     * @SWG\Parameter( name="complex_id", in="path", type="string", description="The complex id." )
     *
     * @SWG\Parameter( name="app_version", in="query", required=true, type="string", description="The version of the app." )
     * @SWG\Parameter( name="code_version", in="query", required=true, type="string", description="The version of the code." )
     * @SWG\Parameter( name="language", in="query", required=true, type="string", description="The language being used (either en or es)." )
     * @SWG\Parameter( name="time_offset", in="query", type="string", description="Time difference with respect to GMT time." )
     *
     * @SWG\Response(
     *     response=200,
     *     description="Returns a list with the frequently asked questions of a business.",
     *     @SWG\Schema (
     *          @SWG\Property(
     *              property="data", type="array",
     *              @SWG\Items(
     *                  @SWG\Property( property="id", type="integer", description="FAQ ID", example="1" ),
     *                  @SWG\Property( property="business_name", type="string", description="Name of the business", example="Business" ),
     *                  @SWG\Property( property="business_address", type="string", description="Address of the business", example="4400 Rickenbacker Causeway, Miami, FL, 33149, EE. UU." ),
     *                  @SWG\Property( property="business_phone", type="string", description="Phone of the business", example="+306 5558 8999" ),
     *                  @SWG\Property( property="faqs", type="array",
     *                      @SWG\Items(
     *                          @SWG\Property( property="question", type="string", description="Frequently asked question", example="Pregunta 1" ),
     *                          @SWG\Property( property="answer", type="string", description="Answer to the frequently asked question", example="Answer 1" ),
     *                      )
     *                  ),
     *              ),
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
     * @SWG\Tag(name="FAQ")
     */
    public function getFaqAction($complex_id, $page_id = 1)
    {
        try {
            $this->initialise();

            /** @var ComplexFaqRepository $complexFaqRepo */
            $complexFaqRepo = $this->em->getRepository('BackendAdminBundle:ComplexFaq');
            /** @var ComplexRepository $complexRepo */
            $complexRepo = $this->em->getRepository('BackendAdminBundle:Complex');

            $faqs = $complexFaqRepo->getApiFaqs($complex_id, $page_id);
            $total = $complexFaqRepo->countApiFaqs($complex_id);
            /** @var Complex $complex */
            $complex = $complexRepo->getApiFaqs($complex_id);

            $data = array(
                'business_name' => $complex->getBusiness()->getName(),
                'business_address' => $complex->getBusiness()->getAddress(),
                'business_phone' => $complex->getBusiness()->getPhoneNumber(),
            );

            $data['faqs'] = array();
            /** @var ComplexFaq $faq */
            foreach ($faqs as $faq) {
                $data['faqs'][] = array(
                    'question' => $faq->getQuestion(),
                    'answer' => $faq->getAnswer(),
                );
            }

            return new JsonResponse(array(
                'message' => "faq",
                'metadata' => $this->calculatePagesMetadata($page_id, $total),
                'data' => $data
            ));
        } catch (Exception $ex) {
            return new JsonResponse(array('message' => $ex->getMessage()), JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Posts a new FAQ.
     *
     * Creates a new frequently asked question.
     *
     * @Rest\Post("/v1/faqMessage", name="sendMessageFAQ")
     *
     * @SWG\Parameter( name="Content-Type", in="header", required=true, type="string", default="application/json" )
     * @SWG\Parameter( name="Authorization", in="header", required=true, type="string", default="Bearer TOKEN", description="Authorization" )
     *
     * @SWG\Parameter( name="message", in="body", required=true, type="string", description="Message.", schema={} )
     * @SWG\Parameter( name="property_id", in="body", required=true, type="integer", description="The property ID of the message.", schema={} )
     *
     * @SWG\Parameter( name="app_version", in="query", required=true, type="string", description="The version of the app." )
     * @SWG\Parameter( name="code_version", in="query", required=true, type="string", description="The version of the code." )
     * @SWG\Parameter( name="language", in="query", required=true, type="string", description="The language being used (either en or es)." )
     * @SWG\Parameter( name="time_offset", in="query", type="string", description="Time difference with respect to GMT time." )
     *
     * @SWG\Response(
     *     response=200,
     *     description="Sends an email to the corresponding admin and post the message.",
     *     @SWG\Schema (
     *          @SWG\Property( property="message", type="string", example="" )
     *      )
     * )
     * @SWG\Response(
     *     response=500, description="Internal error.",
     *     @SWG\Schema (
     *          @SWG\Property(property="data", type="string", example="" ),
     *          @SWG\Property( property="message", type="string", example="Internal error." )
     *     )
     * )
     *
     * @SWG\Tag(name="FAQ")
     */

    public function postFaqAction(Request $request)
    {
        try {
            $this->initialise();

            if (!$request->headers->has('Content-Type')) {
                throw new \Exception("Missing Content-Type header.");
            }

            $lang = strtolower(trim($request->get('language')));

            $message = trim($request->get('message'));
            $propertyId = trim($request->get('property_id'));

            /** @var PropertyRepository $propertyRepo */
            $propertyRepo = $this->em->getRepository('BackendAdminBundle:Property');
            /** @var UserRepository $userRepo */
            $userRepo = $this->em->getRepository('BackendAdminBundle:User');

            $businessArr = $propertyRepo->getApiPostFaq($propertyId);
            $businessId = 0;
            if (array_key_exists('id', $businessArr)) {
                $businessId = $businessArr['id'];
            }

            /** @var User $admin */
            $admin = $userRepo->getApiPostFaq($businessId);
            if ($admin == null) {
                throw new \Exception("No admin user found for this property.");
            }
            $adminEmail = $admin->getEmail();
            $now = new \DateTime();

            $this->translator->setLocale($lang);
            $subject = $this->translator->trans('mail.register_subject');
            $bodyHtml = sprintf("<b>%s</b> %s<br/>", $this->translator->trans('mail.label_user'), $this->getUser()->getUsername());
            $bodyHtml .= sprintf("<b>%s</b> %s<br/>", $this->translator->trans('mail.label_time'), $now->format('Y-m-d H:i'));
            $bodyHtml .= sprintf("<b>%s</b> %s<br/>", $this->translator->trans('mail.label_property_id'), $propertyId);
            $bodyHtml .= "<br/><br/>";
            $bodyHtml .= sprintf("<b>%s</b> %s<br/>", $this->translator->trans('mail.label_message'), $message);

            $messageEmail = $this->get('services')->generalTemplateMail($subject, $adminEmail, $bodyHtml);

            return new JsonResponse(array(
                'message' => "sendMessageFAQ",
            ));
        } catch (Exception $ex) {
            return new JsonResponse(array('message' => $ex->getMessage()), JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }


    /**
     * Gets the common areas of a property.
     *
     * Returns a list of the common areas in the complex of the property.
     *
     * @Rest\Get("/v1/commonAreas/{property_id}/{page_id}", name="listCommonAreas")
     *
     * @SWG\Parameter( name="Content-Type", in="header", type="string", default="application/json" )
     * @SWG\Parameter( name="Authorization", in="header", required=true, type="string", default="Bearer TOKEN", description="Authorization" )
     *
     * @SWG\Parameter( name="property_id", in="path", type="string", description="The ID of the property." )
     * @SWG\Parameter( name="page_id", in="path", type="string", description="The requested pagination page." )
     *
     * @SWG\Parameter( name="app_version", in="query", required=true, type="string", description="The version of the app." )
     * @SWG\Parameter( name="code_version", in="query", required=true, type="string", description="The version of the code." )
     * @SWG\Parameter( name="language", in="query", required=true, type="string", description="The language being used (either en or es)." )
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

            /** @var PropertyRepository $propertyRepo */
            $propertyRepo = $this->em->getRepository('BackendAdminBundle:Property');
            /** @var CommonAreaRepository $commonAreaRepo */
            $commonAreaRepo = $this->em->getRepository('BackendAdminBundle:CommonArea');
            /** @var CommonAreaPhotoRepository $commonAreaPhotosRepo */
            $commonAreaPhotoRepo = $this->em->getRepository('BackendAdminBundle:CommonAreaPhoto');

            $properties = $propertyRepo->getApiCommonAreas($property_id);

            $complexIds = array();
            /** @var Property $property */
            foreach ($properties as $property) {
                $complexIds[] = $property->getComplexSector()->getComplex()->getId();
            }

            $commonAreas = $commonAreaRepo->getApiCommonAreas($complexIds, $page_id);
            $total = $this->em->getRepository('BackendAdminBundle:CommonArea')->countApiCommonAreas($complexIds);

            $cids = $this->getArrayOfIds($commonAreas);

            $rawPhotos = $commonAreaPhotoRepo->getApiCommonAreas($cids);
            $photos = array();
            /** @var CommonAreaPhoto $photo */
            foreach ($rawPhotos as $photo) {
                $photos[$photo->getCommonArea()->getId()] = $photo;
            }

            /** @var CommonArea $commonArea */
            foreach ($commonAreas as $commonArea) {
                $commonAreaPhotos = array();
                if (array_key_exists($commonArea->getId(), $photos)) {
                    /** @var CommonAreaPhoto $photo */
                    foreach ($photos[$commonArea->getId()] as $photo) {
                        $commonAreaPhotos[] = array('url' => $photo->getPhotoPath());
                    }
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
     * Gets a common area availability
     *
     * Returns a list of reservations and availability for a given common area.
     *
     * @Rest\Get("/v1/commonAreaAvailability/{common_area_id}", name="commonAreaAvailability")
     *
     * @SWG\Parameter( name="Content-Type", in="header", type="string", default="application/json" )
     * @SWG\Parameter( name="Authorization", in="header", required=true, type="string", default="Bearer TOKEN", description="Authorization" )
     *
     * @SWG\Parameter( name="common_area_id", in="path", type="string", description="The ID of the common area." )
     *
     * @SWG\Parameter( name="app_version", in="query", required=true, type="string", description="The version of the app." )
     * @SWG\Parameter( name="code_version", in="query", required=true, type="string", description="The version of the code." )
     * @SWG\Parameter( name="language", in="query", required=true, type="string", description="The language being used (either en or es)." )
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
     *                          @SWG\Property( property="date_from", type="string", description="Reservation date from GMT formatted with Unix Time (https://en.wikipedia.org/wiki/Unix_time)", example="1272509157" ),
     *                          @SWG\Property( property="date_to", type="string", description="Reservation date to GMT formatted with Unix Time (https://en.wikipedia.org/wiki/Unix_time)", example="1272519157" ),
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
                $status = $reservation->getCommonAreaReservationStatus();
                if ($status == null) {
                    $status = new CommonAreaReservationStatus();
                }

                $data['reservations'][] = array(
                    'status' => ($lang == 'en') ? $status->getNameEN() : $status->getNameES(),
                    'date_from' => $reservation->getReservationDateFrom()->getTimestamp(),
                    'date_to' => $reservation->getReservationDateTo()->getTimestamp(),
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
     * @Rest\Get("/v1/commonArea/{common_area_id}", name="commonAreaDetail")
     *
     * @SWG\Parameter( name="Content-Type", in="header", type="string", default="application/json" )
     * @SWG\Parameter( name="Authorization", in="header", required=true, type="string", default="Bearer TOKEN", description="Authorization" )
     *
     * @SWG\Parameter( name="common_area_id", in="path", type="string", description="The ID of the common area." )
     *
     * @SWG\Parameter( name="app_version", in="query", required=true, type="string", description="The version of the app." )
     * @SWG\Parameter( name="code_version", in="query", required=true, type="string", description="The version of the code." )
     * @SWG\Parameter( name="language", in="query", required=true, type="string", description="The language being used (either en or es)." )
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
            $commonArea = $this->em->getRepository('BackendAdminBundle:CommonArea')->findOneById($common_area_id);
            if ($commonArea == null) {
                throw new \Exception("Invalid common area ID.");
            }

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

    /**
     * @Rest\Post("/v1/commonAreaReservation", name="commonAreaReservation")
     *
     * @SWG\Parameter( name="Content-Type", in="header", required=true, type="string", default="application/json" )
     * @SWG\Parameter( name="Authorization", in="header", required=true, type="string", default="Bearer TOKEN", description="Authorization" )
     *
     * @SWG\Parameter( name="common_area_id", in="body", required=true, type="integer", description="The common area ID for the reservation.", schema={} )
     * @SWG\Parameter( name="reservation_date_from", in="body", required=true, type="integer", description="The start date of the reservation. This value should be sent in GTM timezone, and in the Linux Date Format (seconds since 1970-01-01 00:00:00 UTC).", schema={} )
     * @SWG\Parameter( name="reservation_date_to", in="body", required=true, type="integer", description="The end date of the reservation. This value should be sent in GTM timezone, and in the Linux Date Format (seconds since 1970-01-01 00:00:00 UTC).", schema={} )
     *
     * @SWG\Parameter( name="app_version", in="query", required=true, type="string", description="The version of the app." )
     * @SWG\Parameter( name="code_version", in="query", required=true, type="string", description="The version of the code." )
     * @SWG\Parameter( name="language", in="query", required=true, type="string", description="The language being used (either en or es)." )
     * @SWG\Parameter( name="time_offset", in="query", type="string", description="Time difference with respect to GMT time." )
     *
     * @SWG\Response(
     *     response=200,
     *     description="Creates a reservation for a common area.",
     *     @SWG\Schema (
     *          @SWG\Property( property="message", type="string", example="" )
     *      )
     * )
     * @SWG\Response(
     *     response=500, description="Internal error.",
     *     @SWG\Schema (
     *          @SWG\Property(property="data", type="string", example="" ),
     *          @SWG\Property( property="message", type="string", example="Internal error." )
     *     )
     * )
     *
     * @SWG\Tag(name="Common Area")
     */
    public function postCommonAreaReservationAction(Request $request)
    {
        try {
            $this->initialise();

            if (!$request->headers->has('Content-Type')) {
                throw new \Exception("Missing Content-Type header.");
            }

            $commonAreaId = $request->get('common_area_id');
            $reservationDateFromSecs = trim($request->get('reservation_date_from'));
            $reservationDateToSecs = trim($request->get('reservation_date_to'));
//            $reservationStatus = trim($request->get('reservation_status')); // ToDo: I removed it since it doesnt make sense

            $commonArea = $this->em->getRepository('BackendAdminBundle:CommonArea')->findOneBy(array('enabled' => true, 'id' => $commonAreaId));
            if ($commonArea == null) {
                throw new \Exception("Invalid common area ID.");
            }

            $status = $this->em->getRepository('BackendAdminBundle:CommonAreaReservationStatus')->findOneBy(array('enabled' => true, 'id' => $this::COMMON_AREA_RESERVATION_STATUS_ID));
            if ($status == null) {
                throw new \Exception("Invalid status ID.");
            }

            $startDate = new \DateTime("@$reservationDateFromSecs");
            $endDate = new \DateTime("@$reservationDateToSecs");

            $reservation = new CommonAreaReservation();
            $reservation->setCommonArea($commonArea);
            $reservation->setReservationDateFrom($startDate);
            $reservation->setReservationDateTo($endDate);
            $reservation->setReservedBy($this->getUser());
            $reservation->setEnabled(true);
            $reservation->setCommonAreaReservationStatus($status);

            $this->get("services")->blameOnMe($reservation, "create");
            $this->get("services")->blameOnMe($reservation, "update");

            $this->em->persist($reservation);

            $this->em->flush();

            return new JsonResponse(array(
                'message' => "",
            ));
        } catch (Exception $ex) {
            return new JsonResponse(array('message' => $ex->getMessage()), JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }


    /**
     * List the rewards of the specified user.
     *
     * This call takes into account all confirmed awards, but not pending or refused awards.
     *
     * @Rest\Get("/v1/payments/{month}/{year}/{page_id}", name="list_payments", )
     *
     * @SWG\Parameter( name="Content-Type", in="header", type="string", default="application/json" )
     * @SWG\Parameter( name="Authorization", in="header", required=true, type="string", default="Bearer TOKEN", description="Authorization" )
     *
     * @SWG\Parameter( name="month", in="path", required=true, type="string", description="The month of the requested payments." )
     * @SWG\Parameter( name="year", in="path", required=true, type="string", description="The year of the requested payments." )
     * @SWG\Parameter( name="page_id", in="path", type="string", description="The requested pagination page." )
     *
     * @SWG\Parameter( name="app_version", in="query", required=true, type="string", description="The version of the app." )
     * @SWG\Parameter( name="code_version", in="query", required=true, type="string", description="The version of the code." )
     * @SWG\Parameter( name="language", in="query", required=true, type="string", description="The language being used (either en or es)." )
     * @SWG\Parameter( name="time_offset", in="query", type="string", description="Time difference with respect to GMT time." )
     *
     * @SWG\Response(
     *     response=200,
     *     description="Returns the list of payments of the month and year.",
     *     @SWG\Schema (
     *          @SWG\Property(
     *              property="data", type="array",
     *              @SWG\Items(
     *                  @SWG\Property( property="id", type="integer", description="FAQ ID", example="1" ),
     *                  @SWG\Property( property="business_name", type="string", description="Name of the business", example="Business" ),
     *                  @SWG\Property( property="business_address", type="string", description="Address of the business", example="4400 Rickenbacker Causeway, Miami, FL, 33149, EE. UU." ),
     *                  @SWG\Property( property="business_phone", type="string", description="Phone of the business", example="+306 5558 8999" ),
     *                  @SWG\Property( property="faqs", type="array",
     *                      @SWG\Items(
     *                          @SWG\Property( property="question", type="string", description="Frequently asked question", example="Pregunta 1" ),
     *                          @SWG\Property( property="answer", type="string", description="Answer to the frequently asked question", example="Answer 1" ),
     *                      )
     *                  ),
     *              ),
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
     * @SWG\Tag(name="Finances")
     */
    public function getPaymentsAction($page_id = 1)
    {
        try {
            $this->initialise();
            $data = array();

            // ToDo: pending definition.

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
        $pageFix = ($total == 0) ? 0 : intval($page);
        $totalInt = intval($total);
        $last = ceil($totalInt / 10);

        return array(
            'total' => $totalInt,
            'my_page' => $pageFix,
            'prev_page' => ($page <= 1) ? $pageFix : $page - 1,
            'next_page' => ($page >= $last) ? $last : $page + 1,
            'last_page' => $last,
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
            $pieces [] = $keyspace[random_int(0, $max)];
        }
        return implode('', $pieces);
    }

}
