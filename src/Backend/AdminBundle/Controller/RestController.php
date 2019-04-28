<?php

namespace Backend\AdminBundle\Controller;

use Backend\AdminBundle\Entity\GeoCountry;
use Backend\AdminBundle\Entity\TermCondition;
use Backend\AdminBundle\Entity\User;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\FOSRestController;
use Swagger\Annotations as SWG;
use Nelmio\ApiDocBundle\Annotation\Model;
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
     *     response=500,
     *     description="Internal error.",
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
     *              @SWG\Items(ref=@Model(type=GeoCountry::class))
     *          ),
     *          @SWG\Property( property="message", type="string", example="" )
     *      )
     * )
     *
     * @SWG\Response(
     *     response=500,
     *     description="Internal error.",
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
            foreach($countries as $country) {
                $data[] = array( 'name' => $country->getName(), 'code' => $country->getCode(), 'lang' => $country->getLocale() );
            }
            $response = array('message' => "", 'data' => $data);

            return new JsonResponse($response);
        } catch (Exception $ex) {
            return new JsonResponse(array('message' => $ex->getMessage()), JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

}