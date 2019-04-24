<?php

namespace Backend\AdminBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\Session\Session;
use FOS\RestBundle\Controller\Annotations\View;
use FOS\RestBundle\Request\ParamFetcher;
use FOS\RestBundle\Controller\Annotations\RequestParam;
use Symfony\Component\HttpFoundation\JsonResponse;


use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Nelmio\ApiDocBundle\Annotation\Model;
use Swagger\Annotations as SWG;



//entities
use Backend\AdminBundle\Entity\User;


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
		$this->serializer =  $this->get('jms_serializer');

	}

	/*

	/**
	 * @Rest\Post("/login_check", name="")
	 *
	 * @SWG\Response(
	 *     response=200,
	 *     description="User was logged in successfully"
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
	 * @SWG\Parameter(
	 *     name="username",
	 *     in="body",
	 *     type="string",
	 *     description="The username",
	 *     schema={}
	 * )
	 *
	 * @SWG\Parameter(
	 *     name="password",
	 *     in="body",
	 *     type="string",
	 *     description="The password",
	 *     schema={}
	 * )
	 *
	 * @SWG\Tag(name="User")
	 */
	/*
	public function getLoginCheckAction(Request $request) {

		$this->initialise();

		$username = $request->request->get('username');
		$password = $request->request->get('password');// hash('sha512', $data);

		try{

			$user = $this->em->getRepository('BackendAdminBundle:User')->findBy(array("username"=>$username, "enabled" => 1));

			if (!$user) {

				$code = 401;
				$error = true;
				$message = "No user found- Error";

				$response = [
					'code' => $code,
					'error' => $error,
					'data' => $message,
				];

				return new Response($this->serializer->serialize($response, "json"));

			}
			else {
				$user = $user[0];

				$factory = $this->get('security.encoder_factory');
				$salt = $user->getSalt();
				$encoder = $factory->getEncoder($user);


				if (!$encoder->isPasswordValid($user->getPassword(), $password, $salt)) {
					$code = 401;
					$error = true;
					$message = "Invalid user and password- Error";

					$response = [
						'code' => $code,
						'error' => $error,
						'data' => $message,
					];

					return new Response($this->serializer->serialize($response, "json"));
					//$response = array("response" => false, "result" => "Password inválido");
					//return new JsonResponse($response);
				} else {
					//VALID USER AND PASSWORD GENERATE TOKEN
					$jwtManager = $this->container->get('lexik_jwt_authentication.jwt_manager');
					return new JsonResponse(['token' => $jwtManager->create($user)]);
				}
			}
		}
		catch (Exception $ex) {
			$code = 500;
			$error = true;
			$message = "An error has occurred trying to retrieve the user - Error: {$ex->getMessage()}";
			$response = [
				'code' => $code,
				'error' => $error,
				'data' => $message,
			];

			return new Response($this->serializer->serialize($response, "json"));
		}


	}
	*/






	/**
	 * @Rest\Post("/clarissa_check", name="")
	 *
	 * @SWG\Response(
	 *     response=200,
	 *     description="ok"
	 * )
	 *
	 * @SWG\Response(
	 *     response=500,
	 *     description="error"
	 * )
	 *
	 * @SWG\Parameter(
	 *     name="username",
	 *     in="body",
	 *     type="string",
	 *     description="The username",
	 *     schema={}
	 * )

	 *
	 * @SWG\Tag(name="User")
	 */

	public function postClarissaCheckAction(Request $request) {

		$this->initialise();

		$username = $request->request->get('username');

			$code = 200;
			$error = false;

			$response = [
				'code' => $code,
				'error' => $error,
				'data' => $username,
			];

			return new Response($this->serializer->serialize($response, "json"));



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
	public function postRegisterAction(Request $request, UserPasswordEncoderInterface $encoder) {
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
 	

	public function myDebug($expression){
		
		print "<pre>";
		var_dump($expression);
		
		
	}






  
}

?>
