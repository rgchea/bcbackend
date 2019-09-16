<?php

namespace Backend\AdminBundle\Controller;

use Backend\AdminBundle\Entity\BookingLog;
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
use Backend\AdminBundle\Entity\PropertyContractTransaction;
use Backend\AdminBundle\Entity\PropertyPhoto;
use Backend\AdminBundle\Entity\PropertyType;
use Backend\AdminBundle\Entity\TenantContract;
use Backend\AdminBundle\Entity\TermCondition;
use Backend\AdminBundle\Entity\Ticket;
use Backend\AdminBundle\Entity\TicketCategory;
use Backend\AdminBundle\Entity\TicketComment;
use Backend\AdminBundle\Entity\TicketFilePhoto;
use Backend\AdminBundle\Entity\TicketFollower;
use Backend\AdminBundle\Entity\TicketStatus;
use Backend\AdminBundle\Entity\TicketStatusLog;
use Backend\AdminBundle\Entity\TicketType;
use Backend\AdminBundle\Entity\User;
use Backend\AdminBundle\Entity\UserNotification;
use Backend\AdminBundle\Entity\BookingComment;
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
use Symfony\Component\HttpKernel\Exception\HttpException;


//entities


/**
 * Class RestController
 *
 * @Route("/api")
 *
 */
class RestController extends FOSRestController
{
    const AVATAR_UPLOADS_FOLDER = 'avatars/';
    const TICKET_UPLOADS_FOLDER = 'tickets/';
    const TENANT_ROLE_ID = 4;
    const COMMON_AREA_RESERVATION_STATUS_ID = 1;
    const TICKET_STATUS_OPEN_ID = 1;
    const TICKET_STATUS_CLOSE_ID = 2;
    const INVITATION_NOTIFICATION_TYPE_ID = 3;

    const USER_ADMIN_ROLE_ID = 1;

    const QUESTION_TYPE_OPEN_ID = 1;
    const QUESTION_TYPE_MULTIPLE_ID = 2;
    //const QUESTION_TYPE_TRUEFALSE_ID = 3;
    const QUESTION_TYPE_ONEOPTION_ID = 3;
    const QUESTION_TYPE_RATING_ID = 4;

    //const IMAGES_PATH = "https://bettercondos.space/uploads/images/";
    const IMAGES_PATH = "/uploads/images/";

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
     * Gets information about a the user .
     *
     * Returns information about the logged user.
     *
     * @Rest\Get("/v1/profile", name="profileDetail")
     *
     * @SWG\Parameter( name="Content-Type", in="header", type="string", default="application/json" )
     * @SWG\Parameter( name="Authorization", in="header", required=true, type="string", default="Bearer TOKEN", description="Authorization" )
     *
     *
     * @SWG\Parameter( name="app_version", in="query", required=true, type="string", description="The version of the app." )
     * @SWG\Parameter( name="code_version", in="query", required=true, type="string", description="The version of the code." )
     * @SWG\Parameter( name="language", in="query", required=true, type="string", description="The language being used (either en or es)." )
     * @SWG\Parameter( name="time_offset", in="query", type="string", description="Time difference with respect to GMT time." )
     *
     * @SWG\Response(
     *     response=200,
     *     description="Returns the information of a user.",
     *     @SWG\Schema (
     *          @SWG\Property(
     *              property="data", type="object",
     *              @SWG\Property( property="id", type="integer", description="ID", example="1" ),
     *              @SWG\Property( property="name", type="string", description="Name", example="Casa Modelo" ),
     *              @SWG\Property( property="email", type="string", description="email", example="w@w.com" ),
     *              @SWG\Property( property="phone", type="string", description="phone", example="56565656" ),
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

    public function getProfileAction(Request $request)
    {
        try {
            $this->initialise();

            if (!$request->headers->has('Authorization')) {
                throw new \Exception("Missing Authorization header.");
            }

            $objUser = $this->getUser();

            $data = array(
                'id' => $objUser->getId(),
                'name' => $objUser->getName(),
                'email' => $objUser->getEmail(),
                'phone' => $objUser->getMobilePhone(),
                'avatar_url' => $objUser->getAvatarPath(),
            );

            return new JsonResponse(array('message' => "", 'data' => $data));
        } catch (Exception $ex) {
            return new JsonResponse(array('message' => $ex->getMessage()), JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }



    /**
     * Updates a user info.
     *
     * UPDATES a user.
     *
     * @Rest\Put("/v1/profile", name="profileUpdate")
     *
     * @SWG\Parameter( name="Content-Type", in="header", required=true, type="string", default="application/json" )
     * @SWG\Parameter( name="Authorization", in="header", required=true, type="string", default="Bearer TOKEN", description="Authorization" )
     *
     * @SWG\Parameter( name="name", in="body", type="string", required=true, description="Renato Chea", schema={} )
     * @SWG\Parameter( name="email", in="body", type="string", required=true, description="w@w.com", schema={} )
     * @SWG\Parameter( name="phone", in="body", type="string", required=true, description="454545455454", schema={} )
     *
     * @SWG\Parameter( name="app_version", in="query", required=true, type="string", description="The version of the app." )
     * @SWG\Parameter( name="code_version", in="query", required=true, type="string", description="The version of the code." )
     * @SWG\Parameter( name="language", in="query", required=true, type="string", description="The language being used (either en or es)." )
     * @SWG\Parameter( name="time_offset", in="query", type="string", description="Time difference with respect to GMT time." )
     *
     * @SWG\Response(
     *     response=200,
     *     description="updates user info.",
     *     @SWG\Schema (
     *          @SWG\Property( property="message", type="string", example="" )
     *      )
     * )
     *
     * @SWG\Response(
     *     response=409,
     *     description="email already exists",
     *     @SWG\Schema (
     *          @SWG\Property( property="message", type="string", example="error" )
     *      )
     * )     *
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

    public function putProfileAction(Request $request)
    {
        try {
            $this->initialise();

            if (!$request->headers->has('Authorization')) {
                throw new \Exception("Missing Authorization header.");
            }

            if (!$request->headers->has('Content-Type')) {
                throw new \Exception("Missing Content-Type header.");
            }

            $name = trim($request->get('name'));
            $email = trim($request->get('email'));
            $phone = trim($request->get('phone'));

            $objUser = $this->getUser();

            $checkExistence = $this->get('services')->checkExistence($email, $objUser->getId());

            if($checkExistence != ""){
                return new JsonResponse(array('message' => $checkExistence), JsonResponse::HTTP_CONFLICT);//409
            }

            $objUser->setName($name);
            $objUser->setEmail($email);
            $objUser->setUsername($email);
            $objUser->setMobilePhone($phone);

            $this->get("services")->blameOnMe($objUser, "update");

            $this->em->persist($objUser);
            $this->em->flush();

            return new JsonResponse(array(
                'message' => "",
            ));
        } catch (Exception $ex) {
            return new JsonResponse(array('message' => $ex->getMessage()), JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }




    /**
     * Updates a user password.
     *
     * UPDATES a user.
     *
     * @Rest\Put("/v1/changePassword", name="changePassword")
     *
     * @SWG\Parameter( name="Content-Type", in="header", required=true, type="string", default="application/json" )
     * @SWG\Parameter( name="Authorization", in="header", required=true, type="string", default="Bearer TOKEN", description="Authorization" )
     *
     * @SWG\Parameter( name="old_password", in="body", type="string", required=true, description="123456", schema={} )
     * @SWG\Parameter( name="new_password", in="body", type="string", required=true, description="123456", schema={} )
     *
     * @SWG\Parameter( name="app_version", in="query", required=true, type="string", description="The version of the app." )
     * @SWG\Parameter( name="code_version", in="query", required=true, type="string", description="The version of the code." )
     * @SWG\Parameter( name="language", in="query", required=true, type="string", description="The language being used (either en or es)." )
     * @SWG\Parameter( name="time_offset", in="query", type="string", description="Time difference with respect to GMT time." )
     *
     * @SWG\Response(
     *     response=200,
     *     description="updates user password.",
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

    public function putChangePasswordAction(Request $request)
    {
        try {
            $this->initialise();

            if (!$request->headers->has('Authorization')) {
                throw new \Exception("Missing Authorization header.");
            }

            if (!$request->headers->has('Content-Type')) {
                throw new \Exception("Missing Content-Type header.");
            }

            $oldPassword = trim($request->get('old_password'));
            $newPassword = trim($request->get('new_password'));


            $objUser = $this->getUser();

            $encoder = $this->get('security.password_encoder');
            $match = $encoder->isPasswordValid($objUser, $oldPassword);

            if(!$match){
                return new JsonResponse(array('message' => "Invalid current password"));
                //return new JsonResponse(array('message' => "Invalid current password"), JsonResponse::HTTP_UNPROCESSABLE_ENTITY);
            }
            else{
                $objUser->setPlainPassword($newPassword);
                //$objUser->setPassword($encoder->encodePassword($objUser, $newPassword));
            }

            $this->get("services")->blameOnMe($objUser, "update");

            $this->em->persist($objUser);
            $this->em->flush();

            return new JsonResponse(array(
                'message' => "",
            ));
        } catch (Exception $ex) {
            return new JsonResponse(array('message' => $ex->getMessage()), JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
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
            $this->translator->setLocale($lang);

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

            // User existence
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

            // Checking for Invite User Notifications for the registering email
            /** @var TenantContractRepository $tenantRepo */
            $tenantRepo = $this->em->getRepository('BackendAdminBundle:TenantContract');
            $tenantContracts = $tenantRepo->getApiRegister($email);

            //$this->translator->setLocale($lang);
            //$description = $this->translator->trans("label_invite_accepted_notification");

            foreach ($tenantContracts as $tenantContract) {
                $tenantContract->setUser($user);
                $this->get("services")->blameOnMe($tenantContract, "update");

                if($tenantContract->getMainTenant()){
                    //$objProperty = $tenantContract->getProperty();
                    //$objProperty->setMainTenant($user);
                    //$this->em->persist($objProperty);

                }

                $this->em->persist($tenantContract);
            }

            // Gamification

            $body = [
                'email' => $user->getEmail(),
                'username' => $user->getEmail(),
                'firstName' => $user->getName(),
                'lastName' => $user->getName(),
                'locale' => $lang,
            ];


            $token = $this->get('services')->getBCToken();
            $gamificationResponse = $this->callGamificationService( "POST", "users", $body );

            //var_dump($gamificationResponse);die;

            // Flushing to DB
            $this->em->flush();

            // Sending Email
            $this->translator->setLocale($lang);
            $subject = $this->translator->trans('mail.register_subject');
            $bodyHtml = "<b>" . $this->translator->trans('mail.label_user') . "</b> " . $user->getUsername() . "<br/>";
            $bodyHtml .= "<b>" . $this->translator->trans('mail.label_password') . "</b> " . $password . "<br/><br/>";
            $bodyHtml .= $this->translator->trans('mail.register_body');

            //$message = $this->get('services')->generalTemplateMail($subject, $user->getEmail(), $bodyHtml);

            return new JsonResponse(array(
                'message' => "register"
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
     *                  @SWG\Property( property="name_code", type="string", description="Name code", example="GT" ),
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
                    'name_code' => $country->getShortName(),
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

            $lang = strtolower(trim($request->get('language')));
            $this->translator->setLocale($lang);

            if (!$request->headers->has('Content-Type')) {
                throw new \Exception("Missing Content-Type header.");
            }

            $propertyCode = strtolower(trim($request->get('property_code')));
            $user = $this->getUser();


            $tenantContract = $this->em->getRepository('BackendAdminBundle:TenantContract')->findOneByPropertyCode($propertyCode);
            if ($tenantContract == null) {
                throw new \Exception("Invalid property code.");
            }

            $property = $tenantContract->getPropertyContract()->getProperty();
            $propertyContract = $tenantContract->getPropertyContract();
            $type = $property->getPropertyType();
            $typeId = 0;
            if ($type != null) {
                $typeId = $type->getId();
            }
            $complexSector = $property->getComplexSector();
            $complexSectorId = 0;
            if ($complexSector != null) {
                $complexSectorId = $complexSector->getId();
            }


            // From validate SMS ...
            /*
            $contracts = $this->em->getRepository('BackendAdminBundle:PropertyContract')->getApiWelcomePrivateKey($property);

            if (count($contracts) <= 0) {
                //throw new \Exception("No available contracts.");
                $response = [
                    'code' => 401,
                    'error' => true,
                    'data' => "No previous contract found",
                ];

                return new JsonResponse($response);

            } else if (count($contracts) > 1) {
                throw new \Exception("One or more active contracts.");
            }

            $contract = $contracts[0];

            $tenants = $this->em->getRepository('BackendAdminBundle:TenantContract')->findOneBy(array('enabled' => true, 'propertyContract' => $contract));

            if (count($tenants) > 0) {
                throw new \Exception("A tenant is already owner.");
            }

            $tenant = new TenantContract();
            */

            $role = $this->em->getRepository('BackendAdminBundle:Role')->findOneById(self::TENANT_ROLE_ID);

            $tenantContract->setUser($this->getUser());
            //$tenant->setRole($role);
            //$tenant->setPropertyContract($contract);
            //$tenant->setIsOwner(false);
            $tenantContract->setEnabled(true);
            $tenantContract->setInvitationAccepted(true);

            ////rchea comment
            //$property->setOwner($this->getUser());

            //$this->get("services")->blameOnMe($property, "update");
            //$this->get("services")->blameOnMe($tenant, "create");
            $this->get("services")->blameOnMe($tenantContract, "update");

            $this->em->persist($tenantContract);


            $description = $this->translator->trans("label_invite_accepted_notification");

            $notification = $this->createInviteUserNotification($tenantContract, $user,  $tenantContract->getCreatedBy(), $description);
            $this->em->persist($tenantContract);
            $this->em->persist($notification);


            //$this->em->persist($property);
            $this->em->flush();

            //add player gamification
            $propertyTeamID = $tenantContract->getPropertyContract()->getProperty()->getTeamCorrelative();
            $tenantEmail = $tenantContract->getUser()->getEmail();
            $body = array();
            $userTeam = $this->get('services')->callBCSpace("POST", "users/{$tenantEmail}/teams/{$propertyTeamID}", $body);
            //print "<pre>";
            //var_dump($userTeam);die;
            if($userTeam){
                $tenantContract->setPlayerId($userTeam["id"]);
            }
            $this->em->persist($tenantContract);
            $this->em->flush();


            ///ADD POINTS TO PLAYER
            $message = $this->translator->trans('label_new')." ".$this->translator->trans("label_property"). " ".$property->getPropertyNumber() ;
            $playKey = "BC-T-00001";//registering your property
            $this->get("services")->addPoints($tenantContract, $message, $playKey);


            $data = array(
                'id' => $property->getId(),
                'tenant_id' => $tenantContract->getId(),
                'code' => $tenantContract->getPropertyCode(), 'name' => $property->getName(),
                'address' => $property->getAddress(), 'type_id' => $typeId,
                'sector_id' => $complexSectorId, 'teamCorrelative' => $property->getTeamCorrelative(),
                'complex' => $property->getComplex()->getName(),
                'complex_id' => $property->getComplex()->getId()
                );

            return new JsonResponse(array('message' => "welcomePrivateKey", 'data' => $data,));
        } catch (Exception $ex) {
            return new JsonResponse(array('message' => $ex->getMessage()), JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }


    /**
     *  READ Updates a user notification.
     *
     * UPDATES a user.
     *
     * @Rest\Put("/v1/readNotification", name="readNotification")
     *
     * @SWG\Parameter( name="Content-Type", in="header", required=true, type="string", default="application/json" )
     * @SWG\Parameter( name="Authorization", in="header", required=true, type="string", default="Bearer TOKEN", description="Authorization" )
     *
     * @SWG\Parameter( name="id", in="body", type="integer", required=true, description="user notification ID", schema={} )
     *
     * @SWG\Parameter( name="app_version", in="query", required=true, type="string", description="The version of the app." )
     * @SWG\Parameter( name="code_version", in="query", required=true, type="string", description="The version of the code." )
     * @SWG\Parameter( name="language", in="query", required=true, type="string", description="The language being used (either en or es)." )
     * @SWG\Parameter( name="time_offset", in="query", type="string", description="Time difference with respect to GMT time." )
     *
     * @SWG\Response(
     *     response=200,
     *     description="updates user notification.",
     *     @SWG\Schema (
     *          @SWG\Property( property="message", type="string", example="" )
     *      )
     * )
     *
     * @SWG\Response(
     *     response=409,
     *     description="email already exists",
     *     @SWG\Schema (
     *          @SWG\Property( property="message", type="string", example="error" )
     *      )
     * )     *
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

    public function putReadNotificationAction(Request $request)
    {
        try {
            $this->initialise();

            if (!$request->headers->has('Authorization')) {
                throw new \Exception("Missing Authorization header.");
            }

            if (!$request->headers->has('Content-Type')) {
                throw new \Exception("Missing Content-Type header.");
            }

            $id = intval($request->get('id'));

            $notification = $this->em->getRepository('BackendAdminBundle:UserNotification')->findOneById($id);

            if ($notification == null) {
                throw new \Exception("Invalid notification.");
            }

            $notification->setIsRead(1);
            $this->get("services")->blameOnMe($notification, "update");

            $this->em->persist($notification);
            $this->em->flush();

            return new JsonResponse(array(
                'message' => "ok",
            ));
        } catch (Exception $ex) {
            return new JsonResponse(array('message' => $ex->getMessage()), JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }


    /**
     *  LIKE Updates a user ticket comment.
     *
     * UPDATES a ticket comment.
     *
     * @Rest\Put("/v1/likeTicketComment", name="likeTicketComment")
     *
     * @SWG\Parameter( name="Content-Type", in="header", required=true, type="string", default="application/json" )
     * @SWG\Parameter( name="Authorization", in="header", required=true, type="string", default="Bearer TOKEN", description="Authorization" )
     *
     * @SWG\Parameter( name="ticket_comment_id", in="body", type="integer", required=true, description="ticket comment ID", schema={} )
     * @SWG\Parameter( name="like", in="body", type="integer", required=true, description="1 is like 0 is unlike", schema={} )
     *
     * @SWG\Parameter( name="app_version", in="query", required=true, type="string", description="The version of the app." )
     * @SWG\Parameter( name="code_version", in="query", required=true, type="string", description="The version of the code." )
     * @SWG\Parameter( name="language", in="query", required=true, type="string", description="The language being used (either en or es)." )
     * @SWG\Parameter( name="time_offset", in="query", type="string", description="Time difference with respect to GMT time." )
     *
     * @SWG\Response(
     *     response=200,
     *     description="updates ticket comment.",
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

    public function putLikeTicketCommentAction(Request $request)
    {
        try {
            $this->initialise();

            if (!$request->headers->has('Authorization')) {
                throw new \Exception("Missing Authorization header.");
            }

            if (!$request->headers->has('Content-Type')) {
                throw new \Exception("Missing Content-Type header.");
            }

            $ticketCommentID = intval($request->get('ticket_comment_id'));
            $like = intval($request->get('like'));


            $ticketComment = $this->em->getRepository('BackendAdminBundle:TicketComment')->findOneById($ticketCommentID);

            if ($ticketComment == null) {
                throw new \Exception("Invalid ticket comment ID.");
            }

            if($like){
                $ticketComment->setLikedBy($this->getUser());
            }
            else{//unlike
                $ticketComment->setLikedBy(NULL);
            }

            $this->get("services")->blameOnMe($ticketComment, "update");

            $this->em->persist($ticketComment);
            $this->em->flush();

            return new JsonResponse(array(
                'message' => "ok",
            ));
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
     *                  @SWG\Property( property="player_id", type="integer", description="Team player ID", example="1" ),
     *                  @SWG\Property( property="complex", type="integer", description="Complex Name", example="complex 1" ),
     *                  @SWG\Property( property="complex_id", type="integer", description="Complex ID", example="1" ),
     *                  @SWG\Property( property="sector_id", type="integer", description="Sector ID", example="1" ),
     *                  @SWG\Property( property="tenant_contract_id", type="integer", description="Tenant contract ID", example="1" ),
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

            $contracts = $this->em->getRepository('BackendAdminBundle:TenantContract')->getApiProperties($this->getUser());
            $total = $this->em->getRepository('BackendAdminBundle:TenantContract')->countApiProperties($this->getUser());

            /** @var Property $property */
            foreach ($contracts as $contract) {
                $type = $contract->getPropertyContract()->getProperty()->getPropertyType();
                if ($type == null) {
                    $type = new PropertyType();
                }
                $complexSector = $contract->getPropertyContract()->getProperty()->getComplexSector();
                if ($complexSector == null) {
                    $complexSector = new ComplexSector();
                }

                $data[] = array(
                        'id' => $contract->getPropertyContract()->getProperty()->getId(),
                        'code' => $contract->getPropertyContract()->getProperty()->getCode(),
                        'name' => $contract->getPropertyContract()->getProperty()->getName(),
                        'address' => $contract->getPropertyContract()->getProperty()->getAddress(),
                        'type_id' => $type->getId(),
                        'player_id' => $contract->getPlayerId(),
                        'complex' => $contract->getPropertyContract()->getProperty()->getComplex()->getName(),
                        'complex_id' => $contract->getPropertyContract()->getProperty()->getComplex()->getId(),
                        'sector_id' => $complexSector->getId(),
                        'tenant_contract_id' => $contract->getId()
                        );
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
     *              @SWG\Property( property="property_contract_id", type="integer", description="property contract ID", example="1" ),
     *              @SWG\Property( property="id", type="integer", description="ID", example="1" ),
     *              @SWG\Property( property="property_number", type="string", description="property number", example="101" ),
     *              @SWG\Property( property="name", type="string", description="Name", example="Casa Modelo" ),
     *              @SWG\Property( property="address", type="string", description="Address", example="1 Avenue des Champs-Elysees" ),
     *              @SWG\Property( property="type", type="string", description="Property Type ", example="Apartment, warehouse" ),
     *              @SWG\Property( property="is_main_account", type="boolean", description="If it is the main tenant", example="true" ),
     *              @SWG\Property(
     *                  property="main_account", type="array",
     *                  @SWG\Items(
     *                      @SWG\Property( property="name", type="string", description="name", example="Arturo C" ),
     *                      @SWG\Property( property="email", type="string", description="email", example="a@a.com" ),
     *                      @SWG\Property( property="phone_code", type="string", description="country code", example="502" ),
     *                      @SWG\Property( property="phone", type="string", description="phone", example="565788484" ),
     *                      @SWG\Property( property="avatar_url", type="string", description="url", example="" ),
     *                  )
     *              ),
     *
     *              @SWG\Property(
     *                  property="invitees", type="array",
     *                  @SWG\Items(
     *                      @SWG\Property( property="name", type="string", description="name", example="Arturo C" ),
     *                      @SWG\Property( property="email", type="string", description="email", example="a@a.com" ),
     *                      @SWG\Property( property="phone_code", type="string", description="country code", example="502" ),
     *                      @SWG\Property( property="phone", type="string", description="country code", example="2434534534" ),
     *                      @SWG\Property( property="avatar_url", type="string", description="url", example="" ),
     *                      @SWG\Property( property="invitation_accepted", type="boolean", description="", example="" ),
     *                  )
     *              ),
     *          ),
     *
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

    public function getPropertyDetailAction(Request $request, $id)
    {
        try {
            $this->initialise();

            $lang = strtolower(trim($request->get('language')));

            /** @var Property $property */
            $property = $this->em->getRepository('BackendAdminBundle:Property')->getApiPropertyDetail($id, $this->getUser());
            if ($property == null) {
                throw new \Exception("Invalid property code.");
            }

            $contract = $this->em->getRepository('BackendAdminBundle:PropertyContract')->findOneBy(
                array('enabled' => true, 'property' => $property, 'isActive' => true)
                //array('id', 'DESC')
            );


            if (!$contract) {
                throw new \Exception("No available contracts for this property.");
            }
            /** @var PropertyContract $contract */
            //$contract = $contracts[0];

            $type = $property->getPropertyType();
            if ($type == null) {
                $type = new PropertyType();
            }

            $owner = $contract->getMainTenantContract()->getOwner();
            if ($owner == null) {
                $owner = new User();
            }



            /*
            $photosFull = $this->em->getRepository('BackendAdminBundle:PropertyPhoto')->findBy(
                array('enabled' => true, 'property' => $property)
            );

            $photos = array();

            foreach ($photosFull as $photo) {
                $photos[] = $photo->getPhotoPath();
            }
            */
            $tenantContracts = $this->em->getRepository('BackendAdminBundle:TenantContract')->findBy(array("propertyContract" => $contract, "mainTenant" => 0, "enabled" => true), array("id" => "DESC"));
            $mainAccount = $contract->getMainTenantContract();

            $isMainAccount = $this->getUser()->getId() == $mainAccount->getUser()->getId() ? true : false;

            $data = array(
                'property_contract_id' => $contract->getId(),
                'id' => $property->getId(),
                'property_number' => $property->getPropertyNumber(),
                //'code' => $tenantContract->getPropertyCode(),
                'name' => $property->getName(),
                'address' => $property->getAddress(),
                'type' => ($lang == 'en') ? $type->getNameEN() : $type->getNameES(),
                'is_main_account' => $isMainAccount,
                'property_contract_id' => $contract->getId(),
                //'photos' => $photos,
                'main_account' => array('name' => $mainAccount->getUser()->getName(),
                                        'email' => $mainAccount->getUser()->getEmail(),
                                        'phone_code' => $mainAccount->getUser()->getGeoCountry()->getCode(),
                                        'phone' => $mainAccount->getUser()->getMobilePhone(),
                                        'avatar_url' => $mainAccount->getUser()->getAvatarPath()
                                    )
            );


            //GET INVITEES
            $data["invitees"] = array();
            /** @var TenantContract $contract */
            foreach ($tenantContracts as $contract) {
                $data["invitees"][] = array(
                    'tenant_contract_id' => $contract->getId(),
                    'name' => ($contract->getUser() != null) ? $contract->getUser()->getName() : "",
                    'email' => ($contract->getUser() != null) ? $contract->getUser()->getEmail() : $contract->getInvitationUserEmail(),
                    'phone_code' => $mainAccount->getUser()->getGeoCountry()->getCode(),
                    'phone' => ($contract->getUser() != null) ? $contract->getUser()->getMobilePhone() : "",
                    'avatar_url' => ($contract->getUser() != null) ?  $contract->getUser()->getAvatarPath(): "",
                    'invitation_accepted' => $contract->getInvitationAccepted(),

                );
            }

            return new JsonResponse(array('message' => "", 'data' => $data));
        } catch (Exception $ex) {
            return new JsonResponse(array('message' => $ex->getMessage()), JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }


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
     *                  @SWG\Property( property="id", type="integer", description="notification ID", example="1" ),
     *                  @SWG\Property( property="ticket_id", type="integer", description="Ticket ID if any", example="1" ),
     *                  @SWG\Property( property="common_area_reservation_id", type="integer", description="Common Area Reservation ID if any", example="1" ),
     *                  @SWG\Property( property="tenant_contract_id", type="integer", description="Tenant Contract ID if any", example="1" ),
     *                  @SWG\Property( property="user", type="object",
     *                      @SWG\Property( property="avatar_path", type="string", description="Avatar Path", example="/avatars/1.jpg" ),
     *                      @SWG\Property( property="username", type="string", description="Username", example="user1" ),
     *                      @SWG\Property( property="user_fullname", type="string", description="User full name", example="Diego Maradona" ),
     *                      @SWG\Property( property="role", type="string", description="Role of the User", example="Role" ),
     *                  ),
     *
     *                  @SWG\Property( property="user_notification", type="object",
     *                      @SWG\Property( property="type_id", type="integer", description="Type of Notification ID", example="1" ),
     *                      @SWG\Property( property="type", type="string", description="Type of Notification", example="accept_invitation" ),
     *                      @SWG\Property( property="title", type="string", description="tile", example="Notification title" ),
     *                      @SWG\Property( property="description", type="string", description="Description", example="Notification Description" ),
     *                      @SWG\Property( property="replies_quantity", type="integer", description="Quantity of replies for the associated ticket", example="10" ),
     *                      @SWG\Property( property="timestamp", type="string", description="Timestamp GMT formatted with Unix Time (https://en.wikipedia.org/wiki/Unix_time)", example="1272509157" ),
     *                      @SWG\Property( property="notice", type="string", description="Notification notice", example="Notice" ),
     *                  )
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

                if ($notification->getTicket() != null) {
                    $id = $notification->getTicket()->getId();
                    $ticketIds[] = $id;

                }
            }
            $ticketIds = array_unique($ticketIds);

            $preComments = $this->em->getRepository('BackendAdminBundle:TicketComment')->getApiCountPerTickets($ticketIds);
            $commentsReplies = array();
            foreach ($preComments as $comment) {
                $commentsReplies[$comment['id']] = $comment['count'];
            }

            /** @var UserNotification $notification */
            foreach ($notifications as $notification) {
                /** @var User $user */
                $user = $notification->getCreatedBy();
                if ($user == null) {
                    $user = new User();
                }
                $type = $notification->getNotificationType();
                if ($type == null) {
                    $type = new NotificationType();
                }

                $ticketId = 0;
                if ($notification->getTicket() != null) {
                    $ticketId = $notification->getTicket()->getId();
                }
                $commonAreaReservationId = 0;
                if ($notification->getCommonAreaReservation() != null) {
                    $commonAreaReservationId = $notification->getCommonAreaReservation()->getId();
                }
                $tenantContractId = 0;
                $propertyID = 0;
                if ($notification->getTenantContract() != null) {
                    $tenantContractId = $notification->getTenantContract()->getId();
                    $propertyID = $notification->getTenantContract()->getPropertyContract()->getProperty()->getId();
                }



                $createdAt =  strtotime($user->getCreatedAt()->format("Y-m-d H:i:s"));


                $data[] = array(
                    'id' => $notification->getId(),
                    'ticket_id' => $ticketId,
                    'common_area_reservation_id' => $commonAreaReservationId,
                    'tenant_contract_id' => $tenantContractId,
                    'property_id' => $propertyID,
                    'user' => array(
                        'avatar_path' => $user->getAvatarPath(),
                        'username' => $user->getUsername(),
                        'user_fullname' => $user->getName(),
                        'role' => (($lang == 'en') ? $user->getRole()->getName() : $user->getRole()->getNameES()),
                    ),
                    'notification' => array(
                        'type_id' => $type->getId(),
                        'type' => (($lang == 'en') ? $type->getNameEN() : $type->getNameES()),
                        'title' => $notification->getTitle(),
                        'description' => $notification->getDescription(),
                        'replies_quantity' => (array_key_exists($ticketId, $commentsReplies)) ? $commentsReplies[$ticketId] : 0,
                        'createdAt' => $notification->getCreatedAt()->getTimestamp(),
                        //'createdAt' => $createdAt,
                        'notice' => $notification->getNotice(),
                    ),
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
     *                  @SWG\Property( property="icon_class", type="string", description="CLASS for the category's icon", example="fas fa-wrench" ),
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

                $iconClass = ($category->getIcon() != null) ? $category->getIcon()->getIconClass() : "";

                $data[] = array(
                    'category_id' => $category->getId(),
                    'category_name' => $category->getName(),
                    'icon_class' => $iconClass,
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
     *                  @SWG\Property( property="user_fullname", type="string", description="Ticket's creator name", example="Firstname Lastname" ),
     *                  @SWG\Property( property="role", type="string", description="Ticket's creator role", example="Admin" ),
     *                  @SWG\Property( property="timestamp", type="string", description="Ticket created timestamp GMT formatted with Unix Time (https://en.wikipedia.org/wiki/Unix_time)", example="1272509157" ),
     *                  @SWG\Property( property="followers_quantity", type="string", description="Amount of followers for the ticket", example="2" ),
     *                  @SWG\Property( property="comments_quantity", type="string", description="Ammount of comments for the ticket", example="3" ),
     *                  @SWG\Property( property="reservation", type="object",
     *                      @SWG\Property( property="id", type="string", description="Common area ID", example="1" ),
     *                      @SWG\Property( property="common_area", type="string", description="Common area name", example="Common area" ),
     *                      @SWG\Property( property="reservation_status", type="string", description="Common area reservation status", example="" ),
     *                      @SWG\Property( property="reservation_from", type="string", description="Common area reservation from date", example="1272509157" ),
     *                      @SWG\Property( property="reservation_to", type="string", description="Common area reservation to date", example="1272519157" ),
     *                  ),

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

            $objProperty = $this->em->getRepository('BackendAdminBundle:Property')->find($property_id);

            $tickets = $this->em->getRepository('BackendAdminBundle:Ticket')->getApiFeed($objProperty->getComplex()->getId(), $property_id, $filter_category_id, $this->getUser(), $page_id);
            $total = $this->em->getRepository('BackendAdminBundle:Ticket')->countApiFeed($objProperty->getComplex()->getId(), $property_id, $filter_category_id, $this->getUser());

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


            ////
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


                ///get one photo
                ///
                $photo = "";
                $objPhoto = $this->em->getRepository('BackendAdminBundle:TicketFilePhoto')->findOneByTicket($ticket->getId());
                if ($objPhoto) {
                    $photo = $objPhoto->getPhotoPath();
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
                        "id" => $reservation->getId(),
                        "common_area" => $commonArea->getName(),
                        "status" => (($lang == 'en') ? $reservationStatus->getNameEN() : $reservationStatus->getNameES()),
                        "reservation_from" => $reservation->getReservationDateFrom()->getTimestamp(),
                        "reservation_to" => $reservation->getReservationDateTo()->getTimestamp(),
                        "comments_quantity" => $this->em->getRepository("BackendAdminBundle:BookingComment")->getCommentsQuantity($ticket->getCommonAreaReservation()->getId())
                    );
                }

                if ($user->getRole() != null) {
                    $role = (($lang == 'en') ? $user->getRole()->getName() : $user->getRole()->getNameES());
                } else {
                    $role = "";
                }

                $iconClass = ($ticket->getTicketCategory()->getIcon() != null) ? $ticket->getTicketCategory()->getIcon()->getIconClass() : "";

                $like = $this->em->getRepository('BackendAdminBundle:TicketFollower')->findOneBy(array('enabled' => true, 'ticket' => $ticket->getId(), 'user' => $this->getUser()->getId()));
                if($like != NULL){
                    $like = 1;
                }
                else{
                    $like = 0;
                }

                if($ticket->getTicketCategory()){
                    $myCategory = array("category_id" => $ticket->getTicketCategory()->getId(),
                        "category_name" => $ticket->getTicketCategory()->getName(),
                        "icon_class" => $iconClass,
                        "color" => $ticket->getTicketCategory()->getColor());
                }
                else{
                    $myCategory = array();

                }

                $data[] = array(
                    'id' => $ticket->getId(),
                    'type_id' => $type->getId(),
                    'type_name' => $type->getName(),
                    //'status' => (($lang == 'en') ? $status->getNameEN() : $status->getNameES()),
                    'status' => $status->getId(),
                    'category' => $myCategory,
                    'title' => $ticket->getTitle(),
                    'description' => $ticket->getDescription(),
                    'is_public' => $ticket->getIsPublic(),
                    'username' => $user->getUsername(),
                    'user_fullname' => $user->getName(),
                    'photo' => $photo,
                    'role' => $role,
                    'timestamp' => $ticket->getCreatedAt()->getTimestamp(),
                    'followers_quantity' => (array_key_exists($ticket->getId(), $followers)) ? $followers[$ticket->getId()] : 0,
                    'like' => $like,
                    'comments_quantity' => (array_key_exists($ticket->getId(), $comments)) ? $comments[$ticket->getId()] : 0,
                    'reservation' => $commonAreaData,
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
     *
     *
     *                  @SWG\Property( property="id", type="string", description="Ticket ID", example="1" ),
     *                  @SWG\Property( property="type_id", type="string", description="Ticket type ID", example="1" ),
     *                  @SWG\Property( property="type_name", type="string", description="Ticket type name", example="TicketTypeName" ),
     *                  @SWG\Property( property="solved_at", type="string", description="Unix timestamp", example="1566232199" ),
     *                  @SWG\Property( property="solved_by", type="string", description="User name", example="Rolegio Rodas" ),
     *                  @SWG\Property( property="solved_by_avatar_url", type="string", description="url", example="" ),
     *                  @SWG\Property( property="status", type="string", description="Ticket status", example="Status" ),
     *                  @SWG\Property( property="category", type="array",
     *                      @SWG\Items(
     *                          @SWG\Property( property="category_id", type="string", description="Category ID", example="1" ),
     *                          @SWG\Property( property="category_name", type="string", description="Category area name", example="Fixes" ),
     *                          @SWG\Property( property="icon_class", type="string", description="icon class", example="fas fa-pencil" ),
     *                          @SWG\Property( property="color", type="string", description="hexa color code", example="#ffffff" ),
     *                      )
     *
     *                  ),
     *                  @SWG\Property( property="title", type="string", description="Ticket title", example="TicketTile" ),
     *                  @SWG\Property( property="description", type="string", description="Ticket description", example="Lorem ipsum." ),
     *                  @SWG\Property( property="is_public", type="boolean", description="Is ticket public?", example="true" ),
     *                  @SWG\Property( property="username", type="string", description="Ticket's creator username", example="admin" ),
     *                  @SWG\Property( property="user_fullname", type="string", description="Ticket's creator name", example="Firstname Lastname" ),
     *                  @SWG\Property( property="timestamp", type="string", description="Ticket created timestamp GMT formatted with Unix Time (https://en.wikipedia.org/wiki/Unix_time)", example="1272509157" ),
     *                  @SWG\Property( property="followers_quantity", type="string", description="Amount of followers for the ticket", example="2" ),*
     *                  @SWG\Property( property="comments_quantity", type="string", description="Ammount of comments for the ticket", example="3" ),
     *                  @SWG\Property( property="comments", type="array",
     *                      @SWG\Items(
     *                          @SWG\Property( property="id", type="integer", description="Comment ID", example="2" ),
     *                          @SWG\Property( property="username", type="string", description="Comments's creator username", example="2" ),
     *                          @SWG\Property( property="timestamp", type="string", description="Comment's creation time", example="1272509157" ),
     *                          @SWG\Property( property="like", type="string", description="User that liked the comment", example="user2" ),
     *                          @SWG\Property( property="comment", type="string", description="Comment content", example="Comment" ),
     *                          @SWG\Property( property="avatar_url", type="string", description="URL for the avatar", example="/avatars/1.jpg" )
     *                      )
     *                  ),
     *                  @SWG\Property( property="common_area", type="array",
     *                      @SWG\Items(
     *                          @SWG\Property( property="id", type="string", description="Common area ID", example="1" ),
     *                          @SWG\Property( property="name", type="string", description="Common area name", example="Common area" ),
     *                          @SWG\Property( property="reservation_status", type="string", description="Common area reservation status", example="" ),
     *                          @SWG\Property( property="reservation_from", type="string", description="Common area reservation from date", example="1272509157" ),
     *                          @SWG\Property( property="reservation_to", type="string", description="Common area reservation to date", example="1272519157" ),
     *                      )
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


            $status = $ticket->getTicketStatus();
            if ($status == null) {
                $status = new TicketStatus();
            }
            $ticketUser = $ticket->getCreatedBy();
            if ($ticketUser == null) {
                $ticketUser = new User();
            }


            $iconClass = ( $ticket->getTicketCategory()->getIcon() != null) ?  $ticket->getTicketCategory()->getIcon()->getIconClass() : "";

            $data = array(
                'id' => $ticket->getId(),
                'type_id' => $ticket->getTicketType()->getId(),
                'type_name' => $ticket->getTicketType()->getName(),
                'status' => $status->getId(),
                'solved_at' => $ticket->getUpdatedAt()->getTimestamp(),
                'solved_by' => $ticket->getUpdatedBy()->getName(),
                'solved_by_avatar_url' => $ticket->getUpdatedBy()->getAvatarPath(),
                'category' =>
                    array("category_id" => $ticket->getTicketCategory()->getId(),
                        "category_name" => $ticket->getTicketCategory()->getName(),
                        "icon_class" => $iconClass,
                        "color" => $ticket->getTicketCategory()->getColor()),
                'title' => $ticket->getTitle(),
                'description' => $ticket->getDescription(),
                'is_public' => $ticket->getIsPublic(),
                'username' => $ticketUser->getUsername(),
                'user_fullname' => $ticketUser->getName(),
                'timestamp' => $ticket->getCreatedAt()->getTimestamp(),
                'followers_quantity' => (array_key_exists($ticket->getId(), $followersCount)) ? $followersCount[$ticket->getId()] : 0,
                'comments_quantity' => (array_key_exists($ticket->getId(), $commentsCount)) ? $commentsCount[$ticket->getId()] : 0,

            );

            //get ticket photos
            $photos = $this->em->getRepository('BackendAdminBundle:TicketFilePhoto')->findBy(array("ticket" => $ticket_id, "enabled" => 1));
            $data['photos'] = array();

            foreach ($photos as $photo) {
                $data['photos'][] = $photo->getPhotoPath();
            }

            // Fetching the ticket comments
            $comments = $this->em->getRepository('BackendAdminBundle:TicketComment')->getApiSingleTicketComments($ticket_id);

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
                    'id' => $comment->getId(),
                    'username' => $commentUser->getUsername(),
                    'user_fullname' => $commentUser->getName(),
                    'timestamp' => $comment->getCreatedAt()->getTimestamp(),
                    'like' => $likeUser->getUsername(),
                    'comment' => $comment->getCommentDescription(),
                    'avatar_url' => $commentUser->getAvatarPath(),
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
     * @SWG\Parameter( name="sector_id", in="body", required=true, type="integer", description="The sector ID of the ticket.", schema={} )
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

        $myPhotoPath = self::IMAGES_PATH.self::TICKET_UPLOADS_FOLDER;


        try {
            $this->initialise();

            $lang = strtolower(trim($request->get('language')));
            $this->translator->setLocale($lang);


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
            $ticket->setTicketType($this->em->getRepository('BackendAdminBundle:TicketType')->find(1));
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


            //setAssignedTo
            $timezone  = intval($request->get('time_offset')); //(GMT -5:00) EST (U.S. & Canada)
            $userToAssign = $this->em->getRepository('BackendAdminBundle:Shift')->getUsertoAssignTicket($timezone, $complexSector->getComplex()->getId());


            $ticket->setAssignedTo($userToAssign);

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

                //var_dump($photo);die;

                $photo = str_replace('data:image/png;base64,', '', $photo);
                $photo = str_replace('data:image/jpg;base64,', '', $photo);
                $photo = str_replace('data:image/jpeg;base64,', '', $photo);
                $photo = str_replace(' ', '+', $photo);
                $decodedPhoto = base64_decode($photo);

                $tmpPath = sys_get_temp_dir() . '/sf_upload' . uniqid();
                file_put_contents($tmpPath, $decodedPhoto);
                $uploadedFile = new FileObject($tmpPath);
//                $originalFilename = $uploadedFile->getFilename();

                $violations = $validator->validate(
                    $uploadedFile,
                    array(
                        new File(array(
                            //'maxSize' => '5M',
                            'mimeTypes' => ['image/png', 'image/jpg','image/jpeg']
                        ))
                    )
                );

                $fileName = md5(uniqid()) . '.' . $uploadedFile->guessExtension();

                try {
                    $uploadPath = $this->getParameter('uploads_directory') . self::TICKET_UPLOADS_FOLDER;
                    $uploadedFile->move($uploadPath, $fileName);
                } catch (FileException $e) {
                    throw new \Exception("Could not upload photo.");
                }

                $ticketPhoto = new TicketFilePhoto();
                $ticketPhoto->setPhotoPath($myPhotoPath.$fileName);
                $ticketPhoto->setTicket($ticket);
                //$ticketPhoto->setFilename($fileName);
                //$ticketPhoto->setOriginalFilename(($originalFilename!=null)?$originalFilename:$fileName);
                //$ticketPhoto->setMimeType($uploadedFile->getMimeType());

                $this->get("services")->blameOnMe($ticketPhoto, "create");
                $this->get("services")->blameOnMe($ticketPhoto, "update");
                $this->em->persist($ticketPhoto);
            }

            $this->em->persist($ticket);
            $this->em->persist($statusLog);

            $this->em->flush();

            ///ADD POINTS
            $message = $this->translator->trans('label_new'). " Ticket {$ticket->getId()}";
            $playKey = "BC-T-00002";//ticket creation
            $this->get("services")->addPoints($tenantContract, $message, $playKey);


            $response = array('message' => $ticket->getId());
            if ($this->container->getParameter('kernel.environment') == 'dev') {
                $response['debug'] = $ticket->getId();
            }
            return new JsonResponse($response);

        } catch (Exception $ex) {
            return new JsonResponse(array('message' => $ex->getMessage()), JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Updates a ticket.
     *
     * UPDATES a ticket and adds a rating to it.
     *
     * @Rest\Put("/v1/ticket", name="updateTicket")
     *
     * @SWG\Parameter( name="Content-Type", in="header", required=true, type="string", default="application/json" )
     * @SWG\Parameter( name="Authorization", in="header", required=true, type="string", default="Bearer TOKEN", description="Authorization" )
     *
     * @SWG\Parameter( name="ticket_id", in="body", required=true, type="integer", description="The ticket ID.", schema={} )
     * @SWG\Parameter( name="rating", in="body", type="integer", description="Rating.", schema={} )
     * @SWG\Parameter( name="status_id", in="body", required=true, type="integer", description="status ID", schema={} )
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

            $lang = strtolower(trim($request->get('language')));
            $this->translator->setLocale($lang);


            $ticketId = trim($request->get('ticket_id'));
            $rating = trim($request->get('rating'));

            $statusId = trim($request->get('status_id'));

            // Required parameter
            /** @var Ticket $ticket */
            $ticket = $this->em->getRepository('BackendAdminBundle:Ticket')->findOneBy(array('enabled' => true, 'id' => $ticketId));
            if ($ticket == null) {
                throw new \Exception("Invalid ticket ID.");
            }

            //$status = $this->em->getRepository('BackendAdminBundle:TicketStatus')->findOneById(self::TICKET_STATUS_CLOSE_ID);
            $status = $this->em->getRepository('BackendAdminBundle:TicketStatus')->findOneById($statusId);
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

            ///ADD POINTS
            $tenantContract = $ticket->getTenantContract();
            $message = $this->translator->trans('label_close'). " Ticket {$ticket->getId()}";
            $playKey = "BC-T-00003";//closing ticket
            $this->get("services")->addPoints($tenantContract, $message, $playKey);


            return new JsonResponse(array(
                'message' => "",
            ));
        } catch (Exception $ex) {
            return new JsonResponse(array('message' => $ex->getMessage()), JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Creates a like on a ticket.
     *
     * It receives all the necessary information to create a ticket.
     *
     * @Rest\Post("/v1/ticketLike", name="ticketLike")
     *
     * @SWG\Parameter( name="Content-Type", in="header", required=true, type="string", default="application/json" )
     * @SWG\Parameter( name="Authorization", in="header", required=true, type="string", default="Bearer TOKEN", description="Authorization" )
     *
     * @SWG\Parameter( name="ticket_id", in="body", required=true, type="integer", description="The id of the ticket.", schema={} )
     * @SWG\Parameter( name="like", in="body", required=true, type="integer", description="1 is like 0 is unlike", schema={} )
     *
     * @SWG\Parameter( name="app_version", in="query", required=true, type="string", description="The version of the app." )
     * @SWG\Parameter( name="code_version", in="query", required=true, type="string", description="The version of the code." )
     * @SWG\Parameter( name="language", in="query", required=true, type="string", description="The language being used (either en or es)." )
     * @SWG\Parameter( name="time_offset", in="query", type="string", description="Time difference with respect to GMT time." )
     *
     * @SWG\Response(
     *     response=200,
     *     description="Creates a successfull Ticket like.",
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

    public function postTicketLikeAction(Request $request)
    {

        try {
            $this->initialise();

            if (!$request->headers->has('Content-Type')) {
                throw new \Exception("Missing Content-Type header.");
            }

            $ticketID = trim($request->get('ticket_id'));
            $like = intval($request->get('like'));

            // Required parameter
            $ticket = $this->em->getRepository('BackendAdminBundle:Ticket')->findOneBy(array('enabled' => true, 'id' => $ticketID));
            if ($ticket == null) {
                throw new \Exception("Invalid ticket ID.");
            }

            if($like){
                $ticketFollower = new TicketFollower();
                $ticketFollower->setUser($this->getUser());
                $ticketFollower->setTicket($ticket);
                $ticketFollower->setEnabled(true);
            }
            else{//unlike
                $ticketFollower = $this->em->getRepository('BackendAdminBundle:TicketFollower')->findOneBy(array('enabled' => true, 'ticket' => $ticket->getId(), 'user' => $this->getUser()->getId()));
                $ticketFollower->setEnabled(false);
            }


            $this->em->persist($ticketFollower);
            $this->em->flush();

            $response = array('message' => $ticketFollower->getId());
            if ($this->container->getParameter('kernel.environment') == 'dev') {
                $response['debug'] = $ticketFollower->getId();
            }
            return new JsonResponse($response);

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

            $commentUser = $ticketComment->getCreatedBy();
            $likeUser = $ticketComment->getLikedBy();

            if ($likeUser == null) {
                $likeUser = new User();
            }


            return new JsonResponse(array(
                'comment' => array(
                    'username' => $commentUser->getUsername(),
                    'user_fullname' => $commentUser->getName(),
                    'timestamp' => $ticketComment->getCreatedAt()->getTimestamp(),
                    'like' => $likeUser->getUsername(),
                    'comment' => $ticketComment->getCommentDescription(),
                    'avatar_url' => $commentUser->getAvatarPath(),
                )
            ));
        } catch (Exception $ex) {
            return new JsonResponse(array('message' => $ex->getMessage()), JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }





    /**
     * Creates a reservation comment.
     *
     * Creates a comment for a reservation.
     *
     * @Rest\Post("/v1/reservationComment", name="commentReservation")
     *
     * @SWG\Parameter( name="Content-Type", in="header", required=true, type="string", default="application/json" )
     * @SWG\Parameter( name="Authorization", in="header", required=true, type="string", default="Bearer TOKEN", description="Authorization" )
     *
     * @SWG\Parameter( name="reservation_id", in="body", required=true, type="integer", description="The ticket ID.", schema={} )
     * @SWG\Parameter( name="comment", in="body", required=true, type="string", description="Comment.", schema={} )
     *
     * @SWG\Parameter( name="app_version", in="query", required=true, type="string", description="The version of the app." )
     * @SWG\Parameter( name="code_version", in="query", required=true, type="string", description="The version of the code." )
     * @SWG\Parameter( name="language", in="query", required=true, type="string", description="The language being used (either en or es)." )
     * @SWG\Parameter( name="time_offset", in="query", type="string", description="Time difference with respect to GMT time." )
     *
     * @SWG\Response(
     *     response=200,
     *     description="Posts a comment into an existin booking.",
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
     * @SWG\Tag(name="Common Area")
     */

    public function postReservationCommentAction(Request $request)
    {
        try {
            $this->initialise();

            if (!$request->headers->has('Content-Type')) {
                throw new \Exception("Missing Content-Type header.");
            }

            $bookingId = trim($request->get('reservation_id'));
            $comment = trim($request->get('comment'));

            // Required parameter
            /** @var Reservation $booking */
            $booking = $this->em->getRepository('BackendAdminBundle:CommonAreaReservation')->findOneBy(array('enabled' => true, 'id' => $bookingId));
            if ($booking == null) {
                throw new \Exception("Invalid booking ID.");
            }

            $bookingComment = new BookingComment();
            $bookingComment->setCommonAreaReservation($booking);
            $bookingComment->setCommentDescription($comment);
            $bookingComment->setEnabled(true);
            $this->get("services")->blameOnMe($bookingComment, "create");
            $this->get("services")->blameOnMe($bookingComment, "update");

            $this->em->persist($bookingComment);

            $this->em->flush();

            $commentUser = $bookingComment->getCreatedBy();
            $likeUser = $bookingComment->getLikedBy();

            if ($likeUser == null) {
                $likeUser = new User();
            }


            return new JsonResponse(array(
                'comment' => array(
                    'username' => $commentUser->getUsername(),
                    'user_fullname' => $commentUser->getName(),
                    'timestamp' => $bookingComment->getCreatedAt()->getTimestamp(),
                    'like' => $likeUser->getUsername(),
                    'comment' => $bookingComment->getCommentDescription(),
                    'avatar_url' => $commentUser->getAvatarPath(),
                )
            ));
        } catch (Exception $ex) {
            return new JsonResponse(array('message' => $ex->getMessage()), JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }



    /**
     * Gets a reservation and all its information.
     *
     * Returns the reservation information including comments
     *
     * @Rest\Get("/v1/reservation/{reservation_id}", name="singleReservation")
     *
     * @SWG\Parameter( name="Content-Type", in="header", type="string", default="application/json" )
     * @SWG\Parameter( name="Authorization", in="header", required=true, type="string", default="Bearer TOKEN", description="Authorization" )
     *
     * @SWG\Parameter( name="reservation_id", in="path", required=true, type="string", description="The ID of the reservation." )
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
     *
     *
     *                  @SWG\Property( property="name", type="string", description="Name of the common area", example="Common area" ),
     *                  @SWG\Property( property="description", type="string", description="Description of the common area", example="Description" ),
     *                  @SWG\Property( property="regulation", type="string", description="Regulation of the common area", example="Regulation" ),
     *                  @SWG\Property( property="term_condition", type="string", description="Terms and conditions of the common area", example="Terms and conditions" ),
     *                  @SWG\Property( property="price", type="float", description="Price of the common area", example="100" ),
     *                  @SWG\Property( property="required_payment", type="boolean", description="The required payment for the reservation of the common area", example="true" ),
     *                  @SWG\Property( property="has_equipment", type="boolean", description="If the common area is equiped", example="true" ),
     *                  @SWG\Property( property="equipment_description", type="string", description="Description of the equipement of the common area", example="" ),
     *                  @SWG\Property( property="photos", type="array",
     *                      @SWG\Items(
     *                          @SWG\Property( property="url", type="string", description="URL of property photo", example="/photo.jpg" ),
     *                      )
     *                  ),
     *                  @SWG\Property( property="comments_quantity", type="string", description="Ammount of comments for the ticket", example="3" ),
     *                  @SWG\Property( property="comments", type="array",
     *                      @SWG\Items(
     *                          @SWG\Property( property="username", type="string", description="Comments's creator username", example="2" ),
     *                          @SWG\Property( property="timestamp", type="string", description="Comment's creation time", example="1272509157" ),
     *                          @SWG\Property( property="like", type="string", description="User that liked the comment", example="user2" ),
     *                          @SWG\Property( property="comment", type="string", description="Comment content", example="Comment" ),
     *                          @SWG\Property( property="avatar_url", type="string", description="URL for the avatar", example="/avatars/1.jpg" ),
     *                      )
     *                  ),
     *                  @SWG\Property( property="reservation", type="array",
     *                      @SWG\Items(
     *                          @SWG\Property( property="status", type="string", description="status", example="pending, approved, rejected" ),
     *                          @SWG\Property( property="date_from", type="string", description="reservation from time", example="1272509157" ),
     *                          @SWG\Property( property="date_to", type="string", description="reservation to time", example="1272509157" ),
     *                          @SWG\Property( property="updated by", type="string", description="user name", example="Roberto H" ),
     *                          @SWG\Property( property="updated_at", type="string", description="updated time", example="1272509157" ),
     *                      )
     *                  ),
     *                  @SWG\Property( property="category", type="array",
     *                      @SWG\Items(
     *                          @SWG\Property( property="category_id", type="string", description="Category ID", example="1" ),
     *                          @SWG\Property( property="category_name", type="string", description="Category area name", example="Fixes" ),
     *                          @SWG\Property( property="icon_class", type="string", description="icon class", example="fas fa-pencil" ),
     *                          @SWG\Property( property="color", type="string", description="hexa color code", example="#ffffff" ),
     *                      )
     *
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
     * @SWG\Tag(name="Common Area")
     */
    public function getReservationAction(Request $request, $reservation_id)
    {
        try {
            $this->initialise();

            $lang = strtolower(trim($request->get('language')));

            $this->translator->setLocale($lang);

            $reservation = $this->em->getRepository('BackendAdminBundle:CommonAreaReservation')->find($reservation_id);
            if ($reservation == null) {
                throw new \Exception("Invalid reservation ID.");
            }

            $ticket = $this->em->getRepository('BackendAdminBundle:Ticket')->findOneByCommonAreaReservation($reservation_id);
            if ($ticket == null) {
                throw new \Exception("Invalid ticket.");
            }
            $commonArea = $reservation->getCommonArea();

            $myPhotoPath = self::IMAGES_PATH."common_area/";

            $commonAreaPhotos = array();

            $myPhotos = $this->em->getRepository('BackendAdminBundle:CommonAreaPhoto')->findBy(array('commonArea' => $commonArea->getId(), "enabled" => 1));

            if($myPhotos){
                foreach ($myPhotos as $photo){
                    $commonAreaPhotos[] = array('url' => $myPhotoPath.$photo->getPhotoPath());
                }
            }

            $data = array(
                'name' => $commonArea->getName(),
                'description' => $commonArea->getDescription(),
                'regulation' => $commonArea->getRegulation(),
                'term_condition' => $commonArea->getTermCondition(),
                'price' => $commonArea->getPrice(),
                'required_payment' => $commonArea->getRequiredPayment(),
                'has_equipment' => $commonArea->getHasEquipment(),
                'equipment_description' => $commonArea->getEquipmentDescription(),
                'photos' => $commonAreaPhotos,
            );


            ///reservation
            $status = $lang == "en" ? $reservation->getCommonAreaReservationStatus()->getNameEN() : $reservation->getCommonAreaReservationStatus()->getNameES();
            $data['reservation'] = array(

                'status' => $status,
                'date_from' => $reservation->getReservationDateFrom()->getTimestamp(),
                'date_to' => $reservation->getReservationDateTo()->getTimestamp(),
                'updated_by' => $reservation->getUpdatedBy()->getName(),


            );


            $iconClass = ($ticket->getTicketCategory()->getIcon() != null) ? $ticket->getTicketCategory()->getIcon()->getIconClass() : "";
            ///category
            $data['category'] = array("category_id" => $ticket->getTicketCategory()->getId(),
                        "category_name" => $ticket->getTicketCategory()->getName(),
                        "icon_class" => $iconClass,
                        "color" => $ticket->getTicketCategory()->getColor());

            // Fetching the booking comments
            $comments = $this->em->getRepository('BackendAdminBundle:BookingComment')->findBy(array("commonAreaReservation" => $reservation_id, "enabled" => 1), array("createdAt" => "DESC") );

            $data['comments'] = array();
            /** @var BookingComment $comment */
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
                    'user_fullname' => $commentUser->getName(),
                    'timestamp' => $comment->getCreatedAt()->getTimestamp(),
                    'like' => $likeUser->getUsername(),
                    'comment' => $comment->getCommentDescription(),
                    'avatar_url' => $commentUser->getAvatarPath(),
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
     *                          @SWG\Property( property="question_id", type="string", description="Poll question ID", example="1" ),
     *                          @SWG\Property( property="question", type="string", description="Poll question", example="Question" ),
     *                          @SWG\Property( property="file_photo", type="string", description="Poll file photo", example="/photo.jpg" ),
     *                          @SWG\Property( property="type", type="string", description="Poll question type", example="Type" ),
     *                          @SWG\Property( property="options", type="array",
     *                              @SWG\Items(
     *                                  @SWG\Property( property="option_id", type="string", description="Question's option ID", example="600" ),
     *                                  @SWG\Property( property="option", type="string", description="Question's option", example="Option" ),
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

            $poll = $this->em->getRepository('BackendAdminBundle:Poll')->find($poll_id);
            $questions = $this->em->getRepository('BackendAdminBundle:PollQuestion')->getApiPoll($poll_id);


            //$qids = $this->getArrayOfIds($questions);
            //var_dump($qids);die;


            $data = array(
                'id' => $poll->getId(),
                'name' => $poll->getName(),
            );


            $data['questions'] = array();
            /** @var PollQuestion $question */
            foreach ($questions as $question) {

                $rawAnswers = $this->em->getRepository('BackendAdminBundle:PollQuestionOption')->getApiPoll($question->getId());

                //$answers = array();
                /** @var PollQuestionOption $answer */
                /*
                foreach ($rawAnswers as $answer) {
                    //var_dump($answer);die;
                    $answers[$answer->getPollQuestion()->getId()] = $answer;
                }
                */

                $options = array();
                foreach ($rawAnswers as $answer) {
                    $options[] = array('option_id' => $answer->getId(),'option' => $answer->getQuestionOption());
                }

                $data['questions'][] = array(
                    'question_id' => $question->getId(),
                    'question' => $question->getQuestion(),
                    'file_photo' => $question->getPollFilePhoto(),
                    'type' => $question->getPollQuestionType()->getNameEN(),
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
     * @SWG\Parameter( name="tenant_contract_id", in="body", required=true, type="integer", description="tenant contract ID", schema={} )
     * @SWG\Parameter( name="end_poll", in="body", required=true, type="integer", description="1 end of the poll - 0", schema={} )
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
            $endPoll = intval($request->get('end_poll'));
            $tenantContractID = intval($request->get('tenant_contract_id'));

            // Required parameter
            /** @var PollQuestion $pollQuestion */
            $pollQuestion = $this->em->getRepository('BackendAdminBundle:PollQuestion')->getApiAnswer($pollQuestionId);
            if ($pollQuestion == null) {
                throw new \Exception("Invalid Poll Question ID.");
            }

            $questionTypeId = $pollQuestion->getPollQuestionType()->getId();
            switch ($questionTypeId) {
                case self::QUESTION_TYPE_OPEN_ID:
                    if (empty($answerText)) {
                        throw new \Exception("Invalid Answer Text.");
                    }

                    $answer = new PollTenantAnswer();
                    $answer->setAnswerText($answerText);
                    $answer->setEnabled(true);
                    $answer->setPollQuestion($pollQuestion);
                    $this->get("services")->blameOnMe($answer, "create");
                    $this->get("services")->blameOnMe($answer, "update");

                    $this->em->persist($answer);
                    break;

                case self::QUESTION_TYPE_RATING_ID:
                    if (empty($answerRating)) {
                        throw new \Exception("Invalid Answer Rating.");
                    }

                    $answer = new PollTenantAnswer();
                    $answer->setAnswerRating($answerRating);
                    $answer->setEnabled(true);
                    $answer->setPollQuestion($pollQuestion);
                    $this->get("services")->blameOnMe($answer, "create");
                    $this->get("services")->blameOnMe($answer, "update");

                    $this->em->persist($answer);
                    break;

                case self::QUESTION_TYPE_MULTIPLE_ID:
                //case self::QUESTION_TYPE_TRUEFALSE_ID:
                case self::QUESTION_TYPE_ONEOPTION_ID:
                    if (count($pollQuestionOptionIds) == 0) {
                        throw new \Exception("poll_question_option_ids is empty.");
                    }

                    if ( $questionTypeId == self::QUESTION_TYPE_ONEOPTION_ID) {
                        if (count($pollQuestionOptionIds) > 1) {
                            throw new \Exception("poll_question_option_ids can only contain one element.");
                        }
                    }

                    // Required parameter
                    $pollQuestionOptions = $this->em->getRepository('BackendAdminBundle:PollQuestionOption')->getApiAnswer($pollQuestionOptionIds);
                    if ($pollQuestionOptions == null) {
                        throw new \Exception("Invalid Poll Question Option ID.");
                    }

                    if (count($pollQuestionOptionIds) != count($pollQuestionOptions)) {
                        throw new \Exception("Invalid Poll Question Option ID.");
                    }

                    foreach ($pollQuestionOptions as $option) {
                        $answer = new PollTenantAnswer();
                        $answer->setPollQuestionOption($option);
                        $answer->setEnabled(true);
                        $answer->setPollQuestion($pollQuestion);
                        $this->get("services")->blameOnMe($answer, "create");
                        $this->get("services")->blameOnMe($answer, "update");

                        $this->em->persist($answer);
                    }
                    break;
            }

            $this->em->flush();


            if($endPoll){

                $tenantContract = $this->em->getRepository('BackendAdminBundle:TenantContract')->find($tenantContractID);
                ///ADD POINTS TO PLAYER

                ///ADD POINTS
                $message = $this->translator->trans('label_answer')." ".$pollQuestion->getPoll()->getName();
                $playKey = "BC-T-00004";//feedback poll
                $this->get("services")->addPoints($tenantContract, $message, $playKey);

            }

            return new JsonResponse(array(
                'message' => "tenantAnswer",
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

    public function putAvatarAction(Request $request)
    {
        try {
            $this->initialise();

            if (!$request->headers->has('Content-Type')) {
                throw new \Exception("Missing Content-Type header.");
            }

            $myPhotoPath = self::IMAGES_PATH.self::AVATAR_UPLOADS_FOLDER;
            $photo = $request->get('photo');
            $photo = $photo[0];

            $pos  = strpos($photo, ';');
            $type = explode('/', substr($photo, 0, $pos))[1];

            /** @var User $user */
            $user = $this->getUser();

            /** @var ValidatorInterface $validator */
            $validator = $this->get('validator');

                $photo = str_replace('data:image/png;base64,', '', $photo);
                $photo = str_replace('data:image/jpg;base64,', '', $photo);
                $photo = str_replace('data:image/jpeg;base64,', '', $photo);
                $photo = str_replace(' ', '+', $photo);
                $decodedPhoto = base64_decode($photo);

                $tmpPath = sys_get_temp_dir() . '/sf_upload' . uniqid();
                file_put_contents($tmpPath, $decodedPhoto);
                $uploadedFile = new FileObject($tmpPath);
                $originalFilename = $uploadedFile->getFilename();
                //var_dump($originalFilename);die;

                $violations = $validator->validate(
                    $uploadedFile,
                    array(
                        new File(array(
                            //'maxSize' => '5M',
                            'mimeTypes' => ['image/png', 'image/jpg','image/jpeg']
                        ))
                    )
                );

                $fileName = md5(uniqid()) . '.' . $uploadedFile->guessExtension();
                //var_dump($fileName);die;

                try {
                    $uploadPath = $this->getParameter('uploads_directory') . self::AVATAR_UPLOADS_FOLDER;
                    $uploadedFile->move($uploadPath, $fileName);
                } catch (FileException $e) {
                    throw new \Exception("Could not upload photo.");
                }


            $user->setAvatarPath($myPhotoPath.$fileName);
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
            $this->translator->setLocale($lang);

            $message = "Invitation";
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

            $conflicTenantContracts = $tenantRepo->findOneBy(array('enabled' => true, 'propertyContract' => $propertyContract, 'invitationUserEmail' => $email));
            if (count($conflicTenantContracts) > 0) {
                //throw new \Exception("There is at least one existing TenantContract with this email and property contract.");

                $tenantContract = $conflicTenantContracts;
                $tenantContract->setEnabled(true);
                $this->get("services")->blameOnMe($tenantContract, "update");
                $this->em->persist($tenantContract);
                $this->em->flush();
            }
            else{

                $tenantContract = new TenantContract();
                $tenantContract->setPropertyContract($propertyContract);
                //$tenantContract->setIsOwner(false);
                $tenantContract->setEnabled(false);
                $tenantContract->setInvitationUserEmail($email);
                $tenantContract->setInvitationAccepted(false);
                $code = $this->get("services")->getToken(6);
                $tenantContract->setPropertyCode($code);

                $this->get("services")->blameOnMe($tenantContract, "create");
                $this->get("services")->blameOnMe($tenantContract, "update");

                /** @var User $user */
                $user = $userRepo->findOneBy(array('enabled' => true, 'email' => $email));
                if ($user != null) {

                    $description = $this->translator->trans("label_invite_notification");
                    $tenantContract->setUser($user);
                    $notification = $this->createInviteUserNotification($tenantContract, $this->getUser(), $user, $description);
                    $this->em->persist($notification);
                }

                $this->em->persist($tenantContract);
                $this->em->flush();



            }

            $objProperty = $tenantContract->getPropertyContract()->getProperty();
            $propertyName = $objProperty->getPropertyType() . " ". $objProperty->getPropertyNumber();

            /*
            $now = new \DateTime();
            $subject = $this->translator->trans('mail.invite_subject');
            $bodyHtml = $this->getUser()->getName(). " ". sprintf("<p>%s</p><br/>", $this->translator->trans('mail.invite_property_body'));
            //$bodyHtml .= sprintf("<p>%s</p><br/>", $message);
            $bodyHtml .= sprintf("<b>%s:</b> %s<br/>", $this->translator->trans('label_property'),  $propertyName);
            $bodyHtml .= sprintf("<b>%s:</b> %s<br/>", $this->translator->trans('label_code'),  $tenantContract->getPropertyCode());
            $bodyHtml .= sprintf("<b>%s</b> %s<br/>", $this->translator->trans('mail.label_time'), $now->format('Y-m-d H:i'));
            $bodyHtml .= "<br/>";

            $messageEmail = $this->get('services')->generalTemplateMail($subject, $email, $bodyHtml);
            */


            //new message from sendgrid
            if($lang == "en"){
                $templateID = "d-a2eda7d6832448c484be6ae550126187";
            }
            else{
                $templateID = "d-010af6bef81a446b9c7be592b4b579db";
            }

            //tenant_name
            //property_address
            //complex_name
            $myJson = '"property_number": "'.$propertyName.'",';
            $myJson .= '"complex_address": "'.$objProperty->getComplex()->getAddress.'",';
            $myJson .= '"complex_name": "'.$objProperty->getComplex()->getName().'",';
            $myJson .= '"complex_city": "'.$objProperty->getComplex()->getGeoState().'",';
            $myJson .= '"complex_state": "'.$objProperty->getComplex()->getGeoState()->getGeoCountry().'",';
            $myJson .= '"property_key": "'.$tenantContract->getPropertyCode().'"';

            $sendgridResponse = $this->get('services')->callSendgrid($myJson, $templateID, $email);

            ///ADD POINTS TO PLAYER
            $message = $this->translator->trans('label_invitation_join').". ".$email;
            $playKey = "BC-T-00005";//invite tenant
            $this->get("services")->addPoints($propertyContract->getMainTenantContract(), $message, $playKey);


            return new JsonResponse(array(
                'message' => "sendInvitation",
            ));
        } catch (Exception $ex) {
            return new JsonResponse(array('message' => $ex->getMessage()), JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    private function createInviteUserNotification($tenantContract, $from, $to, $description = "")
    {
        /** @var NotificationTypeRepository $notificationRepo */
        $notificationRepo = $this->em->getRepository('BackendAdminBundle:NotificationType');
        $notificationType = $notificationRepo->findOneById(self::INVITATION_NOTIFICATION_TYPE_ID);

        $notification = new UserNotification();
        $notification->setEnabled(true);

        $notification->setDescription($description);
        $notification->setNotice("");
        $notification->setTenantContract($tenantContract);
        $notification->setTenantContract($tenantContract);
        $notification->setNotificationType($notificationType);

        $notification->setCreatedBy($from);
        $notification->setUpdatedBy($from);
        $notification->setSentTo($to);

        $now = new \DateTime();
        $notification->setCreatedAt($now);
        $notification->setUpdatedAt($now);

        return $notification;
    }


    /**
     * Updates an invitation.
     *
     * Updates an invitation by setting the proper values in the tenant contract and the notification.
     *
     * @Rest\Put("/v1/invitation", name="updateInvitation")
     *
     * @SWG\Parameter( name="Content-Type", in="header", required=true, type="string", default="application/json" )
     * @SWG\Parameter( name="Authorization", in="header", required=true, type="string", default="Bearer TOKEN", description="Authorization" )
     *
     * @SWG\Parameter( name="tenant_contract_id", in="body", required=true, type="integer", description="The id of the tenant contract.", schema={} )
     * @SWG\Parameter( name="action", in="body", required=true, type="inteter", description="1 is accept and 0 is delete", schema={} )
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

    public function putUpdateInvitationAction(Request $request)
    {
        try {
            $this->initialise();

            if (!$request->headers->has('Content-Type')) {
                throw new \Exception("Missing Content-Type header.");
            }

            $tenantContractId = intval($request->get('tenant_contract_id'));
            $action = intval($request->get('action'));

            /** @var TenantContractRepository $tenantRepo */
            $tenantRepo = $this->em->getRepository('BackendAdminBundle:TenantContract');
            /** @var UserNotificationRepository $notificationRepo */
            $notificationRepo = $this->em->getRepository('BackendAdminBundle:UserNotification');

            /** @var TenantContract $tenantContract */
            $tenantContract = $tenantRepo->findOneById($tenantContractId);
            if ($tenantContract == null) {
                throw new \Exception("Invalid tenant contract.");
            }

            /*
            $userNotifications = $notificationRepo->findBy(array('enabled' => true, 'user' => $this->getUser(), 'tenantContract' => $tenantContract));


            foreach ($userNotifications as $userNotification) {
                $userNotification->setIsRead(true);
                $this->get("services")->blameOnMe($userNotification, "update");

                $this->em->persist($userNotification);
            }
            */

            if($action){//ACCEPT
                $tenantContract->setUser($this->getUser());
                $tenantContract->setInvitationAccepted(true);
            }
            else{//DELETE
                $tenantContract->setEnabled(false);
            }

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
     * @Rest\Get("/v1/faq/{complex_id}", name="faq")
     *
     * @SWG\Parameter( name="Content-Type", in="header", type="string", default="application/json" )
     * @SWG\Parameter( name="Authorization", in="header", required=true, type="string", default="Bearer TOKEN", description="Authorization" )
     *
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
     *                  @SWG\Property( property="faqs", type="string", description="FAQs for the complex", example="Lorem ipsum..." ),
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
    public function getFaqAction(Request $request)
    {
        try {
            $this->initialise();
            $lang = strtolower(trim($request->get('language')));
            $complex_id = trim($request->get('complex_id'));

            /** @var ComplexFaqRepository $complexFaqRepo */
            $complexFaqRepo = $this->em->getRepository('BackendAdminBundle:ComplexFaq');
            //$complexFaq = $complexFaqRepo->getApiFaqs($complex_id);
            $complexFaq = $complexFaqRepo->findOneByComplex($complex_id);
            /** @var ComplexRepository $complexRepo */
            $complexRepo = $this->em->getRepository('BackendAdminBundle:Complex');
            $complex = $complexRepo->findOneById($complex_id);

            //$faqs = $complexFaqRepo->getApiFaqs($complex_id, $page_id);
            //$total = $complexFaqRepo->countApiFaqs($complex_id);


            $data = array(
                'business_name' => $complex->getBusiness()->getName(),
                'business_address' => $complex->getBusiness()->getAddress(),
                'business_phone' => $complex->getBusiness()->getPhoneNumber(),
            );

            $data['faqs'] = $lang == 'en' ? htmlspecialchars_decode($complexFaq->getDescriptionEN()) : htmlspecialchars_decode($complexFaq->getDescriptionES());

            /** @var ComplexFaq $faq */
            /*
            foreach ($faqs as $faq) {
                $data['faqs'][] = array(
                    'question' => $faq->getQuestion(),
                    'answer' => $faq->getAnswer(),
                );
            }
            */

            return new JsonResponse(array(
                'message' => "faq",
                //'metadata' => $this->calculatePagesMetadata($page_id, $total),
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


            $objProperty = $propertyRepo->find($propertyId);

            $admin = $userRepo->findOneBy(array("role" => 1, "business" => $objProperty->getComplex()->getBusiness(), "enabled" => 1));


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
     * Gets the common areas of a complex.
     *
     * Returns a list of the common areas in the complex of the property.
     *
     * @Rest\Get("/v1/commonAreas/{complex_id}/{page_id}", name="listCommonAreas")
     *
     * @SWG\Parameter( name="Content-Type", in="header", type="string", default="application/json" )
     * @SWG\Parameter( name="Authorization", in="header", required=true, type="string", default="Bearer TOKEN", description="Authorization" )
     *
     * @SWG\Parameter( name="complex_id", in="path", type="string", description="The ID of the complex." )
     * @SWG\Parameter( name="page_id", in="path", type="string", description="The requested pagination page." )
     *
     * @SWG\Parameter( name="app_version", in="query", required=true, type="string", description="The version of the app." )
     * @SWG\Parameter( name="code_version", in="query", required=true, type="string", description="The version of the code." )
     * @SWG\Parameter( name="language", in="query", required=true, type="string", description="The language being used (either en or es)." )
     * @SWG\Parameter( name="time_offset", in="query", type="string", description="Time difference with respect to GMT time." )
     *
     * @SWG\Response(
     *     response=200,
     *     description="Returns the list of common areas of a complex.",
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



    public function getCommonAreasAction($complex_id, $page_id = 1)
    {
        try {
            $this->initialise();
            $data = array();

            $myPhotoPath = self::IMAGES_PATH."common_area/";

            $commonAreaRepo = $this->em->getRepository('BackendAdminBundle:CommonArea');
            /** @var CommonAreaPhotoRepository $commonAreaPhotosRepo */
            $commonAreaPhotoRepo = $this->em->getRepository('BackendAdminBundle:CommonAreaPhoto');


            $commonAreas = $commonAreaRepo->getApiCommonAreas($complex_id, $page_id);
            $total = $this->em->getRepository('BackendAdminBundle:CommonArea')->countApiCommonAreas($complex_id);

            $cids = $this->getArrayOfIds($commonAreas);

            //$rawPhotos = $commonAreaPhotoRepo->getApiCommonAreas($cids);
            //$photos = array();
            /** @var CommonAreaPhoto $photo */
            /*
            foreach ($rawPhotos as $photo) {
                $photos[$photo->getCommonArea()->getId()] = $photo;
            }*/

            /** @var CommonArea $commonArea */
            foreach ($commonAreas as $commonArea) {
                $commonAreaPhotos = array();

                $myPhotos = $commonAreaPhotoRepo->findBy(array('commonArea' => $commonArea->getId(), "enabled" => 1));

                if($myPhotos){
                    foreach ($myPhotos as $photo){
                        $commonAreaPhotos[] = array('url' => $myPhotoPath.$photo->getPhotoPath());
                    }
                }
                /*
                if (array_key_exists($commonArea->getId(), $photos)) {

                    foreach ($photos[$commonArea->getId()] as $photo) {
                        $commonAreaPhotos[] = array('url' => $photo->getPhotoPath());
                    }
                }
                */

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
     * @Rest\Get("/v1/commonAreaAvailability/{common_area_id}/{date}", name="commonAreaAvailability")
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
     * @SWG\Parameter( name="date", in="query", type="string", description="Y-m-d" )
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
    public function getCommonAreaAvailabilityAction(Request $request, $common_area_id, $date)
    {
        try {
            $this->initialise();
            $lang = strtolower(trim($request->get('language')));


            $data = array('reservations' => array(), 'availabilities' => array());

            $availabilities = $this->em->getRepository('BackendAdminBundle:CommonAreaAvailability')->getCommonAreaAvailability($common_area_id, $date);
            $reservations = $this->em->getRepository('BackendAdminBundle:CommonAreaReservation')->getApiCommonAreaAvailability($common_area_id, $date);

            /** @var CommonAreaReservation $reservation */
            foreach ($reservations as $reservation) {
                $status = $reservation->getCommonAreaReservationStatus();
                if ($status == null) {
                    $status = new CommonAreaReservationStatus();
                }

                $data['reservations'][] = array(
                    'status' => ($lang == 'en') ? $status->getNameEN() : $status->getNameES(),
                    //'date_from' => $reservation->getReservationDateFrom()->getTimestamp(),
                    //'date_to' => $reservation->getReservationDateTo()->getTimestamp(),
                    'date_from' => $reservation->getReservationDateFrom()->format("H:i"),
                    'date_to' => $reservation->getReservationDateTo()->format("H:i"),
                );
            }

            $data['availabilities'] = $availabilities;

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

            $myPhotoPath = self::IMAGES_PATH."common_area/";

            $commonAreaPhotos = array();

            $myPhotos = $this->em->getRepository('BackendAdminBundle:CommonAreaPhoto')->findBy(array('commonArea' => $commonArea->getId(), "enabled" => 1));

            if($myPhotos){
                foreach ($myPhotos as $photo){
                    $commonAreaPhotos[] = array('url' => $myPhotoPath.$photo->getPhotoPath());
                }
            }





            $data = array(
                'name' => $commonArea->getName(),
                'description' => $commonArea->getDescription(),
                'regulation' => $commonArea->getRegulation(),
                'term_condition' => $commonArea->getTermCondition(),
                'price' => $commonArea->getPrice(),
                //'reservation_hour_period' => $commonArea->getReservationHourPeriod(),
                'required_payment' => $commonArea->getRequiredPayment(),
                'has_equipment' => $commonArea->getHasEquipment(),
                'equipment_description' => $commonArea->getEquipmentDescription(),
                'photos' => $commonAreaPhotos,
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
     * @SWG\Parameter( name="property_id", in="body", required=true, type="integer", description="The property ID booking the common area.", schema={} )
     * @SWG\Parameter( name="event_date", in="body", required=true, type="string", description="date in string format Y-m-d", schema={} )
     * @SWG\Parameter( name="hour_from", in="body", required=true, type="string", description="09:00", schema={} )
     * @SWG\Parameter( name="hour_to", in="body", required=true, type="string", description="10:00", schema={} )
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
            $propertyID = $request->get('property_id');
            $eventDate = trim($request->get('event_date'));
            $hourFrom = trim($request->get('hour_from'));
            $hourTo = trim($request->get('hour_to'));

            $commonArea = $this->em->getRepository('BackendAdminBundle:CommonArea')->findOneBy(array('enabled' => true, 'id' => $commonAreaId));
            if ($commonArea == null) {
                throw new \Exception("Invalid common area ID.");
            }

            $property = $this->em->getRepository('BackendAdminBundle:Property')->findOneBy(array('enabled' => true, 'id' => $propertyID));
            if ($property == null) {
                throw new \Exception("Invalid property ID.");
            }


            $status = $this->em->getRepository('BackendAdminBundle:CommonAreaReservationStatus')->findOneBy(array('enabled' => true, 'id' => $this::COMMON_AREA_RESERVATION_STATUS_ID));
            if ($status == null) {
                throw new \Exception("Invalid status ID.");
            }

            $dateFrom = $eventDate." ".trim($hourFrom);
            $dateTo = $eventDate." ".trim($hourTo);


            $startDate = new \DateTime($dateFrom);
            $endDate = new \DateTime($dateTo);

            $reservation = new CommonAreaReservation();
            $reservation->setProperty($property);
            $reservation->setCommonArea($commonArea);
            $reservation->setReservationDateFrom($startDate);
            $reservation->setReservationDateTo($endDate);
            $reservation->setReservedBy($this->getUser());
            $reservation->setEnabled(true);
            $reservation->setCommonAreaReservationStatus($status);

            $this->get("services")->blameOnMe($reservation, "create");
            $this->get("services")->blameOnMe($reservation, "update");

            $this->em->persist($reservation);

            //$this->em->flush();


            ///generar un pago si montos son diferentes a 0
            ///
            if($commonArea->getRequiredPayment()){
                $cost = floatval($commonArea->getPrice()) ;
            }
            else{
                $cost = 0;
            }


            if($cost > 0){
                $propertyContract = $this->em->getRepository('BackendAdminBundle:PropertyContract')->findOneBy(array("property" => $property->getId(), 'propertyTransactionType' => 3, "enabled" => 1, 'isActive' => 1), array("id"=> "DESC"));
                $transactionType = $this->em->getRepository('BackendAdminBundle:PropertyTransactionType')->find(4);//reservacion

                $payment = new PropertyContractTransaction();
                $payment->setEnabled(1);
                $payment->setComplex($property->getComplex());
                $payment->setPropertyContract($propertyContract);
                $payment->setPropertyTransactionType($transactionType);
                $payment->setCommonAreaReservation($reservation);
                $payment->setDescription($reservation->getCommonArea()->getName()." ". number_format($cost, 2, ".", "") );
                $payment->setPaymentAmount($cost);

                //$payment->setPaidAmount($amountPaid);
                //$payment->setDiscount($discount);

                /*
                ///paid & paid date
                $payment->setPaidBy($propertyContract->getProperty()->getMainTenant());
                $gtmNow = gmdate("Y-m-d H:i:s");
                $payment->setPaidDate(new \DateTime($gtmNow));
                */

                //status
                $payment->setStatus(0);

                //BLAME ME
                $this->get("services")->blameOnMe($payment, "create");
                $this->get("services")->blameOnMe($payment, "update");

                $this->em->persist($payment);

            }

            ///registro a log de reservaciones
            $bookingLog = new BookingLog();
            $bookingLog->setCommonAreaReservation($reservation);
            $bookingLog->setStatus("label_pending");

            //BLAME ME
            $this->get("services")->blameOnMe($bookingLog, "create");
            $this->get("services")->blameOnMe($bookingLog, "update");

            $bookingLog->setCreatedBy($reservation->getReservedBy());

            $this->em->persist($bookingLog);

            /////CREATE TICKET
            $objTicketType = $this->em->getRepository('BackendAdminBundle:TicketType')->find(3);//RESERVATION
            $status = $this->em->getRepository('BackendAdminBundle:TicketStatus')->findOneById(3);//SOLVED
            $objComplex = $property->getComplex();

            $ticket = new Ticket();
            $ticket->setCommonAreaReservation($reservation);
            $ticket->setTicketType($objTicketType);
            $title = $reservation->getCommonArea()->getName();
            $ticket->setTitle($title);
            $ticket->setDescription($this->translator->trans('label_reservation')." ". $reservation->getId());
            $ticket->setPossibleSolution("");
            $ticket->setIsPublic(false);

            $myLocale = $objComplex->getGeoState()->getGeoCountry()->getLocale();
            $name = $myLocale == "en" ? "Common Area" : "Área común";

            $ticketCategory = $this->em->getRepository('BackendAdminBundle:TicketCategory')->findOneBy(array("complex" => $objComplex->getId(), 'name' => $name, "enabled" => 1));
            if(!$ticketCategory){
                $ticketCategory = $this->em->getRepository('BackendAdminBundle:TicketCategory')->findOneBy(array("iconUnicode" => "f78c", 'name' => $name, "enabled" => 1));
            }

            $ticket->setTicketCategory($ticketCategory);
            $ticket->setComplexSector($property->getComplexSector());

            $ticket->setComplex($objComplex);
            $ticket->setProperty($property);
            //$ticket->setCommonAreaReservation($commonAreaReservation);

            $tenantContract =  null;
            $propertyContract = $this->em->getRepository('BackendAdminBundle:PropertyContract')->findOneBy(array("property" => $property->getId(), 'propertyTransactionType' => 3, "enabled" => 1, 'isActive' => 1), array("id"=> "DESC"));
            if($propertyContract) {
                $tenantContract = $this->em->getRepository('BackendAdminBundle:TenantContract')->findOneBy(array("propertyContract" => $propertyContract->getId(), "enabled" => 1, "user" => $this->getUser()->getId()), array("id"=> "DESC"));
            }

            $ticket->setTenantContract($tenantContract);
            $ticket->setTicketStatus($status);
            $ticket->setEnabled(true);


            //setAssignedTo
            //$timezone  = -5; //(GMT -5:00) EST (U.S. & Canada)
            $timezone = str_replace("GMT", '', $objComplex->getBusiness()->getGeoState()->getTimezoneOffset());
            $userToAssign = $this->em->getRepository('BackendAdminBundle:Shift')->getUsertoAssignTicket($timezone, $objComplex->getId());


            $ticket->setAssignedTo($userToAssign);

            $this->get("services")->blameOnMe($ticket, "create");
            $this->get("services")->blameOnMe($ticket, "update");

            $ticket->setCreatedBy($this->getUser());

            $this->em->persist($ticket);

            ////USER NOTIFICATION
            /// toDo



            $this->em->flush();



            return new JsonResponse(array(
                'message' => "",
            ));
        } catch (Exception $ex) {
            return new JsonResponse(array('message' => $ex->getMessage()), JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }


    // -----------------------------------------------------------------------------------------------------------------
    // -----------------------------------------------------------------------------------------------------------------
    // ------------------------------- Gamification
    // -----------------------------------------------------------------------------------------------------------------
    // -----------------------------------------------------------------------------------------------------------------

    /**
     * List the available plays.
     *
     * This calls the Bettercondos.info API to get a list of available plays. [Right now it does nothing].
     *
     * @Rest\Get("/v1/plays", name="listPlays", )
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
     *     description="Returns the list of plays.",
     *     @SWG\Schema (
     *          @SWG\Property(
     *              property="data", type="array",
     *              @SWG\Items(
     *                  @SWG\Property( property="id", type="integer", description="Play ID", example="1" ),
     *                  @SWG\Property( property="name", type="string", description="Name of the play", example="Play" ),
     *              ),
     *          ),
     *          @SWG\Property( property="message", type="string", example="" )
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
     * @SWG\Tag(name="Gamification")
     */
    public function getPlaysAction()
    {
        try {
            $this->initialise();
            $data = array();

            // ToDo: pending definition.

            return new JsonResponse(array(
                'message' => "listPayments",
                'data' => $data
            ));
        } catch (Exception $ex) {
            return new JsonResponse(array('message' => $ex->getMessage()), JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }



    /**
     * List the payments of the specified property by month and year.
     *
     * This calls the Bettercondos.info API to get a list of payments for the specified month and year. [Right now it does nothing].
     *
     * @Rest\Get("/v1/payments/{property_id}/{month}/{year}", name="listPayments", )
     *
     * @SWG\Parameter( name="Content-Type", in="header", type="string", default="application/json" )
     * @SWG\Parameter( name="Authorization", in="header", required=true, type="string", default="Bearer TOKEN", description="Authorization" )
     *
     * @SWG\Parameter( name="property_id", in="path", required=true, type="string", description="The property ID" )
     * @SWG\Parameter( name="month", in="path", required=true, type="string", description="example 09 - The month of the requested payments." )
     * @SWG\Parameter( name="year", in="path", required=true, type="string", description="example 2019 - The year of the requested payments." )
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
     *                  @SWG\Property( property="id", type="string", description="ID of the payment", example="46" ),
     *                  @SWG\Property( property="description", type="string", description="Description of the payment", example="Description" ),
     *                  @SWG\Property( property="created_at", type="string", description="Timestamp GMT formatted with Unix Time (https://en.wikipedia.org/wiki/Unix_time)", example="1272509157" ),
     *                  @SWG\Property( property="due_date", type="string", description="Timestamp GMT formatted with Unix Time (https://en.wikipedia.org/wiki/Unix_time)", example="1272509157" ),
     *                  @SWG\Property( property="status", type="string", description="Status of the payment", example="0 = pending, 1 = paid" ),
     *                  @SWG\Property( property="amount", type="string", description="Amount of the payment", example="4500.00" ),
     *              ),
     *          ),
     *          @SWG\Property( property="message", type="string", example="" )
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
    public function getPaymentsAction(Request $request)
    {
        try {
            $this->initialise();
            $data = array();

            $lang = strtolower(trim($request->get('language')));

            $propertyID = intval($request->get('property_id'));
            $month = trim($request->get('month'));
            $year = trim($request->get('year'));

            $payments = $this->em->getRepository('BackendAdminBundle:PropertyContractTransaction')->getApiPayments($propertyID, $month, $year);

            foreach ($payments as $payment){

                $data[] = array(
                    "description" => $payment->getDescription(),
                    "created_at" => $payment->getCreatedAt()->getTimestamp(),
                    "due_date" => $payment->getDueDate() != null ? $payment->getDueDate()->getTimestamp() : "",
                    "amount" => number_format(floatval($payment->getPaymentAmount()), 2, '.', ''),
                    "status" => $payment->getStatus(),
                    "type" =>  $lang == "en" ? $payment->getPropertyTransactionType()->getNameEN() : $payment->getPropertyTransactionType()->getNameES()
                );

            }


            return new JsonResponse(array(
                'message' => "listPayments",
                'data' => $data
            ));
        } catch (Exception $ex) {
            return new JsonResponse(array('message' => $ex->getMessage()), JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * List the points of the specified user.
     *
     * This calls the Bettercondos.info API to get a list of points for the user. [Right now it does nothing].
     *
     * @Rest\Get("/v1/points/{player_id}/{month}/{year}", name="getPoints", )
     *
     * @SWG\Parameter( name="Content-Type", in="header", type="string", default="application/json" )
     * @SWG\Parameter( name="Authorization", in="header", required=true, type="string", default="Bearer TOKEN", description="Authorization" )
     *
     * @SWG\Parameter( name="player_id", in="path", required=true, type="string", description="Player ID" )
     * @SWG\Parameter( name="month", in="path", required=true, type="string", description="01-12" )
     * @SWG\Parameter( name="year", in="path", required=true, type="string", description="4 digit format 2019" )
     *
     * @SWG\Parameter( name="app_version", in="query", required=true, type="string", description="The version of the app." )
     * @SWG\Parameter( name="code_version", in="query", required=true, type="string", description="The version of the code." )
     * @SWG\Parameter( name="language", in="query", required=true, type="string", description="The language being used (either en or es)." )
     * @SWG\Parameter( name="time_offset", in="query", type="string", description="Time difference with respect to GMT time." )
     *
     * @SWG\Response(
     *     response=200,
     *     description="Returns the list of points for the user.",
     *     @SWG\Schema (
     *          @SWG\Property(
     *              property="data", type="array",
     *              @SWG\Items(
     *                  @SWG\Property( property="ticket_id", type="integer", description="Ticket ID", example="1" ),
     *                  @SWG\Property( property="date", type="string", description="Timestamp GMT formatted with Unix Time (https://en.wikipedia.org/wiki/Unix_time)", example="1272509157" ),
     *                  @SWG\Property( property="play_id", type="integer", description="Play ID", example="1" ),
     *                  @SWG\Property( property="play_name", type="string", description="Name of the play", example="Play" ),
     *                  @SWG\Property( property="points", type="integer", description="Points of the player", example="4500" ),
     *              ),
     *          ),
     *          @SWG\Property( property="message", type="string", example="" )
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
     * @SWG\Tag(name="Gamification")
     */
    public function getPointsAction($player_id, $month, $year)
    {
        try {
            $this->initialise();
            $data = array();

            //$playerID = intval($request->get('player_id'));
            //$month = trim($request->get('month'));
            //$year = intval($request->get('year'));

            // ToDo: pending definition.
            $token = $this->get('services')->getBCToken();

            $username = $this->getUser()->getUsername();
            $arrResponse = $this->callGamificationService( "GET", "account-status/".$player_id."?month=".$month."&year=".$year, array() );

            //var_dump($arrResponse);die;

            //$nextLevelPoints = $arrResponse["next_level_points"];
            $nextLevelPoints = 700;
            //$availablePoints = intval($arrResponse["available_points"]);
            $availablePoints = 400;
            //$exchangedPoints = intval($arrResponse["exchanged_points"]);
            $exchangedPoints = 200;
            //$totalPoints = intval($arrResponse["earned_points"]);
            $totalPoints = 600;
            $percentage = ($totalPoints * 100 ) / $nextLevelPoints;
            //
            //$currentLevel = intval($arrResponse["current_level"]);
            $currentLevel = 1;

            $data = array(
                "name" => $this->getUser()->getName(),
                'avatar_url' => $this->getUser()->getAvatarPath(),
                "level" => $currentLevel,
                "earned_points" => $totalPoints,
                "available_points" => $availablePoints,
                "exchanged_points" => $exchangedPoints,
                "percentage" => $percentage,
                "log" => $arrResponse["recordset"]
                );


            return new JsonResponse(array(
                'message' => "listPayments",
                'data' => $data

            ));
        } catch (Exception $ex) {
            return new JsonResponse(array('message' => $ex->getMessage()), JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * List the rewards of the specified team.
     *
     * This calls the Bettercondos.info API to get a list of rewards for the user. [Right now it does nothing].
     *
     * @Rest\Get("/v1/rewards/{property_id}/{page_id}", name="listRewards")
     *
     * @SWG\Parameter( name="Content-Type", in="header", type="string", default="application/json" )
     * @SWG\Parameter( name="Authorization", in="header", required=true, type="string", default="Bearer TOKEN", description="Authorization" )
     *
     * @SWG\Parameter( name="property_id", in="path", type="integer", description="property ID." )
     * @SWG\Parameter( name="page_id", in="path", type="string", description="The requested pagination page." )
     *
     *
     * @SWG\Parameter( name="app_version", in="query", required=true, type="string", description="The version of the app." )
     * @SWG\Parameter( name="code_version", in="query", required=true, type="string", description="The version of the code." )
     * @SWG\Parameter( name="language", in="query", required=true, type="string", description="The language being used (either en or es)." )
     * @SWG\Parameter( name="time_offset", in="query", type="string", description="Time difference with respect to GMT time." )
     *
     * @SWG\Response(
     *     response=200,
     *     description="Returns the list of points for the user.",
     *     @SWG\Schema (
     *          @SWG\Property(
     *              property="data", type="array",
     *              @SWG\Items(
     *                  @SWG\Property( property="photoURL", type="string", description="photo url", example="//dummyimage.com/150x150/000/fff&text=AmazonGift" ),
     *                  @SWG\Property( property="id", type="integer", description="Reward ID", example="1" ),
     *                  @SWG\Property( property="name", type="string", description="Name of the reward", example="Reward" ),
     *                  @SWG\Property( property="description", type="string HTML", description="Description of the reward", example="<p>Reward Description</p>" ),
     *                  @SWG\Property( property="points", type="integer", description="Points to exchange", example="4500" ),
     *                  @SWG\Property( property="start_at", type="string", description="GMT datetime", example="2019-09-03T00:00:00+0000" ),
     *                  @SWG\Property( property="finish_at", type="string", description="GMT datetime", example="2019-09-03T00:00:00+0000" ),
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
     * @SWG\Tag(name="Gamification")
     */
    public function getRewardsAction(Request $request)
    {
        try {
            $this->initialise();
            $data = array();

            $pageID = trim($request->get('page_id'));
            $propertyID = trim($request->get('property_id'));

            $objProperty = $this->em->getRepository('BackendAdminBundle:Property')->findOneBy(array('enabled' => true, 'id' => $propertyID));
            $complexTeamID = intval($objProperty->getComplex()->getTeamCorrelative()) ;

            $token = $this->get('services')->getBCToken();
            $arrRewards = $this->callGamificationService( "GET", "teams/".$complexTeamID."/rewards?page=".$pageID, array() );


            if(isset($arrRewards["recordset"])){

                foreach ($arrRewards["recordset"] as $reward ){
                    $data[] = $reward;
                }
            }

            return new JsonResponse(array(
                'message' => "listRewards",
                'data' => $data,
                'metadata' => isset($arrRewards["metadata"]) ? $arrRewards["metadata"] : array()
            ));
        } catch (Exception $ex) {
            return new JsonResponse(array('message' => $ex->getMessage()), JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }



    /**
     * List the reward detail.
     *
     * This calls the Bettercondos.info API to get a list of rewards for the user. [Right now it does nothing].
     *
     * @Rest\Get("/v1/reward/{reward_id}", name="rewardDetail")
     *
     * @SWG\Parameter( name="Content-Type", in="header", type="string", default="application/json" )
     * @SWG\Parameter( name="Authorization", in="header", required=true, type="string", default="Bearer TOKEN", description="Authorization" )
     *
     * @SWG\Parameter( name="reward_id", in="path", type="integer", description="reward ID." )
     *
     *
     * @SWG\Parameter( name="app_version", in="query", required=true, type="string", description="The version of the app." )
     * @SWG\Parameter( name="code_version", in="query", required=true, type="string", description="The version of the code." )
     * @SWG\Parameter( name="language", in="query", required=true, type="string", description="The language being used (either en or es)." )
     * @SWG\Parameter( name="time_offset", in="query", type="string", description="Time difference with respect to GMT time." )
     *
     * @SWG\Response(
     *     response=200,
     *     description="Returns the list of points for the user.",
     *     @SWG\Schema (
     *          @SWG\Property(
     *              property="data", type="array",
     *              @SWG\Items(
     *                  @SWG\Property( property="photoURL", type="string", description="photo url", example="//dummyimage.com/150x150/000/fff&text=AmazonGift" ),
     *                  @SWG\Property( property="id", type="integer", description="Reward ID", example="1" ),
     *                  @SWG\Property( property="name", type="string", description="Name of the reward", example="Reward" ),
     *                  @SWG\Property( property="description", type="string HTML", description="Description of the reward", example="<p>Reward Description</p>" ),
     *                  @SWG\Property( property="points", type="integer", description="Points to exchange", example="4500" ),
     *                  @SWG\Property( property="start_at", type="string", description="GMT datetime", example="2019-09-03T00:00:00+0000" ),
     *                  @SWG\Property( property="finish_at", type="string", description="GMT datetime", example="2019-09-03T00:00:00+0000" ),
     *              ),
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
     * @SWG\Tag(name="Gamification")
     */
    public function getRewardDetailAction(Request $request)
    {
        try {
            $this->initialise();
            $data = array();

            $rewardID = intval($request->get('reward_id'));

            $token = $this->get('services')->getBCToken();
            $arrReward = $this->callGamificationService( "GET", "rewards/".$rewardID, array() );

            return new JsonResponse(array(
                'message' => "rewardDetail",
                'data' => $arrReward,
            ));
        } catch (Exception $ex) {
            return new JsonResponse(array('message' => $ex->getMessage()), JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }


    /**
     * Exchanges a rewards of the specified player.
     *
     * This calls the Bettercondos.info API to get a exchange a reward for the user.
     *
     * @Rest\Post("/v1/reward/{reward_id}/{player_id}", name="exchangeReward", )
     *
     * @SWG\Parameter( name="Content-Type", in="header", required=true, type="string", default="application/json" )
     * @SWG\Parameter( name="Authorization", in="header", required=true, type="string", default="Bearer TOKEN", description="Authorization" )
     *
     * @SWG\Parameter( name="reward_id", in="path", required=true, type="integer", description="The reward ID to exchange.", schema={} )
     * @SWG\Parameter( name="player_id", in="path", required=true, type="integer", description="The player ID.", schema={} )
     *
     * @SWG\Parameter( name="app_version", in="query", required=true, type="string", description="The version of the app." )
     * @SWG\Parameter( name="code_version", in="query", required=true, type="string", description="The version of the code." )
     * @SWG\Parameter( name="language", in="query", required=true, type="string", description="The language being used (either en or es)." )
     * @SWG\Parameter( name="time_offset", in="query", type="string", description="Time difference with respect to GMT time." )
     *
     * @SWG\Response(
     *     response=200,
     *     description="Returns the list of points for the user.",
     *     @SWG\Schema (
     *          @SWG\Property( property="message", type="string", example="" )
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
     * @SWG\Tag(name="Gamification")
     */
    public function postExchangeRewardAction(Request $request)
    {
        try {
            $this->initialise();

            $rewardID = intval($request->get('reward_id'));
            $playerID = intval($request->get('player_id'));

            $token = $this->get('services')->getBCToken();

            $body = [
                'player_id' => $playerID,
                'note' => "note"
            ];

            //$gamificationResponse = $this->callGamificationService( "POST", "rewards/".$rewardID, $body );

            $response = $this->get('services')->callBCSpace("POST", "rewards/".$rewardID, $body );


            return new JsonResponse(array(
                "data" => $response
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

    private function callGamificationService( $method, $service, $options) {

        $success = false;
        $attemps = 0;

        $response = false;

        while ( !$success && $attemps < 2 ) {
            $attemps++;
            try {
                $response = $this->get('services')->callBCSpace($method, $service, $options);
                //printf(" ------------------ This never happens ------------------ ");
                $success = true;
            } catch (\GuzzleHttp\Exception\ClientException $ex) {
                //printf(" ---= Error %s  =--- ", $ex->getMessage());
                $token = $this->get('services')->getBCToken();
                //printf("Generating new token with response %s \n", ($token)?"true":"false");
            }
        }

        return $response;
    }



}
